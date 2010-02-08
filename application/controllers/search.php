<?php
require_once 'resourcedescription.php';
require_once 'widgets.php';

class Search extends Controller {
  function __construct() {
    parent::Controller();
  }
    
  function do_html() {
    $data = array();
    $data['content'] = "<p>UNDER CONSTRUCTION</p><p>Use the search in the sidebar for now.</p>";
    $data['sidebar'] = '';
    $widget = new Widget();



    if (array_key_exists('q', $_GET)) {
      $query = $_GET['q'];
      $offset = array_key_exists('o', $_GET) ? $_GET['o'] : 0;
      
      $store = new Store($this->config->item('search_store_uri'));
      $cb = $store->get_contentbox();
      $search_uri = $cb->make_search_uri($query, 30, $offset);
      $responses = $store->search_and_facet($query, array('is','genre', 'format', 'author', 'subject', 'series'), 30, $offset);
      if ($responses['searchResponse']->is_success()) {
        $graph = new SimpleGraph();
        $graph->from_rdfxml($responses['searchResponse']->body);
        $data['content'] = $this->render_results($query, $search_uri, $graph);
      }
      else {
        $data['content'] = "<p>There was a problem with your search. Please try again.</p>";
      }
      if ($responses['facetResponse']->is_success()) {
        $fs = $store->get_facet_service();
        
        $facets = $fs->parse_facet_xml($responses['facetResponse']->body);

        if (array_key_exists('genre', $facets) && count($facets['genre']) > 0) {
          $data['sidebar'] .= $widget->make_panel('Refine by genre', $this->render_facet('genre', $query, $facets));
        }
        if (array_key_exists('author', $facets) && count($facets['author']) > 0) {
          $data['sidebar'] .= $widget->make_panel('Refine by author', $this->render_facet('author', $query, $facets));
        }
        if (array_key_exists('subject', $facets) && count($facets['subject']) > 0) {
          $data['sidebar'] .= $widget->make_panel('Refine by subject', $this->render_facet('subject', $query, $facets));
        } 
        if (array_key_exists('series', $facets) && count($facets['series']) > 0) {
          $data['sidebar'] .= $widget->make_panel('Refine by series', $this->render_facet('series', $query, $facets));
        } 
        if (array_key_exists('format', $facets) && count($facets['format']) > 0) {
          $data['sidebar'] .= $widget->make_panel('Refine by format', $this->render_facet('format', $query, $facets));
        }
        if (array_key_exists('is', $facets) && count($facets['is']) > 0) {
          $data['sidebar'] .= $widget->make_panel('Refine by type', $this->render_facet('is', $query, $facets));
        }
      }
    }

    
    $data['description'] = "";
    $data['links'] = array();
    $data['type'] = 'html';
    $data['title_encoded']  = '        <form action="/search.html" method="get">';
    $data['title_encoded'] .= '          <input class="text" name="q" id="q" value="' . htmlspecialchars($query) . '" type="text" style="width:400px; vertical-align:middle;"> <input name="search" value="Search" type="image" src="/images/srch.jpg" vertical-align:middle;>';
    $data['title_encoded'] .= '        </form>';

    
    $this->load->view('resourcedescriptionview', $data);
    
  }
  

  function render_facet($name, $query, $facets) {
    $facet_html = '<ul>';
    foreach ($facets[$name] as $field_value_pair) {
      $facet_html .= '<li><a href="/search.html?q=' . htmlspecialchars(urlencode($query . ' AND ' . $name . ':"' . $field_value_pair['value'] . '"')) . '">' . $field_value_pair['value'] . '</a></li>';
    }
    $facet_html .='</ul>';
    return $facet_html;
  }

  function render_results($query, $search_uri, $graph) {
    $ret  = '';
    
    $is_search = FALSE;     
    if ($graph->subject_has_property($search_uri, 'http://a9.com/-/spec/opensearch/1.1/totalResults')) {
      
      $total_results = $graph->get_first_literal($search_uri, 'http://a9.com/-/spec/opensearch/1.1/totalResults');
      $start_index = $graph->get_first_literal($search_uri, 'http://a9.com/-/spec/opensearch/1.1/startIndex');
      $items_per_page = $graph->get_first_literal($search_uri, 'http://a9.com/-/spec/opensearch/1.1/itemsPerPage');

      if (! is_numeric($items_per_page) || $items_per_page == 0) {
        $items_per_page = 30; 
      }
      if (! is_numeric($start_index)) {
        $start_index = 0; 
      }
      if (! is_numeric($total_results) || $total_results == 0) {
        $total_results = $start_index;  
      }

      
    
      $ret .= $this->render_result_info($query, $total_results, $start_index, $items_per_page);
    }

    $list_items = array();
    $widget = new Widget();
    $values = $graph->get_subject_property_values($search_uri, 'http://purl.org/rss/1.0/items');

    $this->load->helper('editions');
    $this->load->helper('text');

    foreach ($values as $value_info) {
      if ($value_info['type'] == 'uri') {
        $props = $graph->get_subject_properties($value_info['value'], TRUE);
        
        foreach ($props as $prop) {
          if ( preg_match("~^http://www.w3.org/1999/02/22-rdf-syntax-ns#_(\d+)$~", $prop, $m)) {
            $list_items[$m[1]] = $graph->get_resource_triple_values($value_info['value'], $prop);
          }
        }


        if (count($list_items) > 0) {
          ksort($list_items, SORT_NUMERIC);

          foreach ($list_items as $number => $items) {
            foreach ($items as $item_uri) {
              $meta = '<a href="' . htmlspecialchars($item_uri) .'">'. htmlspecialchars($item_uri) . '</a>';
              $type = $graph->get_first_literal($item_uri, 'http://schemas.semanticlibrary.org/search/terms/is', '');
              if ($type) {
                $meta = '<strong>'. $type . '</strong> | ' . $meta;
              }

              $isbn13 = $graph->get_first_literal($item_uri, 'http://purl.org/ontology/bibo/isbn13', '');
              if ($isbn13) {
                $image = 'http://prism.talis.com/broadminster/imageservice.php?id=' . urlencode($isbn13);
              }
              else {
                $image = '/images/noimage.jpg';
              }
              
              $label = $graph->get_label($item_uri);
              if ($label == $item_uri || $label == 'Item') {
                $label = 'Unknown';
              }
              
              $desc = $graph->get_description($item_uri);
              
              $ret .= $widget->make_item_row($label, $widget->page_about_resource($item_uri), $desc, $image, $meta);
            }
          }
        }
      }
    }

    $ret .= $this->render_pagination($search_uri, $query, $total_results, $start_index, $items_per_page);
    return $ret;
  }

  function render_result_info($query, $total_results, $start_index, $items_per_page) {
    $result_info  = '';
    if ($total_results == 0) {
      $result_info  = 'No items matched <strong>' . htmlspecialchars($query) . '</strong>';    
    }
    else {
      $end_index = $start_index + $items_per_page;
      if ($end_index > $total_results) {
        $end_index = $total_results;  
      }
      $result_info .= 'Results <strong>' . htmlspecialchars($start_index + 1) . ' - ' . htmlspecialchars($end_index) . '</strong> of ';
      
      if ($total_results - $start_index > 100) {
        $result_info .= 'about ';  
      }
      
      $result_info .='<strong>' . htmlspecialchars($total_results) . ' </strong> for <strong>' . htmlspecialchars($query) . '</strong>';
    }

    return '<p id="resultinfo">' . $result_info . "</p>\n";
  }

  function render_pagination($search_uri, $query, $total_results, $start_index, $items_per_page) {
    $ret = '';
    
    $total_number_of_pages = ceil($total_results / $items_per_page);
    if ($total_number_of_pages > 1) {
      $ret = '<p class="paginator"><strong>Result pages: </strong><br />';
      $current_page = floor($start_index / $items_per_page) + 1;
       
      if ($current_page > 1) {
        $offset_raw = $start_index - $items_per_page;
        $offset_page = floor( $offset_raw / $items_per_page);
        $offset = $offset_page * $items_per_page;
        if ( $offset < 0 ) $offset = 0;
        $ret .= ' <a href="/search.html?q=' . htmlspecialchars($query) . '&o=' . $offset . '">Prev</a>'; 
      } 
      if ($current_page > 6) {
        $page_links_start = $current_page - 5;
        $page_links_end = $current_page + 4;
      }
      else {
        $page_links_start = 1;
        $page_links_end = 10;
      }       
      if ($page_links_end > $total_number_of_pages) {
        $page_links_end = $total_number_of_pages;
      }
      
      for ($i = $page_links_start; $i <= $page_links_end;$i++ ) {
        if ($i == $current_page) {
          $ret .= ' ' . $i; 
        } 
        else {
          $offset_raw = $start_index - ($items_per_page * ($current_page - $i));
          $offset_page = floor( $offset_raw / $items_per_page);
          $offset = $offset_page * $items_per_page;
          if ( $offset < 0 ) $offset = 0;
          if ( $offset <= $total_results ) {
            $ret .= ' <a href="/search.html?q=' . htmlspecialchars($query) . '&o=' . $offset . '">' . $i . '</a>'; 
          }
        }
      }

      if ($current_page < $total_number_of_pages) {
        $offset_raw = $start_index + $items_per_page;
        $offset_page = floor( $offset_raw / $items_per_page);
        $offset = $offset_page * $items_per_page;
        if ( $offset <= $total_results ) {
          $ret .= ' <a href="/search.html?q=' . htmlspecialchars($query) . '&o=' . $offset . '">Next</a>'; 
        }
      } 
      $ret .= '</p>';
    }          
    return $ret;
  }

}
