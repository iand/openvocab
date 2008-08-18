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

    if (! empty($this->GET['q']) ) {
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
