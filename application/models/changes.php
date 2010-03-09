<?php
require_once 'rdfmodel.php';

class ChangeList extends RDFModel {
  function __construct() {
    $this->define_field('label', RDFS_LABEL);
    $this->define_field('comment', RDFS_COMMENT);
    $this->define_field('classes', '', 'classes_array');
    $this->define_field('properties', '', 'properties_array');
  }

  function populate_graph($default_store) {
    $schema_uri = config_item('resource_base') . '/' . config_item('term_path');

    $query = "
      prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
      construct {
        ?term a ?type ;
              rdfs:label ?label ;
              rdfs:comment ?comment ;
              rdfs:isDefinedBy <" . $schema_uri . "> .
      } where {
        ?term a ?type ;
              rdfs:label ?label ;
              rdfs:isDefinedBy <" . $schema_uri . "> .
        optional {?term rdfs:comment ?comment }

      }";

    $query_uri = $default_store->uri . '/services/sparql?output=json&query=' . urlencode($query);

    $this->graph->read_data($query_uri);
  }

  function read_classes_array($short_name, $property_uri) {
    $this->$short_name = $this->graph->get_subjects_of_type(OWL_CLASS);
  }

  function read_properties_array($short_name, $property_uri) {
    $this->$short_name = $this->graph->get_subjects_of_type(RDF_PROPERTY);
  }


  function has_data() {
    return TRUE;
  }
}

