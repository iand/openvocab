<?php
require_once 'rdfmodel.php';

/// Represents a term in a vocabulary
class Term extends RDFModel {
  function __construct() {
    $this->add_field('label', RDFS_LABEL);
    $this->add_field('comment', RDFS_COMMENT);
    $this->add_field('is_defined_by', RDFS_ISDEFINEDBY, 'l', 'uri');
    $this->add_field('status', 'http://www.w3.org/2003/06/sw-vocab-status/ns#term_status');
    $this->add_field('userdocs', 'http://www.w3.org/2003/06/sw-vocab-status/ns#userdocs', 'l', 'uri');
    $this->add_field('plural', 'http://purl.org/net/vocab/2004/03/label#plural');
    $this->add_field('is_property', RDF_TYPE, 'b', 'uri', RDF_PROPERTY);
    $this->add_field('is_class', RDF_TYPE, 'b', 'uri', OWL_CLASS);
    $this->add_field('is_symmetrical', RDF_TYPE, 'b', 'uri', OWL_SYMMETRICPROPERTY);
    $this->add_field('is_transitive', RDF_TYPE, 'b', 'uri', OWL_TRANSITIVEPROPERTY);
    $this->add_field('is_functional', RDF_TYPE, 'b', 'uri', OWL_FUNCTIONALPROPERTY);
    $this->add_field('is_inverse_functional', RDF_TYPE, 'b', 'uri', OWL_INVERSEFUNCTIONALPROPERTY);
    $this->add_field('inverses', OWL_INVERSEOF, 'a', 'uri');
    $this->add_field('domains', RDFS_DOMAIN, 'a', 'uri');
    $this->add_field('ranges', RDFS_RANGE, 'a', 'uri');
    $this->add_field('superproperties', RDFS_SUBPROPERTYOF, 'a', 'uri');
    $this->add_field('equivalentproperties', OWL_EQUIVALENTPROPERTY, 'a', 'uri');
    $this->add_field('equivalentclasses', OWL_EQUIVALENTCLASS, 'a', 'uri');
    $this->add_field('superclasses', RDFS_SUBCLASSOF, 'a', 'uri');
    $this->add_field('disjoints', OWL_DISJOINTWITH, 'a', 'uri');
  }

  function is_property() {
    return ($this->graph->has_resource_triple($this->_uri, RDF_TYPE, RDF_PROPERTY));
  }
}

