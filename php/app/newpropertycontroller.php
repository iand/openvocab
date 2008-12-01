<?php
require_once 'constants.inc.php';
require_once 'utility.func.php';
require_once MORIARTY_DIR . 'store.class.php';
require_once MORIARTY_DIR . 'changeset.class.php';
require_once MORIARTY_DIR . 'credentials.class.php';
require_once OV_LIB_DIR . 'validate/Validate.php';

class NewPropertyController extends k_Controller
{
  
  function init_params() {
    $params = Array('mode' => 'new',
                    'uri' => '',
                    'title' => 'New Property', 
                    'slug' => '',
                    'functional' => '',
                    'inversefunctional' => '',
                    'symmetric' => '',
                    'transitive' => '',
                    'label_en' => '',
                    'plural_en' => '',
                    'comment_en' => '',
                    'description_en' => '',
                    'range' => array(), 
                    'domain'=>array(), 
                    'equivalent'=>array(), 
                    'subprop'=>array(), 
                    'inverse'=>array(), 
                    'messages' => array(), 
                    'response' => '', 
                    'reason' => '', 
                    );

    return $params;
  }

  function GET() {
    $params = $this->init_params();
    if (isset($this->GET['subPropertyOf'])) {
      $params['subprop_1'] = $this->GET['subPropertyOf'];
    }
    
    
$terms_query = "prefix cs: <http://purl.org/vocab/changeset/schema#>
  prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
  prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
  prefix owl: <http://www.w3.org/2002/07/owl#>
  construct {
  ?p a rdf:Property .
  ?c a owl:Class .
  }
  where {
   {
    ?p a rdf:Property .
       
   }
   union 
   {
    ?c a owl:Class .
   }
  }";    

    $store = new Store(STORE_URI);
    $sparql = $store->get_sparql_service();
    $terms = $sparql->construct_to_simple_graph($terms_query);  
    $properties = array();
    $classes = array();
    $index = $terms->get_index();
    foreach ($index as $s=>$p_list) {
      foreach ($p_list as $p => $v_list) {
        if ($p == RDF_TYPE) {
          foreach ($v_list as $v_info) {
            if ($v_info['type'] == 'uri') {
              if ($v_info['value'] == RDF_PROPERTY) {
                $properties[] = array('label' => make_qname($s), 'uri' => $s);                   
              }
              else if ($v_info['value'] == OWL_CLASS) {
                $classes[] = array('label' => make_qname($s), 'uri' => $s);                   
              }
            }             
          }
        }
      }
    }  
    
    $params['properties'] = $properties;
    $params['classes'] = $classes;
    return $this->render("templates/forms.newproperty.tpl.php", $params);
  }

  function validate_data($params) {
    $validate = new Validate();

    $params['slug'] = isset($this->POST['slug']) ? $this->POST['slug'] : '';
    $params['uri'] = isset($this->POST['uri']) ? $this->POST['uri'] : '';
    $params['label_en'] = isset($this->POST['label_en']) ? $this->POST['label_en'] : '';
    $params['plural_en'] = isset($this->POST['plural_en']) ? $this->POST['plural_en'] : '';
    $params['comment_en'] = isset($this->POST['comment_en']) ? $this->POST['comment_en'] : '';
    $params['description_en'] = isset($this->POST['description_en']) ? $this->POST['description_en'] : '';
    $params['functional'] = isset($this->POST['functional']) ? $this->POST['functional'] : '';
    $params['inversefunctional'] = isset($this->POST['inversefunctional']) ? $this->POST['inversefunctional'] : '';
    $params['symmetrical'] = isset($this->POST['symmetrical']) ? $this->POST['symmetrical'] : '';
    $params['transitive'] = isset($this->POST['transitive']) ? $this->POST['transitive'] : '';
    $params['reason'] = isset($this->POST['reason']) ? $this->POST['reason'] : '';


    if ( ! $params['uri'] ) {
     
      if ( ! isset($this->POST['slug']) || empty($this->POST['slug']) ) {
        $params['messages'][]  = "You must specify the full URI of the property";
      }    
      elseif ( ! preg_match('/^[a-z][a-zA-Z0-9-]+$/', $this->POST['slug']) ) {
        $params['messages'][]  = "The name of the property must be start with a lowercase letter and contain only letters, numbers and/or hyphens";
      }
    }

    if ( ! isset($this->POST['label_en']) || empty($this->POST['label_en']) ) {
      $params['messages'][]  = "You must supply an English label for the property";
    }

    if ( ! isset($this->POST['reason']) || empty($this->POST['reason']) ) {
      $params['messages'][]  = "You must supply a reason for this change";
    }

    foreach ($this->POST as $key => $value) {
      if ( !empty($value) && preg_match('/^(subprop|domain|range|equivalent|inverse)_\d+$/', $key)) {
        $params[$key] = $value;
  
        if ( ! $validate->uri($value)) {
          $params['messages'][]  = "$value is not a valid URI";
        }
      }
    }


    return $params;
  }

  function update_description($params) {
    if ( isset( $params['uri'] ) && !empty( $params['uri']) ) {
      $uri = $params['uri'];
    }
    else {
      $uri = VOCAB_NS . $params['slug'];
    }

    $store_ro = new Store(STORE_URI, null, true);
    //$mb = $store_ro->get_metabox();
    //$response = $mb->describe($uri);

    $sp = $store_ro->get_sparql_service();
    $response = $sp->describe($uri);
    
    $params['response'] = $response;

    if ($response->is_success()) {    
      $orig_desc = $response->body;
      //$orig_desc->remove_property_values($uri, 'http://schemas.talis.com/2005/dir/schema#etag');

      $res = new SimpleGraph();
      $res->add_resource_triple($uri, RDF_TYPE, RDF_PROPERTY);
      $res->add_literal_triple($uri, RDFS_LABEL, $params['label_en'], 'en');
      $res->add_literal_triple($uri, 'http://www.w3.org/2003/06/sw-vocab-status/ns#term_status', 'unstable');
      $res->add_resource_triple($uri, 'http://www.w3.org/2003/06/sw-vocab-status/ns#userdocs', $uri . '.html');
      $res->add_resource_triple($uri, RDFS_ISDEFINEDBY, VOCAB_SCHEMA);
      
      if ( $params['functional'] ) {
        $res->add_resource_triple($uri, RDF_TYPE, OWL_FUNCTIONALPROPERTY);
      }
      if ( $params['inversefunctional'] ) {
        $res->add_resource_triple($uri, RDF_TYPE, OWL_INVERSEFUNCTIONALPROPERTY);
      }
      if ( $params['symmetrical'] ) {
        $res->add_resource_triple($uri, RDF_TYPE, OWL_SYMMETRICPROPERTY);
      }
      if ( $params['transitive'] ) {
        $res->add_resource_triple($uri, RDF_TYPE, OWL_TRANSITIVEPROPERTY);
      }

      foreach ($params as $key => $value) {
        if ( !empty($value) ) {
          if (preg_match('/^subprop_\d+$/', $key)) { 
            $res->add_resource_triple($uri, RDFS_SUBPROPERTYOF, $value);
          }
          elseif (preg_match('/^domain_\d+$/', $key)) { 
            $res->add_resource_triple($uri, RDFS_DOMAIN, $value);
          }
          elseif (preg_match('/^range_\d+$/', $key)) { 
            $res->add_resource_triple($uri, RDFS_RANGE, $value);
          }
          elseif (preg_match('/^inverse_\d+$/', $key)) { 
            $res->add_resource_triple($uri, OWL_INVERSEOF, $value);
          }
          elseif (preg_match('/^equivalent_\d+$/', $key)) { 
            $res->add_resource_triple($uri, OWL_EQUIVALENTPROPERTY, $value);
          }
          elseif (preg_match('/^sameas_\d+$/', $key)) { 
            $res->add_resource_triple($uri, OWL_SAMEAS, $value);
          }
        }
      }



      if ( isset($this->POST['comment_en']) && ! empty($params['comment_en']) ) {
        $res->add_literal_triple($uri, RDFS_COMMENT, $params['comment_en'], 'en');
      }
      if ( isset($this->POST['description_en']) && ! empty($params['description_en']) ) {
        $res->add_literal_triple($uri, SP_MARKDOWNDESCRIPTION, $params['description_en'], 'en');
      }
      if ( isset($this->POST['plural_en']) && ! empty($params['plural_en']) ) {
        $res->add_literal_triple($uri, 'http://purl.org/net/vocab/2004/03/label#plural', $params['plural_en'], 'en');
      }

      $cs = new ChangeSet( array('before_rdfxml' => $orig_desc, 'after_rdfxml' => $res->to_rdfxml(), 'subjectOfChange' => $uri, 'creatorName' => get_user(), 'changeReason' => $params['reason'], 'createdDate' => gmdate(DATE_W3C, time())) );

      if ( $cs->has_changes() ) {

        
        $store = new Store(STORE_URI, new Credentials(USER_NAME, USER_PWD));
        $mb = $store->get_metabox();
        $response = $mb->apply_versioned_changeset($cs);
        $params['response'] = $response;
        if ( $response->is_success()) {
          throw new k_http_Redirect( remote_to_local($uri) );
        }

      }
      else {
        throw new k_http_Redirect( remote_to_local($uri) );
      }      
    }
    return $params;
  }

  function POST() {
    $params = $this->init_params();

    if (isset($this->POST['action']) ) {
      
      $params = $this->validate_data($params);

      if ( count($params['messages']) == 0) {
        $params = $this->update_description($params);
      }
    }
    return $this->render("templates/forms.newproperty.tpl.php", $params);
  
  }
}
?>
