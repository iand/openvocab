<?php
require_once 'rdfmodel.php';

/// Represents a term in a vocabulary
class Term extends RDFModel {
  function __construct() {
    $this->define_field('label', RDFS_LABEL);
    $this->define_field('comment', RDFS_COMMENT);
    $this->define_field('status', 'http://www.w3.org/2003/06/sw-vocab-status/ns#term_status');
    $this->define_field('plural', 'http://purl.org/net/vocab/2004/03/label#plural');
    $this->define_field('is_defined_by', RDFS_ISDEFINEDBY, 'resource_scalar');
    $this->define_field('userdocs', 'http://www.w3.org/2003/06/sw-vocab-status/ns#userdocs', 'resource_scalar');
    $this->define_field('is_property', RDF_TYPE, 'is_property');
    $this->define_field('is_class', RDF_TYPE, 'is_class');
    $this->define_field('is_symmetrical', RDF_TYPE,'is_symmetrical');
    $this->define_field('is_transitive', RDF_TYPE, 'is_transitive');
    $this->define_field('is_functional', RDF_TYPE, 'is_functional');
    $this->define_field('is_inverse_functional', RDF_TYPE, 'is_inverse_functional');
    $this->define_field('inverses', OWL_INVERSEOF, 'resource_array');
    $this->define_field('domains', RDFS_DOMAIN, 'resource_array');
    $this->define_field('ranges', RDFS_RANGE, 'resource_array');
    $this->define_field('superproperties', RDFS_SUBPROPERTYOF, 'resource_array');
    $this->define_field('equivalentproperties', OWL_EQUIVALENTPROPERTY, 'resource_array');
    $this->define_field('equivalentclasses', OWL_EQUIVALENTCLASS, 'resource_array');
    $this->define_field('superclasses', RDFS_SUBCLASSOF, 'resource_array');
    $this->define_field('disjoints', OWL_DISJOINTWITH, 'resource_array');
  }

  function read_boolean($short_name, $property_uri, $truth_value) {
    $this->$short_name = $this->graph->has_resource_triple($this->_uri, $property_uri, $truth_value);
  }

  function write_boolean($short_name, $property_uri, $truth_value) {
    if ($this->graph->has_resource_triple($this->_uri, $property_uri, $truth_value)) {
      if (! $this->$short_name) {
        $this->graph->remove_resource_triple($this->_uri, $property_uri, $truth_value);
      }

    }
    else {
      if ($this->$short_name) {
        $this->graph->add_resource_triple($this->_uri, $property_uri, $truth_value);
      }
    }
  }

  function init_boolean($short_name) {
    $this->$short_name = FALSE;
  }



  function read_is_property($short_name, $property_uri) {
    $this->read_boolean($short_name, $property_uri, RDF_PROPERTY);
  }
  function write_is_property($short_name, $property_uri) {
    $this->write_boolean($short_name, $property_uri, RDF_PROPERTY);
  }
  function init_is_property($short_name, $property_uri) {
    $this->init_boolean($short_name);
  }


  function read_is_class($short_name, $property_uri) {
    $this->read_boolean($short_name, $property_uri, OWL_CLASS);
  }
  function write_is_class($short_name, $property_uri) {
    $this->write_boolean($short_name, $property_uri, OWL_CLASS);
  }
  function init_is_class($short_name, $property_uri) {
    $this->init_boolean($short_name);
  }


  function read_is_symmetrical($short_name, $property_uri) {
    $this->read_boolean($short_name, $property_uri, OWL_SYMMETRICPROPERTY);
  }
  function write_is_symmetrical($short_name, $property_uri) {
    $this->write_boolean($short_name, $property_uri, OWL_SYMMETRICPROPERTY);
  }
  function init_is_symmetrical($short_name, $property_uri) {
    $this->init_boolean($short_name);
  }


  function read_is_transitive($short_name, $property_uri) {
    $this->read_boolean($short_name, $property_uri, OWL_TRANSITIVEPROPERTY);
  }
  function write_is_transitive($short_name, $property_uri) {
    $this->write_boolean($short_name, $property_uri, OWL_TRANSITIVEPROPERTY);
  }
  function init_is_transitive($short_name, $property_uri) {
    $this->init_boolean($short_name);
  }


  function read_is_functional($short_name, $property_uri) {
    $this->read_boolean($short_name, $property_uri, OWL_FUNCTIONALPROPERTY);
  }
  function write_is_functional($short_name, $property_uri) {
    $this->write_boolean($short_name, $property_uri, OWL_FUNCTIONALPROPERTY);
  }
  function init_is_functional($short_name, $property_uri) {
    $this->init_boolean($short_name);
  }


  function read_is_inverse_functional($short_name, $property_uri) {
    $this->read_boolean($short_name, $property_uri, OWL_INVERSEFUNCTIONALPROPERTY);
  }
  function write_is_inverse_functional($short_name, $property_uri) {
    $this->write_boolean($short_name, $property_uri, OWL_INVERSEFUNCTIONALPROPERTY);
  }
  function init_is_inverse_functional($short_name, $property_uri) {
    $this->init_boolean($short_name);
  }



  function is_property() {
    return ($this->graph->has_resource_triple($this->_uri, RDF_TYPE, RDF_PROPERTY));
  }

  function validate() {
    $errors = array();

    $uri = $this->get_uri();
    if (empty($uri) ) {
      $errors[] = array('field' => 'uri', 'message' => "Please specify a URI for this term.");
    }

    if (empty($this->label) ) {
      $errors[] = array('field' => 'label', 'message' => "Please specify a label.");
    }
    if (empty($this->comment) ) {
      $errors[] = array('field' => 'comment', 'message' => "Please specify a comment.");
    }

    foreach ($this->inverses as $uri) {
      if (! $this->uri_check($uri)) {
        $errors[] = array('field' => 'inverses', 'message' => $uri . " is not a valid URI.");
      }
    }

    foreach ($this->domains as $uri) {
      if (! $this->uri_check($uri)) {
        $errors[] = array('field' => 'domains', 'message' => $uri . " is not a valid URI.");
      }
    }

    foreach ($this->ranges as $uri) {
      if (! $this->uri_check($uri)) {
        $errors[] = array('field' => 'ranges', 'message' => $uri . " is not a valid URI.");
      }
    }

    foreach ($this->superproperties as $uri) {
      if (! $this->uri_check($uri)) {
        $errors[] = array('field' => 'superproperties', 'message' => $uri . " is not a valid URI.");
      }
    }

    foreach ($this->equivalentproperties as $uri) {
      if (! $this->uri_check($uri)) {
        $errors[] = array('field' => 'equivalentproperties', 'message' => $uri . " is not a valid URI.");
      }
    }

    foreach ($this->equivalentclasses as $uri) {
      if (! $this->uri_check($uri)) {
        $errors[] = array('field' => 'equivalentclasses', 'message' => $uri . " is not a valid URI.");
      }
    }

    foreach ($this->superclasses as $uri) {
      if (! $this->uri_check($uri)) {
        $errors[] = array('field' => 'superclasses', 'message' => $uri . " is not a valid URI.");
      }
    }

    foreach ($this->disjoints as $uri) {
      if (! $this->uri_check($uri)) {
        $errors[] = array('field' => 'disjoints', 'message' => $uri . " is not a valid URI.");
      }
    }

    return $errors;
  }


  // Validation of URI fields
  function uri_check($url) {
    $ret = TRUE;

    $url = substr($url,-1) == "/" ? substr($url,0,-1) : $url;
    if ( !$url || $url=="" ) return TRUE;
    if ( !( $parts = @parse_url( $url ) ) ) $ret = FALSE;
    else {
        if ( !isset($parts['scheme']) ) $ret = FALSE;
        else if ( !isset($parts['host']) ) $ret = FALSE;
        else if ( $parts['scheme'] != "http"
          && $parts['scheme'] != "https"
          && $parts['scheme'] != "info"
          && $parts['scheme'] != "tag"
          && strtolower($parts['scheme']) != "urn"
          ) $ret = FALSE;
        else if ( !eregi( "^[0-9a-z]([-.]?[0-9a-z])*.[a-z]{2,4}$", $parts['host'], $regs ) ) $ret = FALSE;
        else if (isset($parts['user']) && !eregi( "^([0-9a-z-]|[_])*$", $parts['user'], $regs ) ) $ret = FALSE;
        else if (isset($parts['pass']) && !eregi( "^([0-9a-z-]|[_])*$", $parts['pass'], $regs ) ) $ret = FALSE;
        else if (isset($parts['path']) && !eregi( "^[0-9a-z/_.@~-]*$", $parts['path'], $regs ) ) $ret = FALSE;
        else if (isset($parts['query']) && !eregi( "^[0-9a-z?&=#,]*$", $parts['query'], $regs ) ) $ret = FALSE;
    }

    if ($ret) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }



}

