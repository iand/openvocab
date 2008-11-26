<?php
require_once 'constants.inc.php';
require_once MORIARTY_DIR . 'store.class.php';
require_once MORIARTY_DIR . 'sparqlservice.class.php';

class TermListController extends k_Controller
{

  function forward($name) {
    $next_controller = new TermController($this, $name);
    return $next_controller->handleRequest()  ;
  }

  function GET() {
    $vars = array( 'results' => null, 'q' => '' );

    if ( empty($this->GET['q']) ) {

    $terms_query =  "prefix cs: <http://purl.org/vocab/changeset/schema#>
prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
select ?term ?label ?comment
where {
  {
    ?term a rdf:Property ;
          rdfs:label ?label .
          optional { ?term rdfs:comment ?comment .}
  }
  union {
    ?term a rdfs:Class ;
          rdfs:label ?label .
          optional { ?term rdfs:comment ?comment .}
  }
}
order by desc(?date)
";

    $store = new Store(STORE_URI);
    $sparql = $store->get_sparql_service();
    $vars['terms'] = $sparql->select_to_array($terms_query);
      
      
    }
    else {
      $query = $this->GET['q'];
      $store = new Store(STORE_URI);
      $cb = $store->get_contentbox();

      $vars['results'] = $cb->search_to_resource_list($query);

      $facet_service = $store->get_facet_service();
      $vars['facets'] = $facet_service->facets_to_array($query, array('tag'));

      $vars['q'] = $query;
    }

    return $this->render("templates/termlist.tpl.php", $vars);
  }
}
?>
