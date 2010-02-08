<?php
require_once 'resourcedescription.php';
require_once 'widgets.php';

class Collections extends ResourceDescription {
  
  function augment_data() {
    $silkworm_list  = array();
    $sw_store = new Store('http://api.talis.com/stores/silkworm-dev');
    $ss = $sw_store->get_sparql_service();
    $sameas_list = $this->graph->get_resource_triple_values($this->resource_uri, 'http://www.w3.org/2002/07/owl#sameAs');
    foreach ($sameas_list as $sameas) {
      if (preg_match('~^http://directory.talis.com/res/~', $sameas)) {
        
        $query = 'describe <' . $sameas . '> ?loc where { <'. $sameas . '> <http://schemas.talis.com/2005/dir/schema#isAccessedVia> ?loc . }';
        
        $response = $ss->query($query, 'json');
        if ($response->is_success()) {
          $munged_uri = str_replace('/', '\\/', $this->resource_uri);
          $munged_silkworm_uri = str_replace('/', '\\/', $sameas);
          $body = str_replace($munged_silkworm_uri, $munged_uri, $response->body);
          $this->graph->add_json($body);
        }
        
        
      }
    }

    
  }  
  
  function get_widgets() {
    $widgets = array();
    $widgets[] = new CollectionWidget();
    return $widgets;
  }  
  
  
}
