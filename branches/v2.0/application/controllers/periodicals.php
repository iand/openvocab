<?php
require_once 'resourcedescription.php';
require_once 'widgets.php';

class Periodicals extends ResourceDescription {
  function get_resource_uri($base_uri) {
    return str_replace('semanticlibrary.org', 'bl.dataincubator.org', $base_uri);
  }
  
  function read_data() {
    $store = new Store('http://api.talis.com/stores/bl-dev1');
    $response = $store->describe($this->resource_uri, 'lcbd', 'json');
    if ($response->is_success()) {
      $this->graph->add_json($response->body);
    }

  }


  function augment_data() {
    $authors = $this->graph->get_resource_triple_values($this->resource_uri, 'http://purl.org/dc/terms/creator');
    $parts = $this->graph->get_resource_triple_values($this->resource_uri, 'http://purl.org/dc/terms/hasPart');
    $containers = $this->graph->get_resource_triple_values($this->resource_uri, 'http://purl.org/dc/terms/isPartOf');
    $store = new Store('http://api.talis.com/stores/bl-dev1');
    $response = $store->describe(array_merge($containers, $parts, $authors), 'cbd', 'json');
    if ($response->is_success()) {
      $this->graph->add_json($response->body);
    }
  }
  
  function get_widgets() {
    $widgets = array();
    $widgets[] = new PeriodicalWidget();
    return $widgets;
  }  
  
  
}
