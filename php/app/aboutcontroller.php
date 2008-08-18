<?php
require_once MORIARTY_DIR . 'moriarty.inc.php';
require_once MORIARTY_DIR . 'store.class.php';
require_once MORIARTY_DIR . 'simplegraph.class.php';


class AboutController extends k_Controller
{

  function forward($name) {
    $params = Array();
    switch ($name) {
      case 'privacy':
      case 'availability':
      
      case 'rights':
        return $this->render("templates/about." . $name . ".tpl.php", $params);
    }

    $response = new k_http_Response(404);
    $response->setContent('<html><head><title>404 Not Found</title></head><body><h1>404 Not Found</h1></body></html>');
    throw $response;
  }

  function GET() {
    $params = Array();
    return $this->render("templates/about.tpl.php", $params);
  }
}
?>
