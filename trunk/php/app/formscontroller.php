<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'editpropertycontroller.php';
class FormsController extends k_Controller
{

  function forward($name) {
    if ($name == 'newclass') {
      $next = new NewClassController($this, $name);
      return $next->handleRequest();
    }
    elseif ($name == 'newprop') {
      $next = new NewPropertyController($this, $name);
      return $next->handleRequest();
    }    
    elseif ($name == 'editprop') {
      $next = new EditPropertyController($this, $name);
      return $next->handleRequest();
    } 
    elseif ($name == 'editclass') {
      $next = new EditClassController($this, $name);
      return $next->handleRequest();
    } 
    $response = new k_http_Response(404);
    $response->setContent('<html><head><title>404 Not Found</title></head><body><h1>404 Not Found</h1></body></html>');
    throw $response;
  }

  function GET() {
    $params = Array('content' => '');
    return $this->render("templates/forms.tpl.php", $params);
  }
}
?>
