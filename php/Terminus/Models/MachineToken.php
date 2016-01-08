<?php

namespace Terminus\Models;

class MachineToken extends TerminusModel {

  /**
   * Deletes machine token
   *
   * @return array
   */
  public function delete() {
    $response = $this->request->simpleRequest(
      'users/' . $this->user->id . '/refresh_tokens/' . $this->get('id'),
      array('method' => 'delete')
    );
    return $response;
  }

}
