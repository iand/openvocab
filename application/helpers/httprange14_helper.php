<?php
  function page_about_resource($resource_uri, $extension = 'html') {
    $uri = $resource_uri;
    if (preg_match('~http://([^/]+)/~i', $resource_uri, $m)) {
      if ( $_SERVER["HTTP_HOST"] == $m[1] . '.local' ) {
        $uri = str_replace($m[1], $_SERVER["HTTP_HOST"], $resource_uri) . '.html';
      }
      else if ( $_SERVER["HTTP_HOST"] == $m[1] ) {
        $uri .= '.' . $extension;
      }
    }
    return $uri;    
  }
