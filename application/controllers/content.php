<?php

class Content extends Controller {

  function __construct() { 
    parent::Controller();
    $this->load->library('session');
  }
    
  function create() {
    $data = array();
    $this->load->view('create', $data);
  }

}
?>
