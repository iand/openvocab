<?php
require_once 'constants.inc.php';
require_once MORIARTY_DIR . 'store.class.php';
require_once MORIARTY_DIR . 'changeset.class.php';
require_once MORIARTY_DIR . 'credentials.class.php';

class EditPropertyController extends NewPropertyController
{

  function get_uri() {
    $uri = isset($this->POST['uri']) ? $this->POST['uri'] : isset($this->GET['uri']) ? $this->GET['uri'] : '';
    return $uri;
  }

  function can_uri_be_edited($uri) {
    return preg_match('/^http\:\/\/open\.vocab\.org\/terms\/[a-z][a-zA-Z0-9-]+$/', $uri);
  }

  function GET() {
    $params = $this->init_params();
    $params['title'] = 'Edit Property';
    $params['mode'] = 'edit';
    $uri = $this->get_uri();
    $params['uri'] = $uri;

    if ( $this->can_uri_be_edited($uri) ) {
      $store = new Store(STORE_URI);
      $mb = $store->get_metabox();
      $response = $mb->describe($uri);
      if ($response->is_success()) {
        $desc = new SimpleGraph();
        $desc->from_rdfxml($response->body);
        $desc->remove_property_values($uri, 'http://schemas.talis.com/2005/dir/schema#etag');

        $params['label_en'] = $desc->get_first_literal($uri, RDFS_LABEL, '');
        $params['comment_en'] = $desc->get_first_literal($uri, RDFS_COMMENT, '');
        $params['description_en'] = $desc->get_first_literal($uri, SP_MARKDOWNDESCRIPTION, '');
        $params['plural_en'] = $desc->get_first_literal($uri, 'http://purl.org/net/vocab/2004/03/label#plural', '');


        if ( $desc->has_resource_triple($uri, RDF_TYPE, OWL_FUNCTIONALPROPERTY) ) {
          $params['functional'] = '1';
        }
        if ( $desc->has_resource_triple($uri, RDF_TYPE, OWL_INVERSEFUNCTIONALPROPERTY) ) {
          $params['inversefunctional'] = '1';
        }
        if ( $desc->has_resource_triple($uri, RDF_TYPE, OWL_SYMMETRICPROPERTY) ) {
          $params['symmetric'] = '1';
        }
        if ( $desc->has_resource_triple($uri, RDF_TYPE, OWL_TRANSITIVEPROPERTY) ) {
          $params['transitive'] = '1';
        }

        $params['sameas'] = array();
        foreach ($desc->get_resource_triple_values($uri, OWL_SAMEAS) as $obj) {
          $params['sameas'][] = $obj;
        }

        $params['inverse'] = array();
        foreach ($desc->get_resource_triple_values($uri, OWL_INVERSEOF) as $obj) {
          $params['inverse'][] = $obj;
        }

        $params['equivalent'] = array();
        foreach ($desc->get_resource_triple_values($uri, OWL_EQUIVALENTPROPERTY) as $obj) {
          $params['equivalent'][] = $obj;
        }

        $params['domain'] = array();
        foreach ($desc->get_resource_triple_values($uri, RDFS_DOMAIN) as $obj) {
          $params['domain'][] = $obj;
        }

        $params['range'] = array();
        foreach ($desc->get_resource_triple_values($uri, RDFS_RANGE) as $obj) {
          $params['range'][] = $obj;
        }

        $params['subprop'] = array();
        foreach ($desc->get_resource_triple_values($uri, RDFS_SUBPROPERTYOF) as $obj) {
          $params['subprop'][] = $obj;
        }
        return $this->render("templates/forms.newproperty.tpl.php", $params);
      }
      else {
        $params = Array('goal' => 'retrieve information about the term ' . $term_uri, 'response' => $response);
        return $this->render("templates/errors.communication.tpl.php", $params);
      }
    }

    return $this->render("templates/forms.invaliduri.tpl.php", $params);
  }

  function POST() {
    $params = $this->init_params();
    $params['title'] = 'Edit Property';
    $params['mode'] = 'edit';
    $uri = $this->get_uri();
    $params['uri'] = $uri;

    if ( $this->can_uri_be_edited($uri) ) {


      if (isset($this->POST['action']) ) {
        $params = $this->validate_data($params);

        if ( count($params['messages']) == 0) {
          $params = $this->update_description($params);
        }
      }
      return $this->render("templates/forms.newproperty.tpl.php", $params);
    }

    return $this->render("templates/forms.invaliduri.tpl.php", $params);

  
  }

}
?>