<?php
$prefixes = array (
                    'http://www.w3.org/2000/01/rdf-schema#' => 'rdfs',
                    'http://www.w3.org/2002/07/owl#' => 'owl',
                    'http://purl.org/vocab/changeset/schema#' => 'cs',
                    'http://schemas.talis.com/2006/bigfoot/configuration#' => 'bf',
                    'http://schemas.talis.com/2006/frame/schema#' => 'frm',
                    'http://open.vocab.org/terms/' => 'ov',

                    'http://purl.org/dc/elements/1.1/' => 'dc',
                    'http://purl.org/dc/terms/' => 'dct',
                    'http://purl.org/dc/dcmitype/' => 'dctype',

                    'http://xmlns.com/foaf/0.1/' => 'foaf',
                    'http://purl.org/vocab/bio/0.1/' => 'bio',
                    'http://www.w3.org/2003/01/geo/wgs84_pos#' => 'geo',
                    'http://purl.org/vocab/relationship/' => 'rel',
                    'http://purl.org/rss/1.0/' => 'rss',
                    'http://xmlns.com/wordnet/1.6/' => 'wn',
                    'http://www.daml.org/2001/10/html/airport-ont#' => 'air',
                    'http://www.w3.org/2000/10/swap/pim/contact#' => 'contact',
                    'http://www.w3.org/2002/12/cal/ical#' => 'ical',
                    'http://purl.org/vocab/frbr/core#' => 'frbr',

                    'http://schemas.talis.com/2005/address/schema#' => 'ad',
                    'http://schemas.talis.com/2005/library/schema#' => 'lib',
                    'http://schemas.talis.com/2005/dir/schema#' => 'dir',
                    'http://schemas.talis.com/2005/user/schema#' => 'user',
                    'http://schemas.talis.com/2005/service/schema#' => 'sv',
                  );

function get_user() {
  return "Anonymous user at " . $_SERVER["REMOTE_ADDR"];
}


function remote_to_local($uri) {
  if ( $_SERVER["HTTP_HOST"] != 'open.vocab.org') {
    return str_replace('open.vocab.org', $_SERVER["HTTP_HOST"], $uri);
  }
  else {
    return $uri;
  }
}

function local_to_remote($uri) {
  if ( $_SERVER["HTTP_HOST"] != 'open.vocab.org') {
    return str_replace($_SERVER["HTTP_HOST"], 'open.vocab.org', $uri);
  }
  else {
    return $uri;
  }
}

function term_to_doc($uri) {
  return str_replace('open.vocab.org/terms/', 'open.vocab.org/termdocs/', $uri);
}

function link_uri($uri) {
  if (preg_match('/^https?:\/\//', $uri) ) {
    return '<a href="' . htmlspecialchars(remote_to_local($uri)) . '" class="uri">' . make_formatted_qname($uri) . '</a>';
  }
  else {
    return htmlspecialchars($uri);
  }
}

function make_formatted_qname($uri) {
  global $prefixes;
  if (preg_match('/^(.*[\/\#])([a-z0-9\-\_]+)$/i', $uri, $m)) {
    if ( array_key_exists($m[1], $prefixes)) {
      return '<span class="prefix">' . htmlspecialchars($prefixes[$m[1]]) . ':</span><span class="localname">' . htmlspecialchars($m[2]) . '</span>';;
    }  
  }
  return $uri;
}

function make_qname($uri) {
  global $prefixes;
  if (preg_match('/^(.*[\/\#])([a-z0-9\-\_]+)$/i', $uri, $m)) {
    if ( array_key_exists($m[1], $prefixes)) {
      return $prefixes[$m[1]] . ':' . $m[2];
    }  
  }
  return $uri;
}


function list_relations_prose(&$index, $uri, $property, $label, $use_definite_article = true) {
  if ( array_key_exists($uri, $index)) {
    if ( array_key_exists($property, $index[$uri])) {
      echo '<p>' . htmlspecialchars($label) . ' ';
      for ($i = 0 ; $i < count($index[$uri][$property]); $i++) {
        if ($i > 0) {
          if ($i < count($index[$uri][$property]) - 1) { echo ', '; }      
          else if ($i == count($index[$uri][$property]) - 1) { echo ' and '; }      
        }
        $text = $index[$uri][$property][$i]['value'];

        if ( $use_definite_article ) {
          echo 'a';        
          if ( preg_match('/^[aeiou]/', $text) ) {
            echo 'n';        
          }
          echo ' ';
        }
        
        echo link_uri($text);
      }
      echo '</p>' . "\n"; 
    }
  }
}


function list_relations(&$index, $uri, $property, $label) {
  if ( array_key_exists($uri, $index)) {
    if ( array_key_exists($property, $index[$uri])) {
      echo '<p>' . htmlspecialchars($label) . ': ';
      for ($i = 0 ; $i < count($index[$uri][$property]); $i++) {
        if ($i > 0) { echo ', '; }      
        echo link_uri($index[$uri][$property][$i]['value']);
      }
      echo '</p>' . "\n"; 
    }
  }
}


function paraphrase(&$index, $uri) {
  if ( array_key_exists($uri, $index)) {
    if ( array_key_exists(RDF_TYPE, $index[$uri])) {
      
    }
  }  
}

function list_form_fields($label, $name, $values) {
  echo '<tr>' . "\n";
  echo '  <th valign="top"><label for="' . $name . '_' . count($values) . '">' . htmlspecialchars($label) . ': </label></th>' . "\n";
  echo '  <td valign="top">' . "\n";
  for ($i = 0; $i < count($values); $i++) {
    echo '      <input type="text" class="text" size="60" name="' . $name . '_' . $i . '" id="' . $name . '_' . $i . '" value="' . htmlspecialchars($values[$i]) . '"/> <br />' . "\n";
  }
  echo '    <input type="text" class="text" size="60" name="' . $name . '_' . count($values) . '" id="' . $name . '_' . count($values) . '" value=""/>' . "\n";
  echo ' <a href="#" id="' . $name . '_more" class="more">add another</a>' . "\n";
  echo '  </td>' . "\n";
  echo '</tr>' . "\n";
}
