<?php
// Useful edition functions

function get_edition_label($item_uri, $graph, $include_isbn = TRUE) {
  $publisher_name = $graph->get_first_literal($item_uri, 'http://rdvocab.info/Elements/publishersName', '');
  $issued = $graph->get_first_literal($item_uri, 'http://purl.org/dc/terms/issued', '');
  $publication_place_name = $graph->get_first_literal($item_uri, 'http://rdvocab.info/Elements/placeOfPublication', '');
  $language = $graph->get_first_literal($item_uri, 'http://purl.org/dc/terms/language', '');

  $ret = '';
  $ret .= $publication_place_name ? htmlspecialchars($publication_place_name) . ':' : '';
  $ret .= $publisher_name ? ' ' . htmlspecialchars($publisher_name) : '';
  $ret .= $issued && $publisher_name ? ',' : '';
  $ret .= $issued ? ' ' . htmlspecialchars($issued) : '';
  $ret .= $language ? ' (' . htmlspecialchars($language) . ')' : '';

  if ($include_isbn) {
    $isbn13 = $graph->get_first_literal($item_uri, 'http://purl.org/ontology/bibo/isbn13', '');
    if ($isbn13) {
      if ($ret) $ret .= ', ';
      $ret .= 'ISBN ' . htmlspecialchars($isbn13) . '';
    }
  }
  return $ret;
  
}


