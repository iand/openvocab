<?php
require_once 'rdfmodel.php';

class ChangeList extends RDFModel {
  var $offset = 0;
  var $max = 30;

  function __construct() {
    $this->define_field('changes', '', 'changes_array');
  }

  function populate_graph($default_store) {
    $schema_uri = config_item('resource_base') . '/' . config_item('term_path');

    $query = "
      prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
      prefix skos: <http://www.w3.org/2004/02/skos/core#>
      prefix dc: <http://purl.org/dc/elements/1.1/>
      prefix foaf: <http://xmlns.com/foaf/0.1/>
      construct {
        ?term a ?type ;
              rdfs:label ?label ;
              skos:note ?note ;
              rdfs:isDefinedBy <" . $schema_uri . "> .
        ?note rdfs:label ?notelabel ;
              rdfs:comment ?notecomment ;
              dc:creator ?creator ;
              dc:created ?notedate .
        ?creator foaf:openid ?openid .
      } where {
        ?term a ?type ;
              rdfs:label ?label ;
              skos:note ?note ;
              rdfs:isDefinedBy <" . $schema_uri . "> .
        ?note rdfs:label ?notelabel ;
              rdfs:comment ?notecomment ;
              dc:creator ?creator ;
              dc:created ?notedate .
        ?creator foaf:openid ?openid .

      } order by desc(?notedate) offset " . $this->offset . " limit " . $this->max;

    $query_uri = $default_store->uri . '/services/sparql?output=json&query=' . urlencode($query);

    $this->graph->set_namespace_mapping(config_item('vocab_prefix'), config_item('vocab_uri'));
    $this->graph->read_data($query_uri);
  }

  function read_changes_array($short_name, $property_uri) {
    $terms = $this->graph->get_subjects_where_resource('http://www.w3.org/2000/01/rdf-schema#isDefinedBy', config_item('resource_base') . '/' . config_item('term_path'));

    $changes = array();
    foreach ($terms as $term) {
      $change_uris = $this->graph->get_resource_triple_values($term, 'http://www.w3.org/2004/02/skos/core#note');
      foreach ($change_uris as $change_uri) {
        $date = $this->graph->get_first_literal($change_uri, 'http://purl.org/dc/elements/1.1/created');
        $changes[$change_uri] = $date;
      }
    }

    arsort($changes);
    $this->$short_name = array_keys($changes);
  }



  function has_data() {
    return TRUE;
  }
}

