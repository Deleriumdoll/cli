<?php

namespace Terminus\Dispatcher;

use Terminus\DocParser;
use Terminus\Dispatcher\CompositeCommand;

/**
 * Creates CompositeCommand or Subcommand instances.
 */
class CommandFactory {

  /**
   * Creates a new composite command or subcommand
   *
   * @param string           $name   Name of command to create
   * @param string           $class  Name of class command belongs to
   * @param CompositeCommand $parent Parent command
   * @return CompositeCommand
   */
  public static function create($name, $class, CompositeCommand $parent) {
    $reflection = new \ReflectionClass($class);

    if ($reflection->hasMethod('__invoke')) {
      $command = self::createSubcommand(
        $parent,
        $name,
        $reflection->name,
        $reflection->getMethod('__invoke')
      );
    } else {
      $command = self::createCompositeCommand($parent, $name, $reflection);
    }

    return $command;
  }

  /**
   * Creates a new composite command
   *
   * @param RootCommand      $parent     Parent command
   * @param string           $name       Name of command to create
   * @param \ReflectionClass $reflection Object with name of class to call
   * @return CompositeCommand
   */
  private static function createCompositeCommand(
    RootCommand $parent,
    $name,
    \ReflectionClass $reflection
  ) {
    $docparser = new DocParser($reflection->getDocComment());
    $container = new CompositeCommand($parent, $name, $docparser);

    foreach ($reflection->getMethods() as $method) {
      if (!self::isGoodMethod($method)) {
        continue;
      }
      $subcommand      = self::createSubcommand(
        $container,
        false,
        $reflection->name,
        $method
      );
      $subcommand_name = $subcommand->getName();
      $container->addSubcommand($subcommand_name, $subcommand);
    }

    return $container;
  }

  /**
   * Creates a new subcommand
   *
   * @param CompositeCommand  $parent     Parent command
   * @param string            $name       Name of command to create
   * @param string            $class_name Name of class command belongs to
   * @param \ReflectionMethod $method     Name of function to invoke in class
   * @return Subcommand
   */
  private static function createSubcommand(
    CompositeCommand $parent,
    $name,
    $class_name,
    \ReflectionMethod $method
  ) {
    $docparser = new DocParser($method->getDocComment());
    if (!$name) {
      $name = $docparser->getTag('subcommand');
      if (!$name) {
        $name = $method->name;
      }
    }
    $method_name  = $method->name;
    $when_invoked =
      function ($args, $assoc_args) use ($class_name, $method_name) {
        call_user_func(
          array(new $class_name, $method_name),
          $args,
          $assoc_args
        );
      };

    $subcommand = new Subcommand($parent, $name, $docparser, $when_invoked);
    return $subcommand;
  }

  /**
   * Decides if the given method is a good one
   *
   * @param \ReflectionMethod $method Method to be called
   * @return bool
   */
  private static function isGoodMethod(\ReflectionMethod $method) {
    $is_good_method = $method->isPublic()
      && !$method->isConstructor()
      && !$method->isStatic();
    return $is_good_method;
  }

}

