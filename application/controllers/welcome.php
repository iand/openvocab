<?php
include MORIARTY_DIR.'httprequestfactory.class.php';

class Welcome extends Controller {

  function __construct() {
    parent::Controller();
    $this->load->helper('url');
    $this->load->library('session');
    $this->load->helper('local_link');
  }

    // Index
  function index() {
    $data = array();

    $request_factory = new HttpRequestFactory();
    $request_factory->read_from_cache(FALSE);

    $store_uri = $this->config->item('store_uri');
    $schema_uri = $this->config->item('resource_base') . '/' . $this->config->item('term_path');

/*
prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
prefix owl: <http://www.w3.org/2002/07/owl#>
*/
    // COUNT TYPES OF EACH TERM
    $type_count_query = sprintf('select ?type (count(?s) as ?count) where {?s a ?type; <http://www.w3.org/2000/01/rdf-schema#isDefinedBy> <%s> . } group by ?type', $schema_uri);
    $type_count_uri = sprintf('%s/services/sparql?output=json&query=%s', $store_uri, urlencode($type_count_query));
    $type_count_request = $request_factory->make( 'GET', $type_count_uri );
    $type_count_request->set_accept('application/json');
    $type_count_request->execute_async();

// add more queries here
// 5 MOST ACTIVE USERS

// 5 RECENT CHANGES

    $recent_changes_query = "
      prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
      prefix skos: <http://www.w3.org/2004/02/skos/core#>
      prefix dc: <http://purl.org/dc/elements/1.1/>
      prefix foaf: <http://xmlns.com/foaf/0.1/>
      select ?term ?note ?label ?notelabel ?notedate ?openid {
        ?term rdfs:label ?label ;
              skos:note ?note ;
              rdfs:isDefinedBy <" . $schema_uri . "> .
        ?note rdfs:label ?notelabel ;
              dc:created ?notedate ;
              dc:creator ?creator .
        ?creator foaf:openid ?openid .
      } order by desc(?notedate) limit 5";

    $recent_changes_uri = sprintf('%s/services/sparql?output=json&query=%s', $store_uri, urlencode(trim($recent_changes_query)));
    $recent_changes_request = $request_factory->make( 'GET', $recent_changes_uri );
    $recent_changes_request->set_accept('application/json');
    $recent_changes_request->execute_async();

//printf('<pre><a href="%s">query</a></pre>', htmlspecialchars($recent_changes_uri ));

    // NEWEST TERMS
    $new_term_query = sprintf('prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> select ?term ?date ?label where {?term <http://purl.org/dc/elements/1.1/created> ?date; rdfs:label ?label ; rdfs:isDefinedBy <%s> . } order by desc(?data) limit 5', $schema_uri);
    $new_term_uri = sprintf('%s/services/sparql?output=json&query=%s', $store_uri, urlencode($new_term_query));
    $new_term_request = $request_factory->make( 'GET', $new_term_uri );
    $new_term_request->set_accept('application/json');
    $new_term_request->execute_async();




    $data = array();

    $type_count_response = $type_count_request->get_async_response();

    if ($type_count_response->is_success()) {
      $type_count_data = json_decode($type_count_response->body, true);
      if (isset($type_count_data['results']['bindings'])) {
        foreach ($type_count_data['results']['bindings'] as $binding) {
          if (isset($binding['type']['value'])) {
            if ($binding['type']['value'] == OWL_CLASS) {
              $data['class_count'] = $binding['count']['value'];
            }
            else if ($binding['type']['value'] == RDF_PROPERTY) {
              $data['property_count'] = $binding['count']['value'];
            }
            else if ($binding['type']['value'] == 'http://www.w3.org/2002/07/owl#FunctionalProperty') {
              $data['functional_count'] = $binding['count']['value'];
            }
            else if ($binding['type']['value'] == 'http://www.w3.org/2002/07/owl#InverseFunctionalProperty') {
              $data['inverse_functional_count'] = $binding['count']['value'];
            }
            else if ($binding['type']['value'] == 'http://www.w3.org/2002/07/owl#SymmetricProperty') {
              $data['symmetric_count'] = $binding['count']['value'];
            }
            else if ($binding['type']['value'] == 'http://www.w3.org/2002/07/owl#TransitiveProperty') {
              $data['transitive_count'] = $binding['count']['value'];
            }
          }
        }
      }

    }

    $new_term_response = $new_term_request->get_async_response();

    if ($new_term_response->is_success()) {
      $new_term_data = json_decode($new_term_response->body, true);
      if (isset($new_term_data['results']['bindings'])) {
        $data['new_terms'] = array();
        foreach ($new_term_data['results']['bindings'] as $binding) {
          $data['new_terms'][] = array('uri' => $binding['term']['value'],'date' => $binding['date']['value'],  'label' => $binding['label']['value']);
        }
      }

    }

    $recent_changes_response = $recent_changes_request->get_async_response();

    if ($recent_changes_response->is_success()) {
      $recent_changes_data = json_decode($recent_changes_response->body, true);
      if (isset($recent_changes_data['results']['bindings'])) {
        $data['recent_changes'] = array();
        foreach ($recent_changes_data['results']['bindings'] as $binding) {
          $data['recent_changes'][] = array('uri' => $binding['term']['value'],'label' => $binding['label']['value'], 'note' => $binding['note']['value'], 'notelabel' => $binding['notelabel']['value'], 'notedate' => $binding['notedate']['value'], 'openid' => $binding['openid']['value']);
        }
      }
    }

    $this->load->library('jquery');
    $this->jquery->setExternalJquery('/js/jquery-1.3.2.min.js');
    $this->jquery->addExternalScript('/js/pretty.js');
    $this->jquery->setJqDocumentReady(true);
    $js_ready = '$(".date").each(function() { $(this).html(prettyDate($(this).html())); });';
    $this->jquery->addJqueryScript($js_ready);
    $data['js'] = $this->jquery->processJquery();


    $this->load->view('welcome', $data);
  }
}
?>
