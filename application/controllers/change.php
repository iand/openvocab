<?php
require_once 'rdfcontroller.php';

class Change extends RDFController {
  function load_model() {
    $this->load->model('TermChange', 'model');
  }
  function load_view($data) {
    $this->load->view('change', $data);
  }

}
