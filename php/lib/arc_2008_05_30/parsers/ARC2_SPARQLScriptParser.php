<?php
/*
homepage: http://arc.semsol.org/
license:  http://arc.semsol.org/license

class:    ARC2 SPARQLScript Parser (SPARQL+ + functions)
author:   Benjamin Nowack
version:  2008-04-10 
*/

ARC2::inc('ARC2_SPARQLPlusParser');

class ARC2_SPARQLScriptParser extends ARC2_SPARQLPlusParser {

  function __construct($a = '', &$caller) {
    parent::__construct($a, $caller);
  }
  
  function ARC2_SPARQLScriptParser($a = '', &$caller) {
    $this->__construct($a, $caller);
  }

  function __init() {
    parent::__init();
  }

  /*  */

  function parse($v, $src = '') {
    $this->prefixes = array(
      'rdf:' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
      'rdfs:' => 'http://www.w3.org/2000/01/rdf-schema#',
      'owl:' => 'http://www.w3.org/2002/07/owl#',
      'xsd:' => 'http://www.w3.org/2001/XMLSchema#',
    );
    $this->base = $src ? $this->calcBase($src) : ARC2::getScriptURI();
    $this->blocks = array();
    do {
      $proceed = 0;
      $this->unparsed_code = trim($v);
      $this->r = array('base' => '', 'vars' => array(), 'prefixes' => $this->prefixes);
      /* PrefixDecl */
      if (!$proceed) {
        while ((list($r, $v) = $this->xPrefixDecl($v)) && $r) {
          $this->prefixes[$r['prefix']] = $r['iri'];
          $proceed = 1;
        }
      }
      /* EndpointDecl */
      if (!$proceed) {
        if ((list($r, $v) = $this->xEndpointDecl($v)) && $r) {
          $this->blocks[] = array(
            'type' => 'endpoint_decl',
            'infos' => $r
          );
          $proceed = 1;
        }
      }
      /* Assignment */
      if (!$proceed) {
        if ((list($r, $v) = $this->xAssignment($v)) && $r) {
          $this->blocks[] = array(
            'type' => 'assignment',
            'infos' => $r
          );
          $proceed = 1;
        }
      }
      /* Query */
      if (!$proceed) {
        if ((list($r, $rest) = $this->xQuery($v)) && $r) {
          $q = trim(str_replace($rest, '', $v));
          $v = $rest;
          $this->blocks[] = array_merge($this->r, array(
            'type' => 'query',
            'query_type' => $r['type'],
            'query' => $q,
            //'prefixes' => $this->prefixes,
            'base' => $this->base,
            //'infos' => $r
          ));
          $proceed = 1;
        }
      }
    } while ($proceed);
    if ($this->unparsed_code && !$this->getErrors()) {
      $rest = preg_replace('/[\x0a|\x0d]/i', ' ', substr($this->unparsed_code, 0, 30));
      $msg = trim($rest) ? 'Could not properly handle "' . $rest . '"' : 'Syntax Error';
      $this->addError($msg);
    }
  }
  
  function getScriptBlocks() {
    return $this->v('blocks', array());
  }


  /* s2 */
  
  function xEndpointDecl($v) {
    if ($r = $this->x("ENDPOINT\s+", $v)) {
      if ((list($r, $sub_v) = $this->xIRI_REF($r[1])) && $r) {
        $r = $this->calcUri($r, $this->base);
        if ($sub_r = $this->x('\.', $sub_v)) {
          $sub_v = $sub_r[1];
        }
        return array($r, $sub_v);
      }
    }
    return array(0, $v);
  }
  
  /* s3 */
  
  function xAssignment($v) {
    /* Var */
    list($r, $sub_v) = $this->xVar($v);
    if (!$r) return array(0, $v);
    $var = $r;
    /* := | = */
    if (!$sub_r = $this->x("\:?\=", $sub_v)) return array(0, $v);
    $sub_v = $sub_r[1];
    /* try String */
    list($r, $sub_v) = $this->xString($sub_v);
    if ($r) return array(array('var' => $var, 'sub_type' => 'string', 'string' => $r), $sub_v);
    /* try query */
    $this->r = array('base' => '', 'vars' => array(), 'prefixes' => $this->prefixes);
    list($r, $rest) = $this->xQuery($sub_v);
    if (!$r) return array(0, $v);
    $q = trim(str_replace($rest, '', $sub_v));
    return array(
      array(
        'var' => $var,
        'sub_type' => 'query',
        'query' => array_merge($this->r, array(
          'type' => 'query',
          'query_type' => $r['type'],
          'query' => $q,
          'base' => $this->base,
        )),
      ),
      $rest
    );
  }
  

}  
