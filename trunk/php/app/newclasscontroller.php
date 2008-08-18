<?php
require_once 'constants.inc.php';
require_once MORIARTY_DIR . 'store.class.php';
require_once MORIARTY_DIR . 'changeset.class.php';
require_once MORIARTY_DIR . 'credentials.class.php';
require_once OV_LIB_DIR . 'validate/Validate.php';

class NewClassController extends k_Controller
{
  
  function init_params() {
    $params = Array('mode' => 'new',
                    'uri' => '',
                    'title' => 'New Class', 
                    'slug' => '',
                    'label_en' => '',
                    'plural_en' => '',
                    'comment_en' => '',
                    'description_en' => '',
                    'disjoint'=>array(), 
                    'equivalent'=>array(), 
                    'subclass'=>array(), 
                    'messages' => array(), 
                    'response' => '', 
                    'reason' => 'Created', 
                    );

    return $params;
  }

  function GET() {
    $params = $this->init_params();
    if (isset($this->GET['subClassOf'])) {
      $params['subclass_1'] = $this->GET['subClassOf'];
    }
    return $this->render("templates/forms.newclass.tpl.php", $params);
  }


  function POST() {
    $params = $this->init_params();


    if (isset($this->POST['action']) ) {
      $params = $this->validate_data($params);

      if ( count($params['messages']) == 0) {
        $params = $this->update_description($params);
      }
    }
    return $this->render("templates/forms.newclass.tpl.php", $params);
  
  }


  function validate_data($params) {
    $validate = new Validate();

    $params['slug'] = isset($this->POST['slug']) ? $this->POST['slug'] : '';
    $params['uri'] = isset($this->POST['uri']) ? $this->POST['uri'] : '';
    $params['label_en'] = isset($this->POST['label_en']) ? $this->POST['label_en'] : '';
    $params['plural_en'] = isset($this->POST['plural_en']) ? $this->POST['plural_en'] : '';
    $params['comment_en'] = isset($this->POST['comment_en']) ? $this->POST['comment_en'] : '';
    $params['description_en'] = isset($this->POST['description_en']) ? $this->POST['description_en'] : '';
    $params['reason'] = isset($this->POST['reason']) ? $this->POST['reason'] : '';

    if ( ! $params['uri'] ) {
     
      if ( ! isset($this->POST['slug']) || empty($this->POST['slug']) ) {
        $params['messages'][]  = "You must specify the full URI of the property";
      }    
      elseif ( ! preg_match('/^[A-Z][a-zA-Z0-9-]+$/', $this->POST['slug']) ) {
        $params['messages'][]  = "The name of the class must be start with an uppercase letter and contain only letters, numbers and/or hyphens";
      }
    }

    if ( ! isset($this->POST['label_en']) || empty($this->POST['label_en']) ) {
      $params['messages'][]  = "You must supply an English label for the class";
    }
    
    if ( ! isset($this->POST['reason']) || empty($this->POST['reason']) ) {
      $params['messages'][]  = "You must supply a reason for this change";
    }    

    foreach ($this->POST as $key => $value) {
      if ( !empty($value) && preg_match('/^(subclass|equivalent|disjoint)_\d+$/', $key)) {
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
      $uri = 'http://open.vocab.org/terms/' . $params['slug'];
    }

    $store_ro = new Store(STORE_URI, null, true);
    $mb = $store_ro->get_metabox();
    $response = $mb->describe($uri);
    $params['response'] = $response;

    if ($response->is_success()) {    
      $orig_desc = $response->body;
      //$orig_desc->remove_property_values($uri, 'http://schemas.talis.com/2005/dir/schema#etag');

      $store = new Store(STORE_URI, new Credentials(USER_NAME, USER_PWD));
      $res = new SimpleGraph();
      $res->add_resource_triple($uri, RDF_TYPE, OWL_CLASS);
      $res->add_literal_triple($uri, RDFS_LABEL, $params['label_en'], 'en');
      $res->add_literal_triple($uri, 'http://www.w3.org/2003/06/sw-vocab-status/ns#term_status', 'unstable');
      $res->add_resource_triple($uri, 'http://www.w3.org/2003/06/sw-vocab-status/ns#userdocs', $uri . '/html');
      
      foreach ($params as $key => $value) {
        if ( !empty($value) ) {
          if (preg_match('/^subclass_\d+$/', $key)) { 
            $res->add_resource_triple($uri, RDFS_SUBCLASSOF, $value);
          }
          elseif (preg_match('/^equivalent_\d+$/', $key)) { 
            $res->add_resource_triple($uri, OWL_EQUIVALENTCLASS, $value);
          }
          elseif (preg_match('/^disjoint_\d+$/', $key)) { 
            $res->add_resource_triple($uri, OWL_DISJOINTWITH, $value);
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

}
?>
