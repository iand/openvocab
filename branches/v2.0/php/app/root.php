<?php
class Root extends k_Dispatcher
{
  public $debug = TRUE;

  public $map = Array(
    'terms' => 'Terms',
    'browse' => 'Browse',
    'create' => 'Create',
    'changes' => 'Recent Changes',
    'about' => 'About',
  );

  function forward($name) {
    
    if ($name == 'changes') {
      $next = new RecentChangesController($this, $name);
    }
    elseif (preg_match('~^terms~', $name)) {
      $next = new TermListController($this, $name);
    }
    elseif ($name == 'browse') {
      $next = new BrowseController($this, $name);
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
    $vars = Array('content' => $this->render("templates/home.tpl.php", array()) );
    return $this->render("templates/root.tpl.php", $vars);
  }

  function HEAD() {
    throw new k_http_Response(200);
  }
}
