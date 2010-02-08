<?php
require_once 'resourcedescription.php';
require_once 'widgets.php';

class People extends ResourceDescription {
  
  function augment_data() {
    parent::augment_data();
    $store = new Store($this->config->item('store_uri'));
    $ss = $store->get_sparql_service();
  
 /*   
    $query = 'construct {?work <http://purl.org/dc/terms/creator> <' . $this->resource_uri . '>  ; <http://www.w3.org/2004/02/skos/core#prefLabel> ?label .} 
              where {?work <http://purl.org/dc/terms/creator> <' . $this->resource_uri . '> ; <http://www.w3.org/2004/02/skos/core#prefLabel> ?label . } limit 31';
    $response = $ss->query($query, 'json');
    if ($response->is_success()) {
      $this->graph->add_json($response->body);
    }

    $query = 'construct {?work <http://purl.org/dc/terms/contributor> <' . $this->resource_uri . '>  ; <http://www.w3.org/2004/02/skos/core#prefLabel> ?label .} 
              where {?work <http://purl.org/dc/terms/contributor> <' . $this->resource_uri . '> ; <http://www.w3.org/2004/02/skos/core#prefLabel> ?label . } limit 31';
    $response = $ss->query($query, 'json');
    if ($response->is_success()) {
      $this->graph->add_json($response->body);
    }
*/

    $dbpedia_people = array();
    $dbpedia_people = array_merge($dbpedia_people, $this->graph->get_resource_triple_values($this->resource_uri, 'http://dbpedia.org/property/influenced'));
    $dbpedia_people = array_merge($dbpedia_people, $this->graph->get_resource_triple_values($this->resource_uri, 'http://dbpedia.org/property/influences'));



    if (count($dbpedia_people) > 0) {
      $query = 'select ?s ?l ?same where ';
      if (count($dbpedia_people) > 1) $query .= '{ ';

      for ($i = 0; $i < count($dbpedia_people); $i++) {
        if ($i > 0) {
          $query .= ' union ';
        }
        $query .= '{?s <http://www.w3.org/2002/07/owl#sameAs> <' . $dbpedia_people[$i] . '>; <http://www.w3.org/2004/02/skos/core#prefLabel> ?l; <http://www.w3.org/2002/07/owl#sameAs> ?same .}';
      }
      if (count($dbpedia_people) > 1) $query .= ' }';

      $results = $ss->select_to_array($query);
      for ($i = 0; $i < count($results); $i++) {
        if (in_array($results[$i]['same']['value'], $dbpedia_people)) {
          $this->graph->replace_resource($results[$i]['same']['value'], $results[$i]['s']['value']);
          $this->graph->add_literal_triple($results[$i]['s']['value'], 'http://www.w3.org/2004/02/skos/core#prefLabel', $results[$i]['l']['value']);
        }
      }
    }

  }
  


  function get_widgets() {
    $widgets = array();
    $widgets[] = new PersonWidget();
    $widgets[] = new TopSubjectsChartWidget();
    $widgets[] = new ClientSideListWidget('Works created by this author', $id='works', $this->resource_path . '/works.json');
    $widgets[] = new ClientSideListWidget('Works contributed to', $id='contributions', $this->resource_path . '/contributions.json');
//    $widgets[] = new RelatedWorksWidget("Works created by this author", 'http://purl.org/dc/terms/creator');
//    $widgets[] = new RelatedWorksWidget("Works contributed to", 'http://purl.org/dc/terms/contributor');
    return $widgets;
  }

  
  
  
}
