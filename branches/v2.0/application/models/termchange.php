<?php
require_once 'rdfmodel.php';

/// Represents a change to a term's description
class TermChange extends RDFModel {
  function __construct() {
    $this->define_field('creator', DC_CREATOR, 'resource_scalar');
    $this->define_field('term', 'http://www.w3.org/2004/02/skos/core#changeNote', 'inverse_scalar');
    $this->define_field('label', RDFS_LABEL);
    $this->define_field('reason', RDFS_COMMENT);
    $this->define_field('date', DC_DATE);
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
        ?term skos:changeNote <" . $this->_uri . "> .
      }";


    $query_uri = $default_store->uri . '/services/sparql?output=json&query=' . urlencode($query);

    $this->graph->read_data($query_uri);
  }


  function validate() {
    $errors = array();
    if (empty($this->reason) ) {
      $errors[] = array('field' => 'reason', 'message' => "Please specify a reason for this change.");
    }
    return $errors;
  }
}

