<?php
require_once 'resourcedescription.php';
require_once 'widgets.php';

class Publishers extends ResourceDescription {
  
  function augment_data() {
    $store = new Store($this->config->item('store_uri'));
    $ss = $store->get_sparql_service();
    
    $query = 'describe ?edition where {?edition <http://purl.org/dc/terms/publisher> <' . $this->resource_uri . '> . } limit 31';
    $response = $ss->query($query, 'json');
    if ($response->is_success()) {
      $this->graph->add_json($response->body);
    }
    parent::augment_data();
  }
  
  function get_widgets() {
    $this->load->helper('editions');     
    $widgets = array();
    $widgets[] = new PublisherWidget();
    $widgets[] = new RelatedEditionsWidget("Editions by this publisher", 'http://purl.org/dc/terms/publisher');
    $widgets[] = new Widget('Other Information');
    return $widgets;
  }
  
  
}
