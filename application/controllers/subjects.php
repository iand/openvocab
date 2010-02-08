<?php
require_once 'resourcedescription.php';
require_once 'widgets.php';

class Subjects extends ResourceDescription {
  
  function augment_data() {
    $store = new Store($this->config->item('store_uri'));
    $ss = $store->get_sparql_service();
    
    $query = 'construct {?work <http://purl.org/dc/terms/subject> <' . $this->resource_uri . '>  ; <http://www.w3.org/2004/02/skos/core#prefLabel> ?label .} where {?work <http://purl.org/dc/terms/subject> <' . $this->resource_uri . '> ; <http://www.w3.org/2004/02/skos/core#prefLabel> ?label . } limit 31';
    $response = $ss->query($query, 'json');
    if ($response->is_success()) {
      $this->graph->add_json($response->body);
    }
    parent::augment_data();
  }
  
  function get_widgets() {
    $widgets = array();
    $widgets[] = new SubjectWidget();
    $widgets[] = new RelatedWorksWidget("Works with this subject", 'http://purl.org/dc/terms/subject');
    return $widgets;
  }
  
  
  
}
