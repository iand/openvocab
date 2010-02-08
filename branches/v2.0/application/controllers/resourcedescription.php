<?php
require_once MORIARTY_DIR . 'store.class.php';
require_once MORIARTY_DIR . 'simplegraph.class.php';
require_once MORIARTY_DIR . 'labeller.class.php';
require_once PAGET_DIR . 'paget_storebackedresourcedescription.class.php';
require_once 'widgets.php';

class ResourceDescription extends Controller {
  var $property_order =  array('http://www.w3.org/2004/02/skos/core#prefLabel', RDFS_LABEL, 'http://purl.org/dc/terms/title', DC_TITLE, FOAF_NAME, 'http://www.w3.org/2004/02/skos/core#definition', RDFS_COMMENT, 'http://purl.org/dc/terms/description', DC_DESCRIPTION, 'http://purl.org/vocab/bio/0.1/olb', RDF_TYPE); 
  var $ignore_properties = array();
  var $excludes = array();
  var $graph;
  function __construct() {
    if (count($_GET)) show_404('page');

    parent::Controller();
    $this->request_uri = $this->config->item('resource_base') . $this->uri->uri_string();
    $this->type = '';

    $this->resource_path = '/';
    $base_uri = $this->config->item('resource_base') . $this->uri->uri_string();
    
    if ( preg_match('~^(.+)\.([a-z]+)$~', $this->uri->uri_string(), $m)) {
      $this->resource_path = $m[1];
      $base_uri = $this->config->item('resource_base') . $m[1];
      $this->type = $m[2];
    }

    $this->resource_uri = $this->get_resource_uri($base_uri);
    $this->graph = new SimpleGraph();
    $this->read_data();
    if (! $this->has_description() ) show_404('page');
    $this->load->helper('text');

  }
   
  function has_description() {
    return $this->graph->has_triples_about($this->resource_uri);
  }
   
  function get_resource_uri($base_uri) {
    return $base_uri;
  }
   
  function read_data() {
    $store = new Store($this->config->item('store_uri'));
    $response = $store->describe($this->resource_uri, 'lcbd', 'json');
    if ($response->is_success()) {
      $this->graph->add_json($response->body);
    }
  }
   
  function label_data() {
    $labeller = new Labeller();
    $labeller->label_graph($this->graph);
  }

  function augment_data() {
    $dbpedia_list  = array();
    $sameas_list = $this->graph->get_resource_triple_values($this->resource_uri, 'http://www.w3.org/2002/07/owl#sameAs');
    foreach ($sameas_list as $sameas) {
      if (preg_match('~^http://dbpedia.org/resource~', $sameas)) {
        $dbpedia_list[] = $sameas;
      }
    }
    $store = new Store('http://api.talis.com/stores/dbpedia');
    $response = $store->describe($dbpedia_list, 'lcbd', 'json');
    
    if ($response->is_success()) {
      $body = $this->replace_uris_in_json($response->body, $dbpedia_list, $this->resource_uri);
      $this->graph->add_json($body);
    }
    
  }
  
  // $look_for could be an array
  function replace_uris_in_json($json, $look_for, $replace_with) {
    $munged_look_for = str_replace('/', '\\/', $look_for);
    $munged_replace_with = str_replace('/', '\\/', $replace_with);
    return str_replace($munged_look_for, $munged_replace_with, $json);
  }
  


  function _getAcceptHeader() {
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        $accepts = explode(',', $_SERVER['HTTP_ACCEPT']);
        $orderedAccepts = array();
        foreach ($accepts as $key => $accept) {
            $exploded = explode(';', $accept);
            if (isset($exploded[1]) && substr($exploded[1], 0, 2) == 'q=') {
                $orderedAccepts[substr($exploded[1], 2)][] = trim($exploded[0]);
            } else {
                $orderedAccepts['1'][] = trim($exploded[0]);
            }
        }
        krsort($orderedAccepts);
        $accepts = array();
        foreach ($orderedAccepts as $q => $acceptArray) {
            foreach ($acceptArray as $mimetype) {
                $accepts[] = trim($mimetype);
            }
        }
        
        // FIX for IE. if */*, replace with text/html
        $key = array_search('*/*', $accepts);
        if ($key !== FALSE) {
            $accepts[$key] = 'text/html';
        }
        return $accepts;
    }
    return array('*/*');
  }

  function do_303($id) {
    $this->load->helper('url');
    $this->load->helper('httprange14');
    $extension = 'rdf';
    $accepts = $this->_getAcceptHeader();
    foreach ($accepts as $accept) {
      if ($accept == 'application/rdf+xml') {
        break;          
      }
      else if ($accept == 'application/json') {
        $extension = 'json';
        break;          
      }
      else if ($accept == 'text/plain') {
        $extension = 'turtle';
        break;          
      }
      else if ($accept == 'text/html') {
        $extension = 'html';
        break;          
      }
    }

    $canonical_uris = $this->graph->get_literal_triple_values($this->resource_uri, 'http://open.vocab.org/terms/canonicalUri');
    if ( count($canonical_uris) == 1 ) {
      $page_uri = page_about_resource($canonical_uris[0], $extension);
    }
    else {
      $page_uri = page_about_resource($this->resource_uri, $extension);
    }
    redirect( $page_uri, 'location', 303);
  }

  function do_html() {
    $this->load->library('jquery');
    $this->jquery->setExternalJquery('/js/jquery-1.3.2.min.js'); 
    $this->jquery->setJqDocumentReady(true); 

    $this->augment_data();
    $this->label_data();

    $links = array();
    $formats = $this->graph->get_resource_triple_values($this->request_uri, 'http://purl.org/dc/terms/hasFormat');
    foreach ($formats as $format_uri) {
      $media_type = $this->desc->get_first_literal($format_uri, 'http://purl.org/dc/elements/1.1/format');  
      if ($media_type != $this->type) {
        $label = $this->desc->get_first_literal($format_uri, RDFS_LABEL, $media_type); 
        $links[] = array('type' => $media_type, 'href' => $format_uri, 'label' => $label, 'title' => $label. ' version of this document');
      }
    }

    $data['description'] = "View information about " . $this->graph->get_label($this->resource_uri); 
    $data['links'] = $links;
    $data['type'] = $this->type;
    $data['request_uri'] = $this->request_uri;
    $data['resource_uri'] = $this->resource_uri;
    $data['desc'] = $this->graph;
    $data['title'] = $this->graph->get_label($this->resource_uri);

    
    $data['content'] = $this->get_content();
    $data['sidebar'] = $this->get_sidebar();
    
    $data['js'] = $this->jquery->processJquery();
    $this->load->view('resourcedescriptionview', $data);
  }
  
  function get_widgets() {
    $widgets = array();
    $widgets[] = new Widget();
    return $widgets;
  }
  
  function get_secondary_widgets() {
    $widgets = array();
    $widgets[] = new SearchWidget();
    $widgets[] = new IdentifiersWidget();
    $widgets[] = new WorkSubjectsWidget();
    $widgets[] = new LinksWidget('More on other sites');
    $widgets[] = new LinkedDataWidget();
    return $widgets;
  }

  function get_content() {
    $content = '';

    $index = $this->graph->get_index();
    if (array_key_exists($this->resource_uri, $index)) {
      $available_properties = array_keys($index[$this->resource_uri]);

      $widgets = $this->get_widgets();

      foreach ($widgets as $widget) {
        if ($widget->can_display($this->resource_uri, $available_properties, $this->graph)) {
          $content .= $widget->render($this->resource_uri, $available_properties, $this->graph);
          $js_script = $widget->get_jquery_script($this->resource_uri, $available_properties, $this->graph);    
          if ($js_script) {
            $this->jquery->addJqueryScript($js_script);
          }

          $available_properties = array_diff($available_properties, $widget->get_properties_used());
        }
      }    
    }
    return $content;
  }
  
  function get_sidebar() {
    $content = '';

    $index = $this->graph->get_index();
    if (array_key_exists($this->resource_uri, $index)) {
      $available_properties = array_keys($index[$this->resource_uri]);

      $widgets = $this->get_secondary_widgets();

      foreach ($widgets as $widget) {
        if ($widget->can_display($this->resource_uri, $available_properties, $this->graph)) {
          $content .= $widget->render_panel($this->resource_uri, $available_properties, $this->graph);
          $available_properties = array_diff($available_properties, $widget->get_properties_used());
        }
      }    
    }
    return $content;
  }
 
  
  function do_rdf($id) {
    header("content-type:application/rdf+xml");
    echo $this->graph->to_rdfxml();
  }
  
  function do_turtle($id) {
    header("content-type:text/n3");
    echo $this->graph->to_turtle();
  }

  function do_json($id) {
    header("content-type:application/json");
    echo $this->graph->to_json();
  }

}







?>
