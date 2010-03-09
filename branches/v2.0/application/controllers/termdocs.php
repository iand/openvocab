<?php
require_once 'rdfcontroller.php';

class TermDocs extends RDFController {
  function load_model() {
    $this->load->model('Term', 'model');
  }

  function load_view($data) {
    $this->load->view('term', $data);
  }

  function get_resource_uri($request_uri, $request_path) {
    if (preg_match('~^(.+)\.(html|rdf|ttl|json)$~', $request_path, $m) ) {
      return $this->config->item('resource_base')  . str_replace('/' . $this->config->item('term_document_path') .'/', '/' .  $this->config->item('term_path') . '/', $m[1]);
    }
    else {
      return $this->config->item('resource_base')  . str_replace('/' .$this->config->item('term_document_path') . '/', '/' . $this->config->item('term_path') . '/', $request_path);
    }
  }
}
