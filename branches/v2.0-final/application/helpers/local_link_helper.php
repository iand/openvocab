<?php
  function local_link($uri) {
    if (preg_match('~^http://[a-zA-Z0-9-\.]+(.*)$~', $uri, $m)) {
      $link = $m[1];
      if (preg_match('~^/' . config_item('term_path') .  '/(.*)$~', $link, $m)) {
        return '/' . config_item('term_document_path') . '/' . $m[1];
      }
      else if ($link === '/' . config_item('term_path')) {
        return '/' . config_item('term_document_path');
      }
      else {
        return $link;
      }
    }
    else {
      return $uri;
    }
  }
