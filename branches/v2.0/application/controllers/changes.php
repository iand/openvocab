<?php
require_once 'rdfcontroller.php';

class Changes extends RDFController {
  function load_model() {
    $this->load->model('ChangeList', 'model');
  }
  function load_view($data) {
    $this->load->view('changes', $data);
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
    $this->load->view('changes.atom.php', $this->get_view_data());
  }
}
