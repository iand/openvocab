<?php

class Welcome extends Controller {

  function __construct() {
    parent::Controller();
    $this->load->helper('url');
    $this->load->library('session');
  }

    // Index
  function index() {
    $data = array();
    $this->load->view('welcome', $data);
  }
}
?>
