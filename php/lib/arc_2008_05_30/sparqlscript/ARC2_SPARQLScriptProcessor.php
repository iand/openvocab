<?php
/*
homepage: ARC or plugin homepage
license:  http://arc.semsol.org/license

class:    ARC2 SPARQLScript Processor
author:   
version:  2008-05-20
*/

ARC2::inc('Class');

class ARC2_SPARQLScriptProcessor extends ARC2_Class {

  function __construct($a = '', &$caller) {
    parent::__construct($a, $caller);
  }
  
  function ARC2_SPARQLScriptProcessor ($a = '', &$caller) {
    $this->__construct($a, $caller);
  }

  function __init() {
    parent::__init();
    $this->env = array(
      'endpoint' => ''
    );
  }

  /*  */
  
  function processScript($s) {
    $r = array();
    $parser = $this->getParser();
    $parser->parse($s);
    $blocks = $parser->getScriptBlocks();
    if ($parser->getErrors()) return 0;
    foreach ($blocks as $block) {
      $sub_r = $this->processBlock($block);
      if ($this->getErrors()) return 0;
      $r[] = $sub_r;
    }
    return $r;
  }

  /*  */
  
  function getParser() {
    ARC2::inc('SPARQLScriptParser');
    return new ARC2_SPARQLScriptParser($this->a, $this);
  }
  
  /*  */

  function processBlock($block) {
    $type = $block['type'];
    $m = 'process' . $this->camelCase($type) . 'Block';
    if (method_exists($this, $m)) {
      return $this->$m($block);
    }
    return $this->addError('Unsupported block type "' . $type . '"');
  }

  /*  */
  
  function processEndpointDeclBlock($block) {
    $this->env['endpoint'] = $block['infos'];
    return $this->env;
  }

  /*  */

  function processQueryBlock($block) {
    $ep_uri = $this->env['endpoint'];
    /* q */
    $q = 'BASE <' . $block['base']. '>';
    foreach ($block['prefixes'] as $k => $v) {
      $q .= "\n" . 'PREFIX ' . $k . ' <' . $v . '>';
    }
    $q .= "\n" . $block['query'];
    /* local store */
    if ((!$ep_uri || $ep_uri == ARC2::getScriptURI()) && ($this->v('sparqlscript_default_endpoint', '', $this->a) == 'local')) {
      $store = ARC2::getStore($this->a);/* @@todo error checking */
      return $store->query($q);
    }
    elseif ($ep_uri) {
      ARC2::inc('RemoteStore');
      $conf = array_merge($this->a, array('remote_store_endpoint' => $ep_uri));
      $store =& new ARC2_RemoteStore($conf, $this);
      return $store->query($q, 'raw', $ep_uri);
    }
    else {
      return $this->addError("no store");
    }
  }

  /*  */

  function processAssignmentBlock($block) {
    $infos = $block['infos'];
    $sub_type = $infos['sub_type'];
    $m = 'process' . $this->camelCase($sub_type) . 'AssignmentBlock';
    if (method_exists($this, $m)) {
      return $this->$m($block);
    }
    return $this->addError('Unsupported block type "' . $sub_type . ' assignment"');
  }

  function processQueryAssignmentBlock($block) {
    $this->env['vars'][$block['infos']['var']['value']] = $this->processQueryBlock($block['infos']['query']);
  }
  
  
  /*  */

  
  
}