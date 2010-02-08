<?php
require_once 'rdfmodel.php';

class VocabDescription extends RDFModel {  
  function __construct() {
    $this->add_field('label', RDFS_LABEL);
    $this->add_field('comment', RDFS_COMMENT);
  }

  function read_data() {
    parent::read_data();
    
    $dt = new DataTable(config_item('store_uri'));
    $dt->map(RDFS_LABEL, 'label');
    $dt->map(RDFS_COMMENT, 'comment');
    $dt->map(RDFS_ISDEFINEDBY, 'isdefinedby');
    $dt->map(RDF_TYPE, 'type');

    $dt->select('label');
    $dt->optional('comment');
    $dt->where_uri('isdefinedby', config_item('resource_base') . '/' . config_item('term_path'));
    $dt->where_uri('type', OWL_CLASS);
    $this->classes = $dt->get()->result();
    
    $dt = new DataTable(config_item('store_uri'));
    $dt->map(RDFS_LABEL, 'label');
    $dt->map(RDFS_COMMENT, 'comment');
    $dt->map(RDFS_ISDEFINEDBY, 'isdefinedby');
    $dt->map(RDF_TYPE, 'type');

    $dt->select('label');
    $dt->optional('comment');
    $dt->where_uri('isdefinedby', config_item('resource_base') . '/' . config_item('term_path'));
    $dt->where_uri('type', RDF_PROPERTY);
    $this->properties = $dt->get()->result();    
  }
  
  function has_data() {
    return TRUE;
  }  
}

