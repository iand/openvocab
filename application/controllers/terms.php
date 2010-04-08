<?php
require_once MORIARTY_DIR . 'store.class.php';

class Terms extends Controller {
  var $graph;


  function __construct() {
    if (count($_GET)) show_404('page');

    parent::Controller();

    $this_host = $this->input->server("HTTP_HOST");
    $path = $this->uri->uri_string();

    if (preg_match('~^(.+)\.(html|rdf|ttl|json|atom)$~', $path, $m) ) {
      $path = $m[1];
    }


    $this->request_uri = 'http://' . $this_host . $path;

    $this->resource_uri = $this->config->item('resource_base')  . $path;
    $this->doc_uri = 'http://' . $this_host . str_replace('/' . $this->config->item('term_path') . '/', '/' . $this->config->item('term_document_path') . '/', $path);

    $this->load->model('Term', 'term');

    $this->term->set_uri($this->resource_uri);
    $this->term->load_from_network();

    if (!$this->term->has_data() ) {
      show_404('page');
    }
  }

  function do_303($id) {
    $this->load->helper('url');
    $canonical_uris = $this->term->graph->get_literal_triple_values($this->resource_uri, 'http://open.vocab.org/terms/canonicalUri');
    if ( count($canonical_uris) == 1 ) {
      $page_uri = $canonical_uris[0];
    }
    else {
      $page_uri = $this->doc_uri;
    }
    redirect( $page_uri, 'location', 303);
  }

  function do_html_redirect($format) {
    $this->load->helper('url');
    $page_uri = $this->doc_uri . '.html';
    redirect( $page_uri, 'location', 301);
  }

  function do_rdf_redirect($format) {
    $this->load->helper('url');
    $page_uri = $this->doc_uri . '.rdf';
    redirect( $page_uri, 'location', 301);
  }


  function do_ttl_redirect($format) {
    $this->load->helper('url');
    $page_uri = $this->doc_uri . '.ttl';
    redirect( $page_uri, 'location', 301);
  }

  function do_json_redirect($format) {
    $this->load->helper('url');
    $page_uri = $this->doc_uri . '.json';
    redirect( $page_uri, 'location', 301);
  }

}
