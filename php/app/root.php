<?php
class Root extends k_Dispatcher
{
  public $debug = TRUE;

  public $map = Array(
    'terms' => 'Browse',
    'create' => 'Create',
    'changes' => 'Recent Changes',
    'about' => 'About',
  );

  function forward($name) {
    
    if ($name == 'changes') {
      $next = new RecentChangesController($this, $name);
    }
    elseif ($name == 'terms') {
      $next = new TermListController($this, $name);
    }
    elseif ($name == 'forms') {
      $next = new FormsController($this, $name);
    }
    elseif ($name == 'create') {
      $next = new ParticipateController($this, $name);
    }
    elseif ($name == 'about') {
      $next = new AboutController($this, $name);
    }
    elseif ($name == 'login') {
      $next = new LoginController($this, $name);
    }
    
    if (isset($next)) {
      $params = Array('content' => $next->handleRequest());
      return $this->render("templates/root.tpl.php", $params);
    }
    else {
      throw new k_http_Response(404);
    }  
  }

  function GET() {
    $params = Array('content' => $this->render("templates/home.tpl.php", array()) );
    return $this->render("templates/root.tpl.php", $params);
  }

  function HEAD() {
    throw new k_http_Response(200);
  }
}
