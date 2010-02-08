<?php
require_once 'rdfmodel.php';

/// Represents the description of a term in a vocabulary
class TermDescription extends RDFModel {
  function __construct() {
    $this->add_field('primarytopic', FOAF_PRIMARYTOPIC, 'l', 'uri');
  }

}

