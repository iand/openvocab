<?php
require_once 'rdfmodel.php';

/// Represents a change to a term's description
class TermChange extends RDFModel {
  function __construct() {
    $this->define_field('creator', DC_CREATOR, 'resource_scalar');
    $this->define_field('term', 'http://www.w3.org/2004/02/skos/core#note', 'inverse_scalar');
    $this->define_field('label', RDFS_LABEL);
    $this->define_field('reason', RDFS_COMMENT);
    $this->define_field('after', 'http://open.vocab.org/terms/afterGraph');
    $this->define_field('before', 'http://open.vocab.org/terms/beforeGraph');
    $this->define_field('date', 'http://purl.org/dc/elements/1.1/created', 'datetime_scalar');
    $this->define_field('openid', 'http://xmlns.com/foaf/0.1/openid', 'creator_resource');
  }

  function read_creator_resource($short_name, $property_uri) {
    $this->$short_name = $this->graph->get_first_resource($this->creator, $property_uri, NULL);
    if ($this->$short_name !== NULL ) $this->_has_data = TRUE;
  }

  function write_creator_resource($short_name, $property_uri) {
    $this->graph->remove_property_values($this->creator, $property_uri);
    $this->graph->add_resource_triple($this->creator, $property_uri, $this->$short_name);
  }

  function populate_graph($default_store) {
    $query = "
      prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
      prefix skos: <http://www.w3.org/2004/02/skos/core#>
      prefix dc: <http://purl.org/dc/elements/1.1/>
      describe ?term  <" . $this->_uri . "> {
        ?term skos:note <" . $this->_uri . "> .
      }";


    $query_uri = $default_store->uri . '/services/sparql?output=json&query=' . urlencode(trim($query));

    $this->graph->set_namespace_mapping(config_item('vocab_prefix'), config_item('vocab_uri'));
    $this->graph->read_data($query_uri);
/*
    printf("<h2>query</h2><pre>%s</pre>", htmlspecialchars($query));
    printf("<h2>query_uri</h2><pre>%s</pre>", htmlspecialchars($query_uri));
    printf("<h2>DATA</h2><pre>%s</pre>", htmlspecialchars($this->graph->to_turtle()));
*/
  }


  function validate() {
    $errors = array();
    if (empty($this->reason) ) {
      $errors[] = array('field' => 'reason', 'message' => "Please specify a reason for this change.");
    }
    return $errors;
  }
}

