<?php

class Logout extends Controller {

  function __construct() { 
    parent::Controller();
    $this->load->library('session');
    $this->load->helper('url');
    
  }
    
    // Index
  function index() {
    $newdata = array(
      'logged_in' => FALSE
    );

    $this->session->set_userdata($newdata);
    redirect( 'docs', 'location', 302);
  }

}
?>
