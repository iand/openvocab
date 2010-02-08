<?php

class Create extends Controller {

  function __construct() {
    parent::Controller();
    $this->load->helper('url');
    $this->load->library('session');
  }

    // Index
  function index() {
    $data = array();
    $this->load->view('create', $data);
  }
}
?>
