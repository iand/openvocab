<?php
require_once 'term.php';

/// Represents a term in a vocabulary
class TermWithChanges extends Term {
  var $offset = 0;
  var $max = 30;
  function __construct() {
    parent::__construct();
    $this->define_field('changes', '', 'changes_array');
  }
  function populate_graph($default_store) {
    $query = "
      prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
      prefix skos: <http://www.w3.org/2004/02/skos/core#>
      prefix dc: <http://purl.org/dc/elements/1.1/>
      prefix foaf: <http://xmlns.com/foaf/0.1/>
      construct {
         <" . $this->_uri . "> skos:note ?note .
        ?note rdfs:label ?notelabel ;
              rdfs:comment ?notecomment ;
              dc:creator ?creator ;
              dc:created ?notedate .
        ?creator foaf:openid ?openid .
      } where {
         <" . $this->_uri . "> skos:note ?note .
        ?note rdfs:label ?notelabel ;
              rdfs:comment ?notecomment ;
              dc:creator ?creator ;
              dc:created ?notedate .
        ?creator foaf:openid ?openid .

      } order by desc(?notedate) offset " . $this->offset . " limit " . $this->max;

    $query_uri = sprintf('%s/services/sparql?output=json&query=%s', $default_store->uri, urlencode($query));
    $describe_uri = sprintf('%s/meta?output=json&about=%s', $default_store->uri, urlencode($this->_uri));

    $this->graph->set_namespace_mapping(config_item('vocab_prefix'), config_item('vocab_uri'));
    $this->graph->update_prefix_mappings();
    $this->graph->read_data(array($describe_uri, $query_uri));
/*
    printf('<h2>describe_uri</h2><a href="%s">%s</a>', htmlspecialchars($describe_uri), htmlspecialchars($describe_uri));
    printf('<h2>query_uri</h2><a href="%s">%s</a>', htmlspecialchars($query_uri), htmlspecialchars($query_uri));
    printf("<h2>DATA</h2><pre>%s</pre>", htmlspecialchars($this->graph->to_turtle()));
*/
  }

  function read_changes_array($short_name, $property_uri) {

    $changes = array();
    $change_uris = $this->graph->get_resource_triple_values($this->_uri, 'http://www.w3.org/2004/02/skos/core#note');
    foreach ($change_uris as $change_uri) {
      $date = $this->graph->get_first_literal($change_uri, 'http://purl.org/dc/elements/1.1/created');
      $changes[$change_uri] = $date;
    }

    arsort($changes);

    $this->$short_name = array_keys($changes);
  }

}
