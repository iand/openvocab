<?php
require_once 'rdfcontroller.php';

class Changes extends RDFController {
  function load_model() {
    $this->load->model('ChangeList', 'model');
  }
  function load_view($data) {
    $this->load->view('changes', $data);
  }
}
