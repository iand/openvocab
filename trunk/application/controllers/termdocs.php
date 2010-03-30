<?php
require_once 'rdfcontroller.php';

class TermDocs extends RDFController {
  function load_model() {
    $this->load->model('TermWithChanges', 'model');
  }

  function load_view($data) {
    $this->load->view('term', $data);
  }

  function get_resource_uri($request_uri, $request_path) {
    if (preg_match('~^(.+)\.(html|rdf|ttl|json|atom)$~', $request_path, $m) ) {
      return $this->config->item('resource_base')  . str_replace('/' . $this->config->item('term_document_path') .'/', '/' .  $this->config->item('term_path') . '/', $m[1]);
    }
    else {
      return $this->config->item('resource_base')  . str_replace('/' .$this->config->item('term_document_path') . '/', '/' . $this->config->item('term_path') . '/', $request_path);
    }
  }

  function get_document_types() {
    return array(
          'rdf' => array('media_type' => 'application/rdf+xml', 'label' => 'RDF/XML'),
          'json' => array('media_type' => 'application/json', 'label' => 'JSON'),
          'ttl' => array('media_type' => 'text/turtle', 'label' => 'Turtle'),
          'atom' => array('media_type' => 'application/atom+xml', 'label' => 'Atom feed of changes'),
          );
  }

  function get_media_type_map() {
    return array('application/rdf+xml' => 'rdf', 'text/html' => 'html', 'application/atom+xml' => 'atom', 'application/xml' => 'atom', 'application/json'=>'json', 'text/turtle' => 'ttl', 'text/plain' => 'ttl');
  }


  function do_atom($id) {
    header("content-type:application/xml");
    $this->load->view('termdocs.atom.php', $this->get_view_data());
  }
}
