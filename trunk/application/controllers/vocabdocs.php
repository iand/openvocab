<?php
require_once 'rdfcontroller.php';

class VocabDocs extends RDFController {
  function load_model() {
    $this->load->model('VocabDescription', 'model');
  }
  function load_view($data) {
    $this->load->view('vocab', $data);
  }
}

