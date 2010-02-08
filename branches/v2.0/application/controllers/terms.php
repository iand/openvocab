<?php
require_once MORIARTY_DIR . 'store.class.php';

class Terms extends Controller {
  var $graph;
  

  function __construct() {
    if (count($_GET)) show_404('page');

    parent::Controller();

    $this_host = $this->input->server("HTTP_HOST");
    $path = $this->uri->uri_string();
    $this->request_uri = 'http://' . $this_host . $path;

    $this->resource_uri = $this->config->item('resource_base')  . $path;
    $this->doc_uri = 'http://' . $this_host . str_replace('/' . $this->config->item('term_path') . '/', '/' . $this->config->item('term_document_path') . '/', $path);
    
    
    $this->graph = new SimpleGraph();
    $this->read_data();

    if (!$this->has_description() ) {
      show_404('page');
    }
  }
   
  function has_description() {
    return $this->graph->has_triples_about($this->resource_uri);
  }
      
  function read_data() {
    $store = new Store($this->config->item('store_uri'));
    $response = $store->describe($this->resource_uri, 'cbd', 'json');
    if ($response->is_success()) {
      $this->graph->add_json($response->body);
    }
  }
   

  function do_303($id) {
    $this->load->helper('url');
    $canonical_uris = $this->graph->get_literal_triple_values($this->resource_uri, 'http://open.vocab.org/terms/canonicalUri');
    if ( count($canonical_uris) == 1 ) {
      $page_uri = $canonical_uris[0];
    }
    else {
      $page_uri = $this->doc_uri;
    }
    redirect( $page_uri, 'location', 303);
  }

}
