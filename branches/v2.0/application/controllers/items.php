<?php
require_once 'resourcedescription.php';
require_once 'widgets.php';

class Items extends ResourceDescription {
  var $holdings_results_uri;
  
  function augment_data() {
    $tocs = $this->graph->get_resource_triple_values($this->resource_uri, 'http://purl.org/dc/terms/tableOfContents');
    $store = new Store($this->config->item('store_uri'));
    $response = $store->describe($tocs, 'cbd', 'json');
    if ($response->is_success()) {
      $this->graph->add_json($response->body);
    }


    $isbn13 = $this->graph->get_first_literal($this->resource_uri, 'http://purl.org/ontology/bibo/isbn13');
    if ( $isbn13 ) {
      $query = "id:" .  $isbn13;
      $holdings = new Store('http://api.talis.com/stores/holdings');
      $cb = $holdings->get_contentbox();
      $this->holdings_results_uri = $cb->make_search_uri($query, 50, 0, 'name');
      $response = $cb->search($query, 50, 0, 'name');
      if ($response->is_success()) {
        $this->graph->add_rdfxml($response->body);
      }
    }
    parent::augment_data();
    
  }
  
  function get_widgets() {
    $this->load->helper('language');     
    $this->load->helper('editions');     
    $widgets = array();
    $widgets[] = new EditionWidget();
    $widgets[] = new TocWidget();
    $widgets[] = new HoldingsWidget($this->holdings_results_uri);
    return $widgets;
  }
  
  
}
