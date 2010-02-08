<?php
// Useful edition functions

function get_edition_label($item_uri, $graph) {
  $publisher_name = $graph->get_first_literal($item_uri, 'http://purl.org/dc/terms/publisher', '');
  $issued = $graph->get_first_literal($item_uri, 'http://purl.org/dc/terms/issued', '');
  $publication_place_name = $graph->get_first_literal($item_uri, 'http://rdvocab.info/Elements/placeOfPublication', '');
  $language = $graph->get_first_literal($item_uri, 'http://purl.org/dc/terms/language', '');
  $isbn13 = $graph->get_first_literal($item_uri, 'http://purl.org/ontology/bibo/isbn13', '');

  $ret = '';
  $ret .= $publication_place_name ? htmlspecialchars($publication_place_name) . ':' : '';
  $ret .= $publisher_name ? ' ' . htmlspecialchars($publisher_name) : '';
  $ret .= $issued && $publisher_name ? ',' : '';
  $ret .= $issued ? ' ' . htmlspecialchars($issued) : '';
  $ret .= $language ? ' (' . htmlspecialchars($language) . ')' : '';
  $ret .= $isbn13 ? ', ISBN ' . htmlspecialchars($isbn13) . '' : '';
  
  return $ret;
  
}


