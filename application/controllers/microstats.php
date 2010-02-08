<?php
require_once 'resourcedescription.php';
require_once 'widgets.php';

class MicroStats extends Controller {
  function __construct() {
    parent::Controller();
  }
    
  function person_subjects_top($id) {
    $resource_uri = $this->config->item('resource_base') . '/people/' . $id;
   
    $query = 'prefix dc: <http://purl.org/dc/terms/> prefix skos: <http://www.w3.org/2004/02/skos/core#> select ?l where {?w dc:creator <';
    $query .= $resource_uri;
    $query .= '>; dc:subject ?s. ?s skos:prefLabel ?l . }';

    $store = new Store($this->config->item('store_uri'));
    $ss = $store->get_sparql_service();
    
    
    $results = $ss->select_to_array($query);
    
    $labels = array();
    $ranks = array();
    foreach($results as $result) {
      $label = $result['l']['value'];
      $key = preg_replace('~[^a-z0-9]~', '', strtolower($label));

      if (!array_key_exists($key, $labels)) {
        $labels[$key] = $label;
      }
      
      if (array_key_exists($key, $ranks)) {
        $ranks[$key]++;
      }
      else  {
        $ranks[$key] = 1;
      }
    }

    arsort($ranks, SORT_NUMERIC);
    
    $num = 5;
    $results = array();
    foreach ($ranks as $key => $count) {
      if ($num-- <= 0) break;
      $results[] = array('label' => $labels[$key], 'count' => $count);
    }
    
    header("Content-type:application/json");
    echo(json_encode($results));

  }


  function person_activity_all($id) {
    $resource_uri = $this->config->item('resource_base') . '/people/' . $id;

    $query = 'prefix dc: <http://purl.org/dc/terms/> prefix skos: <http://www.w3.org/2004/02/skos/core#> select ?date ?label where {?w dc:creator <';
    $query .= $resource_uri;
    $query .= '> ; dc:hasVersion ?v . ?v dc:issued ?date ; skos:prefLabel ?label .}';

    $store = new Store($this->config->item('store_uri'));
    $ss = $store->get_sparql_service();
    
    
    $results = $ss->select_to_array($query);
    
    $labels = array();
    $years = array();
    foreach($results as $result) {
      if ($result['date']['type'] == 'literal') {
        $date = $result['date']['value'];
        if ( preg_match('~\b(\d\d\d\d)\b~', $date, $m)) {
          if ($m[1] != '0000') {
            $year = $m[1];
            if (array_key_exists($year, $years)) {
              $years[$year]++;
            }
            else  {
              $years[$year] = 1;
            }
          }
        }
      }
      
      
    }

    ksort($years, SORT_NUMERIC);
    header("Content-type:application/json");
    echo(json_encode($years));

  }


  

  function backlinks($store_uri, $resource_uri, $p, $format) {
    $query = 'prefix skos: <http://www.w3.org/2004/02/skos/core#>  construct {?res <' . $p . '> <' . $resource_uri . '> ; skos:prefLabel ?label . } where {?res <' . $p. '> <' . $resource_uri . '> ; skos:prefLabel ?label}';

    $store = new Store($this->config->item('store_uri'));
    $ss = $store->get_sparql_service();
    
    $response = $ss->query($query, 'json');
    if ($response->is_success()) {
      if ($format === 'json') {
        header("Content-type:application/json");
        echo($response->body);
      }
      else if ($format === 'rdf') {
        header("Content-type:application/rdf+xml");
        echo($response->body);
      }
    }
    else {
        header("Content-type:text/plain");
        echo $response->to_string();
    }
  }


  function backlinks_creator_json($id) {
    $resource_uri = $this->config->item('resource_base') . '/people/' . $id;
    $this->backlinks($this->config->item('store_uri'), $resource_uri, 'http://purl.org/dc/terms/creator', 'json');
  }

  function backlinks_contributor_json($id) {
    $resource_uri = $this->config->item('resource_base') . '/people/' . $id;
    $this->backlinks($this->config->item('store_uri'), $resource_uri, 'http://purl.org/dc/terms/contributor', 'json');
  }

}
