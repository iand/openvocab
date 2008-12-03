<?php
ini_set('memory_limit', '160M');
require_once 'constants.inc.php';
require_once MORIARTY_DIR . 'store.class.php';
require_once MORIARTY_DIR . 'sparqlservice.class.php';
require_once MORIARTY_DIR . 'simplegraph.class.php';

class BrowseController extends k_Controller
{

  function forward($name) {
    if ($name == 'relations') {
      $next = new BrowseRelationsController($this, $name);
    }
    else if ($name == 'subprop') {
      $next = new BrowseSubPropertyController($this, $name);
    }    
    else if ($name == 'subclass') {
      $next = new BrowseSubClassController($this, $name);
    }    
    
    if (isset($next)) {
      return $next->handleRequest();
    }
    else {
      $response = new k_http_Response(404);
      $response->setHeader("Content-type", "text/html");
      $response->setContent("<html><head><title>404 Not Found</title></head><body><h1>404 Not Found</h1></body></html>");
      throw $response;
    }    
    }

  function GET() {
    $vars = array(  );
    return $this->render("templates/browse.tpl.php", $vars);
  }
}
?>
