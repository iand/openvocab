<?php
require_once MORIARTY_DIR . 'store.class.php';

class Vocab extends Controller {
  var $graph;
  

  function __construct() {
    if (count($_GET)) show_404('page');

    parent::Controller();

    $this_host = $this->input->server("HTTP_HOST");
    $path = $this->uri->uri_string();
    $this->request_uri = 'http://' . $this_host . $path;

    $this->resource_uri = $this->config->item('resource_base')  . $path;
    $this->doc_uri = 'http://' . $this_host . str_replace('/' . $this->config->item('term_path'), '/' . $this->config->item('term_document_path'), $path);
  }

  function do_303($id) {
    $this->load->helper('url');
    redirect( $this->doc_uri, 'location', 303);
  }

}
