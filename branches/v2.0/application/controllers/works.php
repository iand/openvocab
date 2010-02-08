<?php
require_once 'resourcedescription.php';
require_once 'widgets.php';

class Works extends ResourceDescription {
  function augment_data() {
    $contributorlists = $this->graph->get_resource_triple_values($this->resource_uri, 'http://purl.org/ontology/bibo/contributorList');
    $authorlists = $this->graph->get_resource_triple_values($this->resource_uri, 'http://purl.org/ontology/bibo/authorList');
    $contributors = $this->graph->get_resource_triple_values($this->resource_uri, 'http://purl.org/dc/terms/contributor');
    $authors = $this->graph->get_resource_triple_values($this->resource_uri, 'http://purl.org/dc/terms/creator');
    $editions = $this->graph->get_resource_triple_values($this->resource_uri, 'http://purl.org/dc/terms/hasVersion');
    $store = new Store($this->config->item('store_uri'));
    $response = $store->describe(array_merge($contributors, $authors, $editions, $authorlists, $contributorlists), 'cbd', 'json');
    if ($response->is_success()) {
      $this->graph->add_json($response->body);
    }
    else {
      echo '<pre>' . htmlspecialchars($response->to_string()) . '</pre>';
    }
    parent::augment_data();
  }


  function get_widgets() {
    $this->load->helper('editions');     
    $widgets = array();
    $widgets[] = new WorkWidget();
    $widgets[] = new BagWidget('Authors', 'http://purl.org/ontology/bibo/authorList', 'http://purl.org/dc/terms/creator');
    $widgets[] = new BagWidget('Contributors', 'http://purl.org/ontology/bibo/contributorList', 'http://purl.org/dc/terms/contributor');
    $widgets[] = new EditionTableWidget();
    return $widgets;
  }
  

}
