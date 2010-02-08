<?php
require_once 'rdfcontroller.php';

class TermDocs extends RDFController {
  function load_model() {
    $this->load->model('TermDescription', 'model');
  }

  function load_view($data) {
    $this->load->view('term', $data);
  }
}
