<?php
class Widget {
  var $title;
  var $excludes = array();
  var $properties_used = array('http://schemas.talis.com/2005/dir/schema#etag');
  
  function __construct($title = '') {
    $this->title = $title;
  }
  
  function get_title() {
    return $this->title;
  }

  function get_properties_used() {
    return $this->properties_used;
  }

  function add_properties_used($properties) {
    $this->properties_used = array_merge($this->properties_used, $properties);
  }

  function can_display($resource_uri, $available_properties, $graph) {
    return (count($available_properties) > 0);
  }

  function render($resource_uri, $available_properties, $graph, $class = 'section') {

    $content = "\n" . '<div class="' . htmlspecialchars($class) . '">' . "\n";
    $title = $this->get_title();
    if ($title) {
      $content .= '<h3>' . htmlspecialchars($title) . '</h3>' . "\n";
    }
    $content .= $this->get_content($resource_uri, $available_properties, $graph);
    $content .= "\n" . '</div>' . "\n";
    return $content;
  }

  function render_panel($resource_uri, $available_properties, $graph, $class = 'section') {
    return $this->make_panel($this->get_title(), $this->get_content($resource_uri, $available_properties, $graph));
  }

  
  function get_jquery_script($resource_uri, $available_properties, $graph) {
    return '';
  }

  function get_content($resource_uri, $available_properties, $graph) {
    $content = '<p>UNDER CONSTRUCTION</p>' . '<p>Raw data is as follows:</p>';
    $index = $graph->get_index();
    if (array_key_exists($resource_uri, $index)) {
      $content .= $this->format_property_value_list($resource_uri, $available_properties, $graph);
      $this->add_properties_used($available_properties);
    }
    return $content;
  }
  
  
  function format_property_value_list($resource_uri, $properties, $graph, $layout = 'table', $link_properties  = TRUE) {
    $data = array();
    foreach ($properties as $property) {
      if (! $this->is_excluded($resource_uri, $property) ) {
        $property_values = $graph->get_subject_property_values($resource_uri, $property);
        if ( count($property_values) > 0) {
          if ( count($property_values) == 1) {
            $label = ucfirst($graph->get_first_literal($property, RDFS_LABEL));
          }
          else {
            $label = ucfirst($graph->get_first_literal($property, 'http://purl.org/net/vocab/2004/03/label#plural'));
          }         
          
          
          $formatted_label = $this->format_property_label($property, $label, $graph, $link_properties);
          $formatted_values = $this->get_property_values_list($property, $property_values, $graph);

          $data[] = array('label' => $formatted_label, 'values' => $formatted_values );
          $this->exclude($resource_uri, $property);
        }
      }
    }

    return $this->layout_data($data, $layout);   
  }    
  
  
  
  
  function layout_data(&$data, $layout = 'table') {
    $ret = '';
    if ( count($data) > 0 ) {
      if ($layout == 'table') {
        $class = "odd";
        $ret .= "\n" . '<table width="100%" class="proplist">' . "\n";
        foreach ($data as $item) {
          $ret .= '  <tr>' . "\n" .'    <th valign="top" class="label ' . $class . '">' . $item['label'] . '</th>' . "\n" .'    <td valign="top" width="80%" class="value ' . $class . '">' . join("<br />\n", $item['values']) . '</td>' . "\n" .'  </tr>' . "\n";
          if ($class == "odd") {
            $class = "even";
          }
          else {
            $class = "odd"; 
          }
        }   
        $ret .= '</table>' . "\n";
      }
      else if ($layout == 'dl') {
        $ret .= "\n" . '<dl class="proplist">' . "\n";
        foreach ($data as $item) {
          $ret .= "  <dt>" . $item['label'] . "</dt>\n  <dd>";
          $ret .= join("</dd>\n  <dd>", $item['values']);
          $ret .= "</dd>\n";
        }   
        $ret .= "</dl>\n";
      }
      else if ($layout == 'inline') {
        foreach ($data as $item) {
          $ret .= "<p class=\"proplist\"><strong>" . $item['label'] . "</strong>: ";
          $ret .= join(", ", $item['values']);
          $ret .= "</p>\n";
        }   
      }
    }
    
    return $ret;
  }

  function render_property_group($resource_uri, $properties, $title, $graph, $layout = 'p') {
    $ret = '';
    if (! is_array($properties)) $properties = array($properties);
    
    $values = $graph->get_subject_property_values($resource_uri, $properties);

    if (count($values) > 0) {
      $value_list = array();
      foreach  ($properties as $property) {
        $value_list = array_merge($value_list, $this->get_property_values_list($property, $values, $graph, TRUE));
      }
      $value_list = array_unique($value_list);
      
      if ($layout == 'p' || $layout == 'div') {
        $ret .= "\n<" . $layout . " class=\"propgroup\">\n";
        if ($title) {
          $ret .= '<strong>' . $title . '</strong><br />' . "\n";
        }
        $ret .= join("<br />\n", $value_list);
        $ret .= '</' . $layout . '>' . "\n";
      }
      else if ($layout == 'dl' ) {
        $ret .= "\n<dl class=\"propgroup\">\n";
        if ($title) {
          $ret .= '  <dt>' . $title . '</dt>' . "\n";
        }
        $ret .= "  <dd>";
        $ret .= join("</dd>\n  <dd>", $value_list);
        $ret .= "</dd>\n</dl>\n";
      }
      else if ($layout == 'ul' ) {
        $ret .= "\n<div class=\"propgroup\">\n";
        if ($title) {
          $ret .= '  <h4>' . $title . '</h4>' . "\n";
        }
        $ret .= "  <ul><li>";
        $ret .= join("</li>\n  <li>", $value_list);
        $ret .= "</li></ul>\n</div>\n";
      }
      else if ($layout == 'inline') {
        $ret .= "\n<p class=\"propgroup-inline\">\n";
        if ($title) {
          $ret .= $title;
        }
        $ret .= $this->join_list($value_list);
        $ret .= "</p>\n";
      }
    }
    return $ret;
  }


  function exclude($resource_uri, $property_uri) {
    $this->excludes[$resource_uri . ' ' . $property_uri] = 1;
  }

  function is_excluded($resource_uri, $property_uri) {
    return array_key_exists($resource_uri . ' ' . $property_uri, $this->excludes);
  }


  function get_property_values_list($property, &$property_values, $graph, $plain_uris = FALSE) {
    $values = array();
    
    for ($i = 0; $i < count($property_values); $i++) {
      if ($property_values[$i]['type'] == 'uri') {
        $label = '';
        if ($plain_uris) {
          $label = $property_values[$i]['value'];
        }
        $values[] = $this->link_uri($property_values[$i]['value'], $label, $graph);
      }
      else {
        $values[] = $this->render_literal($property_values[$i], $graph);
      }
    }   
    sort($values);
    return $values;
    
  }

  function format_property_values($property, &$property_values, $graph, $layout = 'br', $title = '') {
    $values = $this->get_property_values_list($property, &$property_values, $graph);
    return join("<br />\n", $values);
  }

  function join_list($items, $delim = ', ', $final_delim = ' or ') {
    if (count($items) == 0) return '';
    else if (count($items) == 1) return $items[0];
    else if (count($items) == 2) return $items[0] . $final_delim. $items[1];
    else {
      return join($delim, array_slice($items, 0, -1)) . $final_delim. $items[1];
    }
  }


  function render_literal($resource_info, $graph) {
    $html = '<span class="lit">';
    
    $value = $resource_info['value'];
    $encode_value = TRUE;
    if (isset($resource_info['datatype'])) {
      if ($resource_info['datatype'] == 'http://www.w3.org/2001/XMLSchema#date') {
        $datetime = strtotime($resource_info['value']);
        if ($datetime !== FALSE) {
          $value = date("j F Y", $datetime);
        }
      }
      else if ($resource_info['datatype'] == 'http://www.w3.org/1999/02/22-rdf-syntax-ns#XMLLiteral') {
        $encode_value = FALSE;
      }
    }

    if ($encode_value) {
      $html .= htmlspecialchars($value);
    }
    else {
      $html .= $value;
    }

    $html .= '</span>';
    return $html;
  }
  
  function e($text) {
    echo(htmlspecialchars($text));  
  } 

  function link_uri($uri, $label = '', $graph, $use_definite_article = false) {
    if (preg_match('/^_:/', $uri) ) {
      if ($label == '') {
        $label = $this->make_labelled_uri($uri, $graph);
      }
      return $label;
    }
    else if (preg_match('/^https?:\/\//', $uri) ) {
      $ret = '';
      if ($label == '') {
        $label = $this->make_labelled_uri($uri, $graph);
      }
      
      if ( $use_definite_article ) {
        $ret .= 'a';        
        if ( preg_match('/^(<[^>]+>)?[aeiou]/', $label) ) {
          $ret .= 'n';        
        }
        $ret .= ' ';
      }   

      $ret .= '<a href="' . htmlspecialchars($this->page_about_resource($uri)) . '" class="uri">';
      $ret .= $label . '</a>';
      return $ret;
    }
    else {
      return htmlspecialchars($uri);
    }
  }


  function make_labelled_uri($uri, $graph) {
    $title = $graph->get_label($uri);
    if ($title != $uri) {
      return htmlspecialchars($title);
    }
    else {
      $qname = $graph->uri_to_qname($uri);
      if ($qname != null) {
        $m = split(':', $qname);  
        return '<span class="prefix">' . htmlspecialchars($m[0]) . ':</span><span class="localname">' . htmlspecialchars($m[1]) . '</span>';
      }  
    }
    return $uri;
  }
  
  function format_property_label($property, $label, $graph, $link_property  = TRUE) {
    $ret = '';
        
    if ($link_property) {
      $ret .= $this->link_uri($property, $label, $graph);
    }
    else {
      if ($label) {
        $ret .= htmlspecialchars($label);
      }
      else {
        $ret .= $this->make_labelled_uri($property, $graph);    
      }
    }
    return $ret;
  }
  
  function get_bag_items($resource_uri, $bag_property, $graph) {
    $items = array();
    $lists = $graph->get_resource_triple_values($resource_uri, $bag_property );
    foreach ($lists as $list) {

      $data = array();
      $props = $graph->get_subject_properties($list, TRUE);
      $list_items = array();
      foreach ($props as $prop) {
        if ( preg_match("~^http://www.w3.org/1999/02/22-rdf-syntax-ns#_(\d+)$~", $prop, $m)) {
          $list_items[$m[1]] = array('property' => $prop, 'values' => $graph->get_subject_property_values($list, $prop));
        }
      }
    
      foreach ($list_items as $number => $values) {
        $items[]= $this->format_property_values($values['property'], $values['values'], $graph);
      }
    }
    
    return $items;
  }
  
  function page_about_resource($resource_uri) {
    $uri = $resource_uri;
    if (preg_match('~http://([^/]+)/~i', $resource_uri, $m)) {
      if ( $_SERVER["HTTP_HOST"] == $m[1] . '.local' ) {
        $uri = str_replace($m[1], $_SERVER["HTTP_HOST"], $resource_uri) . '.html';
      }
      else if ( $_SERVER["HTTP_HOST"] == $m[1] ) {
        $uri .= '.html';
      }
      else if ( 'bl.dataincubator.org' == $m[1] ) {
        if ( strpos($_SERVER["HTTP_HOST"], '.local') !== FALSE ) {
          $uri = str_replace('bl.dataincubator.org', 'semanticlibrary.org.local', $uri) . '.html';
        }
        else {
          $uri = str_replace('bl.dataincubator.org', 'semanticlibrary.org', $uri) . '.html';
        }
      }
    }
    return $uri;    
  }

  function request_uri_to_resource_uri($request_uri) {
    return $request_uri;    
  }  
  
  
  function make_item_row($title, $link, $description, $image = '', $meta_html= '') {
    $html = '<div class="itemrow">';
    $html .= '<div class="span-2 colborder row-image">';
    if ($link) {
      $html .= '<a href="' . htmlspecialchars($link) . '"><img src="' . htmlspecialchars($image) . '" /></a>';
    }
    else {
      $html .= '<img src="' . htmlspecialchars($image) . '" />';
    }
    $html .= '</div>';
    $html .= '<div class="span-11 last">';
    if ($title) {
      if ($link) {
        $html .= '<h4><a href="' . htmlspecialchars($link) . '">' . character_limiter(htmlspecialchars($title), 35) . '</a></h4>';
      }
      else {
        $html .= '<h4>' . character_limiter(htmlspecialchars($title), 35) . '</h4>';
      }
    }
    if ($description) {
      $html .= '<div class="desc">' . word_limiter(htmlspecialchars($description), 18) . '</div>';
    }
    else {
      $html .= '<div class="desc"><br><br></div>';
    }
    if ($meta_html) {
      $html .= '<div class="meta">' . $meta_html . '</div>';
    }
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<hr class="space">';
    return $html;
    
  }
  
  
  function make_panel($title, $content) {
    $html = '<div class="panel">';
    if ($title) {
      $html .= '<h3>' . htmlspecialchars($title) . '</h3>';
    }
    $html .= '<div class="panel-content">' . $content . '</div>';
    $html .= '</div>';
    return $html;
  }
  
  function make_feature($content, $image = '') {
    $html = '<div class="feature">';
    $html .= '<div class="content">' . $content . '</div>';
    $html .= '</div>';
    return $html;
  }
}

class FeatureWidget extends Widget {
  
  function __construct($title = '') {
    parent::__construct($title);
    $this->add_properties_used(
        array(
          'http://www.w3.org/1999/02/22-rdf-syntax-ns#type'
         , 'http://www.w3.org/2004/02/skos/core#prefLabel'
         , 'http://www.w3.org/2000/01/rdf-schema#label'
         , 'http://open.vocab.org/terms/subtitle'
         , 'http://purl.org/dc/terms/title'
         , 'http://purl.org/dc/elements/1.1/title'
        ) );
  }

  function render($resource_uri, $available_properties, $graph, $class = 'panel') {
    return $this->make_feature($this->get_content($resource_uri, $available_properties, $graph));
  }

}



class GroupedWidget extends Widget {
  var $groups = array(  array( 'name' => '',
    'id' => 'pdefault',
    'class' => '',
    'properties'=> array(
       'http://www.w3.org/2004/02/skos/core#prefLabel'
      ,'http://www.w3.org/2000/01/rdf-schema#label'
      ,'http://purl.org/dc/terms/title'
      ,'http://purl.org/dc/elements/1.1/title' 
      ,'http://xmlns.com/foaf/0.1/name' 
      ,'http://www.ordnancesurvey.co.uk/ontology/AdministrativeGeography/v2.0/AdministrativeGeography.rdf#hasOfficialName'
      ,'http://www.ordnancesurvey.co.uk/ontology/AdministrativeGeography/v2.0/AdministrativeGeography.rdf#hasName'
      ,'http://www.w3.org/2006/vcard/ns#label'
      ,'http://www.w3.org/2004/02/skos/core#definition'
      ,'http://www.w3.org/2004/02/skos/core#scopeNote'
      ,'http://open.vocab.org/terms/subtitle'
      ,'http://purl.org/ontology/po/medium_synopsis'
      ,'http://www.w3.org/2000/01/rdf-schema#comment'
      ,'http://purl.org/dc/terms/description'
      ,'http://purl.org/dc/elements/1.1/description'
      ,'http://open.vocab.org/terms/firstSentence'
      ,'http://purl.org/stuff/rev#text'
      ,'http://purl.org/dc/terms/creator'
      ,'http://purl.org/dc/elements/1.1/creator'
      ,'http://purl.org/dc/terms/contributor'
      ,'http://purl.org/dc/elements/1.1/contributor'
      ,'http://xmlns.com/foaf/0.1/depiction'
      ,'http://xmlns.com/foaf/0.1/img'
      ,'http://xmlns.com/foaf/0.1/logo'
    ) )

  , array( 'name' => 'Biograhical Information',
    'id' => 'pbio',
    'class' => '',
    'properties'=> array(
      'http://xmlns.com/foaf/0.1/title', 
      'http://xmlns.com/foaf/0.1/givenname', 
      'http://xmlns.com/foaf/0.1/firstName', 
      'http://xmlns.com/foaf/0.1/surname', 
      'http://purl.org/vocab/bio/0.1/olb', 
      'http://purl.org/vocab/bio/0.1/event',
      'http://purl.org/vocab/relationship/childOf',
      'http://purl.org/vocab/relationship/parentOf',
      'http://purl.org/vocab/relationship/ancestorOf',
      'http://purl.org/vocab/relationship/descendantOf',
      'http://purl.org/vocab/relationship/grandchildOf',
      'http://purl.org/vocab/relationship/grandparentOf',
      'http://purl.org/vocab/relationship/lifePartnerOf',
      'http://purl.org/vocab/relationship/siblingOf',
      'http://purl.org/vocab/relationship/spouseOf'
    ) )

  , array( 'name' => 'Contact Details',
    'id' => 'pcontact',
    'class' => '',
    'properties'=> array(
      'http://xmlns.com/foaf/0.1/phone', 
      'http://xmlns.com/foaf/0.1/mbox', 
      'http://rdfs.org/sioc/ns#email', 
      'http://xmlns.com/foaf/0.1/icqChatID', 
      'http://xmlns.com/foaf/0.1/msnChatID', 
      'http://xmlns.com/foaf/0.1/aimChatID', 
      'http://xmlns.com/foaf/0.1/jabberID', 
      'http://xmlns.com/foaf/0.1/yahooChatID'
    ) )

  , array( 'name' => 'Aliases and Alternate Names',
    'id' => 'paliases',
    'class' => '',
    'properties'=> array(
      'http://xmlns.com/foaf/0.1/nick', 
      'http://www.w3.org/2004/02/skos/core#altLabel',
      'http://purl.org/net/schemas/space/alternateName',
      'http://purl.org/ontology/bibo/shortTitle'
      ,'http://www.ordnancesurvey.co.uk/ontology/AdministrativeGeography/v2.0/AdministrativeGeography.rdf#hasVernacularName'
      ,'http://www.ordnancesurvey.co.uk/ontology/AdministrativeGeography/v2.0/AdministrativeGeography.rdf#hasBoundaryLineName'
    ) )

  , array( 'name' => 'Employment',
    'id' => 'pemployment',
    'class' => '',
    'properties'=> array(
      'http://xmlns.com/foaf/0.1/workplaceHomepage',
      'http://purl.org/vocab/relationship/employedBy',
      'http://purl.org/vocab/relationship/employerOf',
      'http://purl.org/vocab/relationship/worksWith'
    ) )

  , array( 'name' => 'Leadership',
    'id' => 'pleadership',
    'class' => '',
    'properties'=> array(
       'http://dbpedia.org/property/leaderName'
    ) )




  , array( 'name' => 'Education',
    'id' => 'peducation',
    'class' => '',
    'properties'=> array(
      'http://xmlns.com/foaf/0.1/schoolHomepage'
    ) )

  , array( 'name' => 'Location',
    'id' => 'pgeo',
    'class' => '',
    'properties'=> array(
       'http://open.vocab.org/terms/regionalContextMap'
      ,'http://open.vocab.org/terms/nationalContextMap'
      ,'http://schemas.talis.com/2005/address/schema#streetAddress'
      ,'http://schemas.talis.com/2005/address/schema#localityName'
      ,'http://schemas.talis.com/2005/address/schema#regionName'
      ,'http://schemas.talis.com/2005/address/schema#postalCode'
      ,'http://www.gazettes-online.co.uk/ontology/location#hasAddress'
      ,'http://www.w3.org/2003/01/geo/wgs84_pos#lat'
      ,'http://www.w3.org/2003/01/geo/wgs84_pos#long'
      ,'http://www.w3.org/2003/01/geo/wgs84_pos#lat_long'
      ,'http://www.w3.org/2003/01/geo/wgs84_pos#altitude'
      ,'http://dbpedia.org/ontology/elevation'
      ,'http://xmlns.com/foaf/0.1/based_near'
      ,'http://www.w3.org/2003/01/geo/wgs84_pos#location'
      ,'http://www.w3.org/2000/10/swap/pim/contact#nearestAirport'
      ,'http://purl.org/net/schemas/space/country'
      ,'http://purl.org/net/schemas/space/place'
      ,'http://purl.org/vocab/bio/0.1/place'
      ,'http://www.ordnancesurvey.co.uk/ontology/AdministrativeGeography/v2.0/AdministrativeGeography.rdf#borders'
    ) )



  , array( 'name' => 'Subjects and Classifications',
    'id' => 'psubjects',
    'class' => '',
    'properties'=> array(
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#type'
      ,'http://purl.org/dc/terms/subject'
      ,'http://purl.org/dc/elements/1.1/subject'
      ,'http://www.w3.org/2004/02/skos/core#subject'
      ,'http://purl.org/ontology/po/genre'
      ,'http://olrdf.appspot.com/key/dewey_decimal_class'
      ,'http://olrdf.appspot.com/key/lc_classification'
      ,'http://purl.org/dc/terms/LCC'
      ,'http://xmlns.com/foaf/0.1/topic'
      ,'http://rdfs.org/sioc/ns#topic'
      ,'http://www.w3.org/2004/02/skos/core#broader'
      ,'http://www.w3.org/2004/02/skos/core#narrower'
      ,'http://www.w3.org/2004/02/skos/core#closeMatch'
      ,'http://www.w3.org/2004/02/skos/core#inScheme'
      ,'http://purl.org/ontology/po/format'
      ,'http://schemas.talis.com/2006/recordstore/schema#tags'
    ) )


  , array( 'name' => 'Publication Facts',
    'id' => 'ppublication',
    'class' => '',
    'properties'=> array(
      'http://purl.org/ontology/bibo/edition' 
      ,'http://purl.org/dc/terms/publisher'
      ,'http://purl.org/dc/elements/1.1/publisher'
      ,'http://rdvocab.info/Elements/placeOfPublication'
      ,'http://olrdf.appspot.com/key/publish_country'
      ,'http://purl.org/dc/terms/issued'
      ,'http://www.gazettes-online.co.uk/ontology#hasPublicationDate'
    ) )

  , array( 'name' => 'Citation',
    'id' => 'pcitation',
    'class' => '',
    'properties'=> array(
      'http://purl.org/dc/terms/isPartOf'
      ,'http://purl.org/ontology/bibo/volume'
      ,'http://purl.org/ontology/bibo/issue'
      ,'http://purl.org/ontology/bibo/pageStart'
      ,'http://purl.org/ontology/bibo/pageEnd'
      ,'http://www.gazettes-online.co.uk/ontology#hasIssueNumber'
      ,'http://www.gazettes-online.co.uk/ontology#hasEdition'
    ) )



   , array( 'name' => 'Dataset',
    'id' => 'pdataset',
    'class' => '',
    'properties'=> array(
      'http://rdfs.org/ns/void#exampleResource',
      'http://rdfs.org/ns/void#sparqlEndpoint',
      'http://rdfs.org/ns/void#uriLookupEndpoint',
      'http://rdfs.org/ns/void#subset',
      'http://rdfs.org/ns/void#vocabulary',
      'http://rdfs.org/ns/void#uriRegexPattern'
    ) )

  , array( 'name' => 'Physical Dimensions',
    'id' => 'pformat',
    'class' => '',
    'properties'=> array(
      'http://purl.org/dc/terms/medium',
      'http://open.vocab.org/terms/numberOfPages',
      'http://olrdf.appspot.com/key/pagination',
      'http://olrdf.appspot.com/key/physical_dimensions',
      'http://open.vocab.org/terms/weight',
      'http://purl.org/net/schemas/space/mass'
      ,'http://www.ordnancesurvey.co.uk/ontology/AdministrativeGeography/v2.0/AdministrativeGeography.rdf#hasArea'
      
    ) )

  , array( 'name' => 'Identifers',
    'id' => 'pidentifiers',
    'class' => '',
    'properties'=> array(
       'http://purl.org/dc/terms/identifier'
      ,'http://purl.org/dc/elements/1.1/identifier'
      ,'http://purl.org/ontology/bibo/isbn10'
      ,'http://purl.org/ontology/bibo/isbn13'
      ,'http://purl.org/ontology/bibo/lccn'
      ,'http://purl.org/ontology/bibo/oclcnum'
      ,'http://purl.org/ontology/bibo/doi'
      ,'http://purl.org/ontology/bibo/uri'
      ,'http://purl.org/ontology/bibo/issn'
      ,'http://purl.org/ontology/bibo/eissn'
      ,'http://xmlns.com/foaf/0.1/mbox_sha1sum'
      ,'http://xmlns.com/foaf/0.1/openid'
      ,'http://purl.org/net/schemas/space/internationalDesignator'
      ,'http://www.daml.org/2001/10/html/airport-ont#icao'
      ,'http://www.daml.org/2001/10/html/airport-ont#iata'
      ,'http://rdfs.org/sioc/ns#id'
      ,'http://purl.org/vocab/aiiso/schema#code'
      ,'http://dbpedia.org/property/iata'
      ,'http://dbpedia.org/property/icao'
      ,'http://www.ordnancesurvey.co.uk/ontology/AdministrativeGeography/v2.0/AdministrativeGeography.rdf#hasCensusCode'
      ,'http://schemas.talis.com/2005/dir/schema#etag'
    ) )

  , array( 'name' => 'Further Information',
    'id' => 'pfurther',
    'class' => '',
    'properties'=> array(
      'http://xmlns.com/foaf/0.1/homepage',
      'http://xmlns.com/foaf/0.1/page',
      'http://xmlns.com/foaf/0.1/weblog',
      'http://purl.org/ontology/po/microsite',
      'http://purl.org/ontology/mo/wikipedia',
      'http://rdfs.org/sioc/ns#feed',
      'http://www.w3.org/2000/01/rdf-schema#seeAlso',
      'http://www.w3.org/2002/07/owl#sameAs',
      'http://xmlns.com/foaf/0.1/isPrimaryTopicOf'
    ) )

  , array( 'name' => 'Members',
    'id' => 'pmembers',
    'class' => '',
    'properties'=> array(
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_1',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_2',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_3',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_4',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_5',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_6',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_7',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_8',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_9',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_10',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_11',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_12',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_13',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_14',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_15',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_16',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_17',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_18',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_19',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_20',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_21',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_22',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_23',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_24',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_25',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_26',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_27',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_28',
      'http://www.w3.org/1999/02/22-rdf-syntax-ns#_29'
    ) )




  , array( 'name' => 'Provenance',
    'id' => 'pprovenance',
    'class' => '',
    'properties'=> array(
      'http://purl.org/dc/terms/created',
      'http://purl.org/dc/terms/modified',
      'http://purl.org/dc/terms/source', 
      'http://purl.org/dc/elements/1.1/source', 
      'http://purl.org/dc/terms/coverage',
      'http://purl.org/dc/elements/1.1/coverage'
    ) )


  , array( 'name' => 'Legal Information',
    'id' => 'plegal',
    'class' => '',
    'properties'=> array(
      'http://purl.org/dc/terms/rights', 
      'http://purl.org/dc/elements/1.1/rights', 
      'http://purl.org/dc/terms/license',
      'http://creativecommons.org/ns#license'
    ) )

  );

  function can_display($resource_uri, $available_properties, $graph) {
    return TRUE;
  }

  function render($resource_uri, $available_properties, $graph, $class = 'section') {


    $index = $graph->get_index();
    $content = '';
    if (array_key_exists($resource_uri, $index)) {
     
      foreach ($this->groups as $group) {
        $properties = array_intersect($available_properties, $group['properties']);
        
        
        if (count($properties) > 0) {
          $content .= '<div class="' . htmlspecialchars($class) . '">';
          $content .= '<h3>' . htmlspecialchars($group['name']) . '</h3>';
          $content .= $this->format_property_value_list($resource_uri, $properties, $graph);
          $content .= '</div>';
          $this->add_properties_used($properties);
        }
      }

      $content .= parent::render($resource_uri, array_diff($available_properties, $this->properties_used), $graph, $class);
    }

    return $content;
  }
  


}



class TocWidget extends Widget {
  function get_title() {
    return "Table of Contents";
  }
  
  function get_properties_used() {
    return array('http://purl.org/dc/terms/tableOfContents', 'http://purl.org/dc/terms/hasPart');
  }

  function can_display($resource_uri, $available_properties, $graph) {
    return in_array('http://purl.org/dc/terms/tableOfContents', $available_properties);
  }                        

  function get_content($resource_uri, $available_properties, $graph) {
    $html = '';
    

    $tocs = $graph->get_resource_triple_values($resource_uri, 'http://purl.org/dc/terms/tableOfContents');
    foreach ($tocs as $toc) {

      $data = array();
      $props = $graph->get_subject_properties($toc, TRUE);
      $list_items = array();
      foreach ($props as $prop) {
        if ( preg_match("~^http://www.w3.org/1999/02/22-rdf-syntax-ns#_(\d+)$~", $prop, $m)) {
          $list_items[$m[1]] = $graph->get_subject_property_values($toc, $prop);
        }
      }
    
      ksort($list_items, SORT_NUMERIC);
      $html .= '<ol>';
      foreach ($list_items as $number => $values) {
        //foreach ($values['values'] as $value_info) {
          $html .= '<li>';
          for ($i = 0; $i < count($values); $i++) {
            if ($values[$i]['type'] != 'literal') {
              $html .= htmlspecialchars($graph->get_label($values[$i]['value']));
            }
            else {
              $html .= htmlspecialchars($values[$i]['value']);
            }
          }   
          $html .= '</li>' . "\n";
        //}
      }
      $html .= '</ol>';
    }

    return $html;
  }
}



class BagWidget extends Widget {
  var $bag_property;
  var $member_property;
  function __construct($title, $bag_property, $member_property = null) {
    parent::__construct($title);
    $this->bag_property = $bag_property;
    $this->member_property = $member_property;
  }
  
  function get_properties_used() {
    return array($this->bag_property, $this->member_property);
  }
    
  function can_display($resource_uri, $available_properties, $graph) {
    return in_array($this->bag_property, $available_properties);
  }                        
  
  function get_content($resource_uri, $available_properties, $graph) {
    $html = '';
    $items = $this->get_bag_items($resource_uri, $this->bag_property, $graph);
    if ( count($items) > 0 ) {
    
      $html .= '<ul>';
      foreach ($items as $item) {
        $html .= '<li>'. $item . '</li>';
      }
      $html .= '</ul>';
    }

    return $html;
  }

}

class EditionListWidget extends Widget {
  function __construct() {
    parent::__construct('Known Editions');
  }
  function get_properties_used() {
    return array('http://purl.org/dc/terms/hasVersion');
  }

  function can_display($resource_uri, $available_properties, $graph) {
    return in_array('http://purl.org/dc/terms/hasVersion', $available_properties);
  }          

  function get_content($resource_uri, $available_properties, $graph) {
    $html = '';
    $editions = $graph->get_resource_triple_values($resource_uri, 'http://purl.org/dc/terms/hasVersion' );
    if (count($editions) > 0) {
    
      $edition_list = array();
      
      foreach ($editions as $edition) {
        $publisher_name = $graph->get_first_literal($edition, 'http://purl.org/dc/terms/publisher', '');
        $issued = $graph->get_first_literal($edition, 'http://purl.org/dc/terms/issued', '');
        $publication_place_name = $graph->get_first_literal($edition, 'http://rdvocab.info/Elements/placeOfPublication', '');
        $language = $graph->get_first_literal($edition, 'http://purl.org/dc/terms/language', '');
        $isbn13 = $graph->get_first_literal($edition, 'http://purl.org/ontology/bibo/isbn13', '');
        
        $pub_info = '';
        $pub_info .= $isbn13 ? '<img src="http://prism.talis.com/broadminster/imageservice.php?id=' . htmlspecialchars($isbn13) . '"/>' : '';
        
        $pub_info .= $publication_place_name ? htmlspecialchars($publication_place_name) . ':' : '';
        $pub_info .= $publisher_name ? ' ' . htmlspecialchars($publisher_name) : '';
        $pub_info .= $issued && $publisher_name ? ',' : '';
        $pub_info .= $issued ? ' ' . htmlspecialchars($issued) : '';
        $pub_info .= $language ? ' (' . htmlspecialchars($language) . ')' : '';
        $pub_info .= $isbn13 ? ', ISBN ' . htmlspecialchars($isbn13) . '' : '';

        $year = $issued;
        if (preg_match('~(\d\d\d\d)~', $issued, $m)) {
          $year = $m[1];
        }
        
        
        $edition_list[$year . '~' . $edition] = $this->link_uri($edition, $pub_info, $graph);

      }
      krsort($edition_list);
      
      $html .= '<ul><li>';
      $html .= join("</li>\n<li>", $edition_list);
      $html .= '</li></ul>';
    }
    return $html;
  }

}


class RelatedWorksWidget extends Widget {
  var $rev_property;
  function __construct($title = 'Works by this author', $rev_property = 'http://purl.org/dc/terms/creator' ) {
    parent::__construct($title);
    $this->rev_property = $rev_property;
  }
  function get_properties_used() {
    return array();
  }

  function can_display($resource_uri, $available_properties, $graph) {
    $index = $graph->get_index();
    foreach ($index as $s => $p_list) {
      if ($graph->has_resource_triple($s, $this->rev_property, $resource_uri) ) {
        return TRUE;
      }
    }
    return FALSE;
  }          

  function get_content($resource_uri, $available_properties, $graph) {
    $html = '';
    $works = array();
    $index = $graph->get_index();
    foreach ($index as $s => $p_list) {
      if ($graph->has_resource_triple($s, $this->rev_property, $resource_uri) ) {
        $works[] = $s;
      }
    }
   
    if (count($works) > 0) {
     
      //$html .= '<p><a href="' . htmlspecialchars($resource_uri . '/works/1') . '">All works</a></p>';
      $html .= '<ul>';
      foreach ($works as $work) {
        $html .= '<li>' . $this->link_uri($work, '', $graph) .'</li>';
      }
      $html .= '</ul>';
      
      
    }
    return $html;
  }

}


class WorkSubjectsWidget extends Widget {
  
  function __construct($title = 'Subjects') {
    parent::__construct($title);
    $this->add_properties_used(
            array(
                'http://purl.org/dc/terms/subject',
               'http://schemas.semanticlibrary.org/misc/terms/genre',
               ));
      
  }
  
  function can_display($resource_uri, $available_properties, $graph) {
    return in_array('http://purl.org/dc/terms/subject', $available_properties);
  }


  function get_content($resource_uri, $available_properties, $graph) {
    $html = '';
        
    $genres = $graph->get_resource_triple_values($resource_uri, 'http://schemas.semanticlibrary.org/misc/terms/genre');
    $formatted_genres = array();
    foreach ($genres as $genre) {
      $label = htmlspecialchars($graph->get_label($genre));
      $label = str_replace('--', ' &mdash; ', $label);
      $formatted_genres[] = '<a href="' . $this->page_about_resource($genre) . '">' . $label . '</a>';
    }
    
    
    if (count($genres) > 0) {
      $html .= '<h4>Genres:</h4>';
      $html .= '<ul id="work-genres"><li>';
      $html .= join('</li><li>', $formatted_genres );
      $html .= '</li></ul>';
    }


    $subjects = $graph->get_resource_triple_values($resource_uri, 'http://purl.org/dc/terms/subject');
    $formatted_subjects = array();
    foreach ($subjects as $subject) {
      $label = htmlspecialchars($graph->get_label($subject));
      $label = str_replace('--', ' &mdash; ', $label);
      $formatted_subjects[] = '<a href="' . $this->page_about_resource($subject) . '">' . $label . '</a>';
    }
    
    
    if (count($subjects) > 0) {
      $html .= '<h4>Subjects:</h4>';
      $html .= '<ul id="work-subjects"><li>';
      $html .= join('</li><li>', $formatted_subjects );
      $html .= '</li></ul>';
    }

    return $html;
  }  
}



class LinksWidget extends Widget {
  
  function __construct($title = 'Links') {
    parent::__construct($title);
    $this->add_properties_used(
            array(
                'http://www.w3.org/2000/01/rdf-schema#seeAlso',
                'http://xmlns.com/foaf/0.1/isPrimaryTopicOf',
                'http://xmlns.com/foaf/0.1/homepage',
               ));
      
  }
  
  function can_display($resource_uri, $available_properties, $graph) {
    return (
             in_array('http://www.w3.org/2000/01/rdf-schema#seeAlso', $available_properties)
          || in_array('http://xmlns.com/foaf/0.1/isPrimaryTopicOf', $available_properties)
          || in_array('http://xmlns.com/foaf/0.1/homepage', $available_properties)
            );
  }


  function get_content($resource_uri, $available_properties, $graph) {
    $html = '';

    $html .= $this->make_link_group($resource_uri, 'http://xmlns.com/foaf/0.1/isPrimaryTopicOf', "", $graph);
    $html .= $this->make_link_group($resource_uri, array('http://xmlns.com/foaf/0.1/homepage', 'http://dbpedia.org/property/website'), "Homepage", $graph);
    $html .= $this->make_link_group($resource_uri, 'http://www.w3.org/2000/01/rdf-schema#seeAlso', "General information", $graph);

    return $html;
  }  

  function make_link_group($resource_uri, $properties, $heading, $graph) {
    $ret = '';
    if (! is_array($properties) ) {
      $properties = array($properties);
    }
    $values = array();
    foreach ($properties as $property) {
      $values = array_merge($values, $graph->get_resource_triple_values($resource_uri, $property));
    }
    $values = array_unique($values);
    sort($values);
    $domain_counts = array();
    
    if (count($values) > 0) {
      $value_list = array();
      foreach  ($values as $uri) {
        $domain = parse_url($uri, PHP_URL_HOST);
        if ($domain == 'openlibrary.org') {
          $label = 'Open Library';
        }
        elseif ($domain == 'en.wikipedia.org') {
          $label = 'Wikipedia';
        }
        elseif ($domain == 'www.librarything.com') {
          $label = 'LibraryThing';
        }
        elseif ($domain == 'amazon.com') {
          $label = 'Amazon';
        }
        elseif ($domain == 'amazon.co.uk') {
          $label = 'Amazon (UK)';
        }
        elseif ($domain == 'lccn.loc.gov') {
          $label = 'Library of Congress';
        }        
        elseif ($domain == 'www.worldcat.org') {
          $label = 'Worldcat';
        }        
        else {
          $label = $domain;
        }
        if (array_key_exists($domain, $domain_counts)) {
          $domain_counts[$domain] = $domain_counts[$domain] + 1;
          $label .= ' ('. $domain_counts[$domain] . ')';
        }
        else {
          $domain_counts[$domain] = 1;
        }
                
        $html = '<a href="' .htmlspecialchars($uri) . '">' . htmlspecialchars($label) . '</a>';
        $value_list[] = $html;
      }
      
      $ret .= "\n<div class=\"propgroup\">\n";
      if ($heading) {
        $ret .= '  <h4>' . $heading . '</h4>' . "\n";
      }
      $ret .= "  <ul><li>";
      $ret .= join("</li>\n  <li>", $value_list);
      $ret .= "</li></ul>\n</div>\n";
    }

    return $ret;
  }
}


class LinkedDataWidget extends Widget {
  var $show_sameas;
  
  function __construct($title = 'Linked Data', $show_sameas = FALSE) {
    parent::__construct($title);
    $this->show_sameas = $show_sameas;
    $this->add_properties_used(
            array(
                'http://www.w3.org/2002/07/owl#sameAs'
               ));
      
  }
  
  function can_display($resource_uri, $available_properties, $graph) {
    return TRUE;
  }

  function get_content($resource_uri, $available_properties, $graph) {
    $content = '';
    $content .= '<p>This page is also available as Linked Data: ';
    $content .= '<a href="' . htmlspecialchars($resource_uri) . '.rdf">RDF</a>';
    $content .= ', <a href="' . htmlspecialchars($resource_uri) . '.turtle">Turtle</a>';
    $content .= ' or <a href="' . htmlspecialchars($resource_uri) . '.json">JSON</a>.';
    $content .= '</p>';
    $content .= '<p id="uri">Linked Data URI: <a href="' . htmlspecialchars($resource_uri) . '">' . htmlspecialchars($resource_uri) . '</a></p>';
    if ($this->show_sameas) {
      $content .= $this->render_property_group($resource_uri, 'http://www.w3.org/2002/07/owl#sameAs', "Same as", $graph, 'dl');
    }

    return $content;
  }  
}


class RelatedEditionsWidget extends Widget {
  var $rev_property;
  function __construct($title, $rev_property ) {
    parent::__construct($title);
    $this->rev_property = $rev_property;
  }
  function get_properties_used() {
    return array();
  }

  function can_display($resource_uri, $available_properties, $graph) {
    $index = $graph->get_index();
    foreach ($index as $s => $p_list) {
      if ($graph->has_resource_triple($s, $this->rev_property, $resource_uri) ) {
        return TRUE;
      }
    }
    return FALSE;
  }          

  function get_content($resource_uri, $available_properties, $graph) {
    $html = '';
    $items = array();
    $index = $graph->get_index();
    foreach ($index as $s => $p_list) {
      if ($graph->has_resource_triple($s, $this->rev_property, $resource_uri) ) {
        $items[] = $s;
      }
    }
   
    if (count($items) > 0) {
      $html .= '<p><a href="' . htmlspecialchars($resource_uri . '/items/1') . '">All editions</a></p>';
      foreach ($items as $item) {
        
        $isbn13 = $graph->get_first_literal($item, 'http://purl.org/ontology/bibo/isbn13', '');
        if ($isbn13) {
          $image = 'http://prism.talis.com/broadminster/imageservice.php?id=' . urlencode($isbn13);
        }
        else {
          $image = 'http://prism.talis.com/broadminster/imageservice.php?id=0000000000000';
        }
        
        $html .= $this->make_item_row($this->make_labelled_uri($item, $graph), $item, get_edition_label($item, $graph), $image);
        
//        $html .= '<li>' . $this->link_uri($item, '', $graph) .'<br />' . get_edition_label($item, $graph) . '</li>';
        
//        $html .= '<hr class="space">';
      }
      
      
    }
    return $html;
  }



}


class EditionTableWidget extends EditionListWidget {

  function get_content($resource_uri, $available_properties, $graph) {
    $html = '';
    $editions = $graph->get_resource_triple_values($resource_uri, 'http://purl.org/dc/terms/hasVersion' );
    if (count($editions) > 0) {
    
      $edition_list = array();
      
      foreach ($editions as $edition) {



        $issued = $graph->get_first_literal($edition, 'http://purl.org/dc/terms/issued', '');

        $year = $issued;
        if (preg_match('~(\d\d\d\d)~', $issued, $m)) {
          $year = $m[1];
        }

        $isbn13 = $graph->get_first_literal($edition, 'http://purl.org/ontology/bibo/isbn13', '');
        if ($isbn13) {
          $image = 'http://prism.talis.com/broadminster/imageservice.php?id=' . urlencode($isbn13);
        }
        else {
          $image = '/images/noimage.jpg';
        }
        
        
        
        $edition_list[$year . '~' . $edition] = $this->make_item_row($graph->get_label($edition), $this->page_about_resource($edition), get_edition_label($edition, $graph), $image);

      }
      krsort($edition_list);
      
      $i = 0;
      foreach ($edition_list as $key => $value) {
        $html .= $value;
      }
    }
    return $html;
  }

}

class IdentifiersWidget extends Widget {
  var $properties = array(
       'http://purl.org/dc/terms/identifier'
      ,'http://purl.org/dc/elements/1.1/identifier'
      ,'http://purl.org/ontology/bibo/isbn10'
      ,'http://purl.org/ontology/bibo/isbn13'
      ,'http://purl.org/ontology/bibo/lccn'
      ,'http://purl.org/ontology/bibo/oclcnum'
      ,'http://purl.org/ontology/bibo/doi'
      ,'http://purl.org/ontology/bibo/uri'
      ,'http://purl.org/ontology/bibo/issn'
      ,'http://purl.org/ontology/bibo/eissn'
      ,'http://xmlns.com/foaf/0.1/mbox_sha1sum'
      ,'http://xmlns.com/foaf/0.1/openid'
      ,'http://purl.org/net/schemas/space/internationalDesignator'
      ,'http://www.daml.org/2001/10/html/airport-ont#icao'
      ,'http://www.daml.org/2001/10/html/airport-ont#iata'
      ,'http://rdfs.org/sioc/ns#id'
      ,'http://purl.org/vocab/aiiso/schema#code'
      ,'http://dbpedia.org/property/iata'
      ,'http://dbpedia.org/property/icao'
      ,'http://www.ordnancesurvey.co.uk/ontology/AdministrativeGeography/v2.0/AdministrativeGeography.rdf#hasCensusCode'
      ,'http://schemas.talis.com/2005/dir/schema#etag'
    );
  function __construct($title = 'Identifiers') {
    parent::__construct($title);
    $this->add_properties_used( $this->properties );
  }
  
  function can_display($resource_uri, $available_properties, $graph) {
    return (count(array_intersect($this->properties, $available_properties)) > 0);
  }
  
  
  function get_content($resource_uri, $available_properties, $graph) {
    return $this->format_property_value_list($resource_uri, array_intersect($this->properties, $available_properties) , $graph, 'inline', FALSE);
  }

}


class TextWidget extends Widget {
  
  function __construct($title, $text) {
    parent::__construct($title);
  }

  function get_content($resource_uri, $available_properties, $graph) {
    return '<p>' . $text . '</p>';
  }
}

class HoldingsWidget extends Widget {
  var $holdings_results_uri;
  
  function __construct($holdings_results_uri, $title = 'Library collections containing this item') {
    parent::__construct($title);
    $this->holdings_results_uri = $holdings_results_uri;
  }
  
  function can_display($resource_uri, $available_properties, $graph) {
    $values = $graph->get_subject_property_values($this->holdings_results_uri, 'http://purl.org/rss/1.0/items');
    foreach ($values as $value_info) {
      if ($value_info['type'] == 'uri') {
        $items = $graph->get_subject_property_values($value_info['value'], 'http://www.w3.org/1999/02/22-rdf-syntax-ns#_1');
        if (count($items) > 0) return TRUE;
      }
    }
    return FALSE;

  }
  
  
  function get_content($resource_uri, $available_properties, $graph) {
    $content = '';
    $values = $graph->get_subject_property_values($this->holdings_results_uri, 'http://purl.org/rss/1.0/items');
    foreach ($values as $value_info) {
      if ($value_info['type'] == 'uri') {
        $props = $graph->get_subject_properties($value_info['value'], TRUE);
        
        $list_items = array();
        foreach ($props as $prop) {
          if ( preg_match("~^http://www.w3.org/1999/02/22-rdf-syntax-ns#_(\d+)$~", $prop, $m)) {
            $items = $graph->get_resource_triple_values($value_info['value'], $prop);
            foreach ($items as $item_uri) {
              $name = $graph->get_first_literal($item_uri, 'http://schemas.talis.com/2005/holdings/schema#collectionName');
              $id = $graph->get_first_literal($item_uri, 'http://schemas.talis.com/2005/holdings/schema#collectionIdentifier');
              $deep_link = $graph->get_first_literal($item_uri, 'http://schemas.talis.com/2005/holdings/schema#deepLink');
              
              $name = str_replace('&amp;', '&', $name);
              $id_uri = 'http://semanticlibrary.org/collections/' . str_replace('.inst', '', $id);
              
              $item_content = '<a href="' . $this->page_about_resource($id_uri) . '">' .  htmlspecialchars($name) . '</a>';
              
              if ($deep_link) {
                $item_content .= ' (<a href="' . htmlspecialchars($deep_link) . '">Find in library</a>)';
              }
                            
              
              $list_items[$name] = $item_content;
            }
          }
        }

        if (count($list_items) > 0) {
          ksort($list_items);
          $content .= "\n<ul>\n  <li>";
          $content .= join("</li>\n  <li>", $list_items);
          $content .= "</li>\n</ul>\n";
        }

      }
    }
    return $content;

  }
}



class EditionWidget extends FeatureWidget {
  
  function __construct($title = 'About this edition') {
    parent::__construct($title);
    $this->add_properties_used(
          array('http://purl.org/dc/terms/isVersionOf'
               , 'http://semanticlibrary.org/terms/by_statement'
               , 'http://rdvocab.info/Elements/publishersName'
               , 'http://purl.org/dc/terms/title'
               , 'http://www.w3.org/2004/02/skos/core#prefLabel'
               , 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type'
               , 'http://rdvocab.info/Elements/placeOfPublication'
               , 'http://purl.org/dc/terms/issued'
               , 'http://open.vocab.org/terms/numberOfPages'
               , 'http://www.w3.org/2004/02/skos/core#note'
               , 'http://purl.org/dc/terms/abstract'
               , 'http://purl.org/dc/terms/medium'
               , 'http://semanticlibrary.org/terms/physical_dimensions'
               , 'http://open.vocab.org/terms/weight'
               , 'http://purl.org/dc/terms/publisher'
               , 'http://purl.org/ontology/bibo/edition'
               , 'http://semanticlibrary.org/terms/title_prefix'
               , 'http://rdvocab.info/Elements/variantTitle'
               , 'http://www.w3.org/2004/02/skos/core#altLabel'
               , 'http://semanticlibrary.org/terms/pagination'
               , 'http://semanticlibrary.org/terms/publish_country'
               , 'http://open.vocab.org/terms/firstSentence'
               , 'http://purl.org/dc/terms/language'
               
               ));
  }
  
  function can_display($resource_uri, $available_properties, $graph) {
    return TRUE;
  }


  function get_content($resource_uri, $available_properties, $graph) {
    $html = '';
    $html .= '<p>A';
    
    $language_name = language_name($graph->get_first_literal($resource_uri, 'http://purl.org/dc/terms/language', ''));
    if ($language_name) {
      if (preg_match('~^[aeiou]~i', $language_name)) $html .= 'n';
      $html .= ' ';
      $html .= $language_name;
    }
    else {
      $html .= 'n';
    }
    $html .= ' edition of ';

    $work_uri = $graph->get_first_resource($resource_uri, 'http://purl.org/dc/terms/isVersionOf', '');
    if ($work_uri) {
      $html .= $this->link_uri($work_uri, $graph->get_first_literal($work_uri,'http://purl.org/dc/terms/title' ), $graph);
    }
  
    $by_statement = $graph->get_first_literal($resource_uri, 'http://semanticlibrary.org/terms/by_statement', '');
    if ($by_statement) {
      if (strpos($by_statement, 'by') !== 0) {
        $html .= ' by';
      }
      $html .= ' ' . htmlspecialchars($by_statement);
    }

    $edition_info = $graph->get_first_literal($resource_uri, 'http://purl.org/ontology/bibo/edition', '');
    if ($edition_info) {
      $edition_info = str_replace('[', '', $edition_info);
      $edition_info = str_replace(']', '', $edition_info);
      $edition_info = str_replace('(', '', $edition_info);
      $edition_info = str_replace(')', '', $edition_info);
      $html .= ' (' . htmlspecialchars($edition_info) . ')';
    }

    
    $html .= '. ';

  // Publication info
    $issued = $graph->get_first_literal($resource_uri, 'http://purl.org/dc/terms/issued', '');
    $publication_place_name = $graph->get_first_literal($resource_uri, 'http://rdvocab.info/Elements/placeOfPublication', '');

    $pub_info = '';
    if ($issued) {
      $pub_info .= 'Published ' . htmlspecialchars($issued);
    }

    $publisher_name = $graph->get_first_literal($resource_uri, 'http://rdvocab.info/Elements/publishersName', '');
    $publisher_uri = $graph->get_first_resource($resource_uri, 'http://purl.org/dc/terms/publisher', '');
    if ($publisher_name) {
      if (!$pub_info) {
        $pub_info .= 'Published';
      }
      $pub_info .= ' by ';
      
      if ($publisher_uri) {
        $pub_info .= '<a href="' . htmlspecialchars($this->page_about_resource($publisher_uri)) . '">' . htmlspecialchars($publisher_name) . '</a>';
      }
      else {
        $pub_info .= htmlspecialchars($publisher_name);
      }
    }



    if ($publication_place_name) {
      if ($pub_info) { 
        $pub_info .= ', ';
      }
      else {
        $pub_info .=  'Published in ';
      }
      $pub_info .= htmlspecialchars($publication_place_name);
    }


    if ($pub_info) {
      $html .= $pub_info . '.';
    }
  
  
    $html.= '</p>';

    $html .= $this->render_property_group($resource_uri, array('http://purl.org/dc/terms/abstract'), "", $graph, 'p');

    $firstSentence = $graph->get_first_literal($resource_uri, 'http://open.vocab.org/terms/firstSentence', '');
    if ($firstSentence) {
      $html .= '<p>First sentence: &#8220;' . htmlspecialchars($firstSentence) . '&#8221;</p>';
    }





    // Format info
    $format_info = '';

    $medium = $graph->get_first_literal($resource_uri, 'http://purl.org/dc/terms/medium', '');
    if ($medium) $format_info_list[] = $medium;

    $number_of_pages = $graph->get_first_literal($resource_uri, 'http://open.vocab.org/terms/numberOfPages', '');
    if (strpos($number_of_pages, 'pages') === FALSE) $number_of_pages .= ' pages';
    if ($number_of_pages) $format_info_list[] = $number_of_pages;

    $dimensions = $graph->get_first_literal($resource_uri, 'http://semanticlibrary.org/terms/physical_dimensions', '');
    if ($dimensions) $format_info_list[] = $dimensions;

    $weight = $graph->get_first_literal($resource_uri, 'http://open.vocab.org/terms/weight', '');
    if ($weight) $format_info_list[] = $weight;
    
    if (count($format_info_list)) {
      $html .= '<p>Physical description: ' . htmlspecialchars(join('; ', $format_info_list)) . '</p>';
    }

    $html .= $this->render_property_group($resource_uri, array('http://www.w3.org/2004/02/skos/core#note'), "", $graph, 'p');

    $html .= $this->render_property_group($resource_uri, array('http://rdvocab.info/Elements/variantTitle', 'http://www.w3.org/2004/02/skos/core#altLabel'), "Also known as ", $graph, 'inline');

    $html = str_replace('..', '.', $html);
    $html = str_replace(' ; ', '; ', $html);
    $html = str_replace('[', '', $html);
    $html = str_replace(']', '', $html);
  
    return $html;
  }  
}


class WorkWidget extends FeatureWidget {
  
  function __construct($title = 'About this work') {
    parent::__construct($title);
    $this->add_properties_used(
            array(
                'http://semanticlibrary.org/terms/by_statement'
               , 'http://purl.org/dc/terms/title'
               , 'http://www.w3.org/2004/02/skos/core#note'
               , 'http://purl.org/dc/terms/abstract'
               , 'http://rdvocab.info/Elements/variantTitle'
               , 'http://semanticlibrary.org/terms/title_prefix'
               
               ));
      
  }
  
  function can_display($resource_uri, $available_properties, $graph) {
    return TRUE;
  }


  function get_content($resource_uri, $available_properties, $graph) {
    $html = '';
  
    $html .= '<p>' . htmlspecialchars($graph->get_label($resource_uri));
    $by_statement = $graph->get_first_literal($resource_uri, 'http://semanticlibrary.org/terms/by_statement', '');
    if ($by_statement) {
      $html .= ' ' . htmlspecialchars($by_statement);
    }
   
    $html .= '</p>';
 

    $html .= $this->render_property_group($resource_uri, 'http://purl.org/dc/terms/abstract', "", $graph, 'p');
    $html .= $this->render_property_group($resource_uri, 'http://www.w3.org/2004/02/skos/core#note', "", $graph, 'p');
    $html .= $this->render_property_group($resource_uri, array('http://rdvocab.info/Elements/variantTitle', 'http://www.w3.org/2004/02/skos/core#altLabel'), "Also known as ", $graph, 'inline');


    $resource_list = $graph->get_resource_triple_values($resource_uri, 'http://purl.org/dc/terms/isPartOf');
    
    $formatted_resources = array();
    foreach ($resource_list as $resource) {
      $formatted_resources[] = $this->link_uri($resource, '', $graph);
    }

    if (count($formatted_resources) > 0) {
      $html .= "\n<p>Part of ";
      if (count($formatted_resources) == 1) {
        $html .= 'the ' . $formatted_resources[0] . ' series.';
      }
      else {
        $html .= 'several series: ' . $this->join_list($formatted_resources, ', ', ' and ');
      }
    }



    return $html;
  }  
}


class SubjectWidget extends FeatureWidget {
  function __construct($title = 'About this subject') {
    parent::__construct($title);
  }
}


class PersonWidget extends FeatureWidget {
  var $properties = array(
          'http://xmlns.com/foaf/0.1/name',
          'http://purl.org/vocab/bio/0.1/olb',
        );
  function __construct($title = 'About this person') {
    parent::__construct($title);
    $this->add_properties_used( $this->properties );
  }
  
  
  function get_content($resource_uri, $available_properties, $graph) {
    $html = '';
   
    $olb = $graph->get_first_literal($resource_uri, 'http://purl.org/vocab/bio/0.1/olb', '');
    if (! $olb) {
      $olb = $graph->get_first_literal($resource_uri, 'http://dbpedia.org/property/shortDescription', '');
    }
    if ($olb) {
      $html .= '<p>' . htmlspecialchars($olb) . '</p>';
    }

    $image_uri = $graph->get_first_literal($resource_uri, 'http://xmlns.com/foaf/0.1/depiction', '');
    if (! $image_uri) {
      $image_uri = $graph->get_first_resource($resource_uri, 'http://xmlns.com/foaf/0.1/img', '');
    }
    
 
    $abstract = $graph->get_first_literal($resource_uri, 'http://dbpedia.org/property/abstract', '');
    $abstract2 = $graph->get_first_literal($resource_uri, 'http://purl.org/dc/terms/abstract', '');
  
    if (strlen($abstract2) > strlen($abstract) * 0.3) {
      $abstract = $abstract2;
    }

    if ($abstract) {
      $html .= '<p>';
      if ($image_uri) {
        $html .= '<img src="' . htmlspecialchars($image_uri) . '" alt="Image of ' . htmlspecialchars($graph->get_label($resource_uri)) . '" class="featureimage">';
      }
      $abstract = str_replace('Cite error: Invalid &lt;ref&gt; tag; refs with no name must have content', '', $abstract);
      
      $html .= nl2br(htmlspecialchars($abstract)) . '</p>';
    }
    else {
      if ($image_uri) {
        $html .= '<p><img src="' . htmlspecialchars($image_uri) . '" alt="Image of ' . htmlspecialchars($graph->get_label($resource_uri)) . '" class="featureimage"></p>';
      }
    }
    
    $html .= $this->render_property_group($resource_uri, array('http://www.w3.org/2004/02/skos/core#altLabel', 'http://dbpedia.org/property/alternativeNames'), "Also known as ", $graph, 'inline');
    $html .= $this->render_property_group($resource_uri, array('http://dbpedia.org/property/pseudonym'), "Pseudonyms: ", $graph, 'inline');

    
    $influenced_list = $graph->get_resource_triple_values($resource_uri, 'http://dbpedia.org/property/influenced');
    $influenced_list_local = array();
    foreach ($influenced_list as $uri) {
      if (parse_url($uri, PHP_URL_HOST) == 'semanticlibrary.org') {
        $influenced_list_local[] = $this->link_uri($uri, '', $graph);
      }
    }

    $influences_list = $graph->get_resource_triple_values($resource_uri, 'http://dbpedia.org/property/influences');
    $influences_list_local = array();
    foreach ($influences_list_local as $uri) {
      if (parse_url($uri, PHP_URL_HOST) == 'semanticlibrary.org') {
        $influences_list_local[] = $this->link_uri($uri, '', $graph);
      }
    }

    $surname = $graph->get_first_literal($resource_uri, 'http://xmlns.com/foaf/0.1/surname');

    if (count($influences_list_local) || count($influenced_list_local)) {
      $html .= '<p>';
      
      if (count($influences_list_local)) {
        if ($surname)  {
          $html .= $surname . ' was i';
        }
        else {
          $html .= 'I';
        }
        $html .= 'nfluenced by ' . $this->join_list($influences_list_local, ', ', ' and ');
      
        if (count($influenced_list_local)) {
          $html .= ' and, in turn, influenced ' . $this->join_list($influenced_list_local, ', ', ' and ');
        }
      }
      else {
        if ($surname)  {
          $html .= $surname . ' i';
        }
        else {
          $html .= 'I';
        }
        $html .= 'nfluenced ' . $this->join_list($influenced_list_local, ', ', ' and ');
      }

      $html .=  '</p>';
    }
    return $html;
  }
}

class PublisherWidget extends FeatureWidget {
  function __construct($title = 'About this publisher') {
    parent::__construct($title);
  }
  
}


class CollectionWidget extends FeatureWidget {
  function __construct($title = 'About this collection') {
    parent::__construct($title);
    $this->add_properties_used( array(
              'http://schemas.talis.com/2005/dir/schema#isAccessedVia'
              ) );
    
  }


  function get_content($resource_uri, $available_properties, $graph) {
    $content = '';

    $locations = array();
    $iav_list = $graph->get_resource_triple_values($resource_uri, 'http://schemas.talis.com/2005/dir/schema#isAccessedVia');
    foreach ($iav_list as $iav) {
      if ($graph->has_resource_triple($iav, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://schemas.talis.com/2005/library/schema#Library') ) {
        
        $address_info = array();
        $streetAddress = $graph->get_first_literal($iav, 'http://schemas.talis.com/2005/address/schema#streetAddress');
        if ($streetAddress) $address_info[] = $streetAddress;

        $localityName = $graph->get_first_literal($iav, 'http://schemas.talis.com/2005/address/schema#localityName');
        if ($localityName) $address_info[] = $localityName;

        $regionName = $graph->get_first_literal($iav, 'http://schemas.talis.com/2005/address/schema#regionName');
        if ($regionName) $address_info[] = $regionName;

        $postalCode = $graph->get_first_literal($iav, 'http://schemas.talis.com/2005/address/schema#postalCode');
        if ($postalCode) $address_info[] = $postalCode;

        $html = '<a href="http://directory.talis.com/ui/ViewLocation.aspx?id=' . htmlspecialchars(urlencode($iav)) . '">' . htmlspecialchars($graph->get_label($iav)) . '</a>';
        if (count($address_info) > 0) {
          $html .= '<br />' . join(', ', $address_info);
        }
        
        $locations[] = $html;
      }
    }
    
    if (count($locations) > 0) {
      $content .= "\n<p>This collection can be accessed at the following locations:</p>\n";
      $content .= "\n<ul>\n  <li>";
      $content .= join("</li>\n  <li>", $locations);
      $content .= "</li>\n</ul>\n";
    }
    
    return $content;
  }
}

class SeriesWidget extends FeatureWidget {
  function __construct($title = 'About this series') {
    parent::__construct($title);
    $this->add_properties_used( array(
              'http://purl.org/dc/terms/hasPart'
              ) );
    
  }


  function get_content($resource_uri, $available_properties, $graph) {
    $html = '';
    $resource_list = $graph->get_resource_triple_values($resource_uri, 'http://purl.org/dc/terms/hasPart');
    
    $formatted_resources = array();
    foreach ($resource_list as $resource) {
      $formatted_resources[] = $this->link_uri($resource, '', $graph);
    }



    if (count($formatted_resources) > 0) {
      $html .= "\n<p>Works in this series:</p>\n";
      $html .= "\n<ul>\n  <li>";
      $html .= join("</li>\n  <li>", $formatted_resources);
      $html .= "</li>\n</ul>\n";
    }
    else {
      $html .= "\n<p>No known works in this series.</p>\n";
    }
    return $html;
  }
}




class TopSubjectsChartWidget extends Widget {
  function __construct($title = 'Subject profile') {
    parent::__construct($title);
  }

  function can_display($resource_uri, $available_properties, $graph) {
    return TRUE;
  }

  function render($resource_uri, $available_properties, $graph, $class = 'section') {
    $content = "\n" . '<div id="topsubjectschart" class="' . htmlspecialchars($class) . '"></div>' . "\n";
    return $content;
  }

  function get_jquery_script($resource_uri, $available_properties, $graph) {
    if (preg_match('~/([^/]+)$~', $resource_uri, $m)) {
      $resource_id = $m[1];
      return "
              $('#topsubjectschart').hide();
              $.ajax({
                  url: './" . htmlspecialchars($resource_id) . "/stats/subjects/top',
                  type: 'GET',
                  dataType: 'json',
                  timeout: 10000,
                  error: function(xhr, ajaxOptions, thrownError){
                    alert(xhr.statusText);
                  },
                  success: function(results){
            
                    var chs = '500x110';
                        
                    var ch_data='';
                    var ch_labels='';
                    for (var i = 0; i < results.length; i++) {
                      if (i > 0) {
                        ch_data += ',';
                        ch_labels += '|';
                      }
                      ch_labels += results[i]['label'];
                      ch_data += results[i]['count'];
                    }
                    h = '<h3>" . htmlspecialchars($this->get_title()) . "</h3>';
                    h += '<img src=\"http://chart.apis.google.com/chart?cht=p3&chs=' + chs + '&chco=0000FF&chd=t:' + escape(ch_data) + '&chl=' + escape(ch_labels) + '\">';

                    $('#topsubjectschart').append(h);
                    $('#topsubjectschart').show();
                 },
              });";
    }
    else {
      return '';
    }

  }

}



class ClientSideListWidget extends Widget {
  var $_id;
  var $_source;
  function __construct($title = 'Works created by this author', $id='foo', $source) {
    parent::__construct($title);
    $this->_id = $id;
    $this->_source = $source;
  }

  function can_display($resource_uri, $available_properties, $graph) {
    return TRUE;
  }

  function render($resource_uri, $available_properties, $graph, $class = 'section') {
    $content = "\n" . '<div id="' . $this->_id . '" class="' . htmlspecialchars($class) . '"></div>' . "\n";
    return $content;
  }


  function get_jquery_script($resource_uri, $available_properties, $graph) {
    if (preg_match('~/([^/]+)$~', $resource_uri, $m)) {
      $resource_id = $m[1];
      return "
              $('#" . $this->_id . "').hide();
              $.ajax({
                  url: '" . $this->_source . "',
                  type: 'GET',
                  dataType: 'json',
                  timeout: 10000,
                  error: function(xhr, ajaxOptions, thrownError){
                    alert(xhr.statusText);
                  },
                  success: function(results){
                    var g = new Moriarty.SimpleGraph();
                    g.from_json(results);
                    var subs = g.subjects();
                    if (subs.length > 0) {
      
                      h = '<h3>" . htmlspecialchars($this->get_title()) . "</h3>';
                      h += '<ul>';
                      for (var i = 0; i < subs.length; i++) {
                        h += '<li><a href=\"' + subs[i] + '.html\">' + g.get_label(subs[i]) + '</a></li>';
                      }
                      h += '</ul>';
                      $('#" . $this->_id . "').append(h);
                      $('#" . $this->_id . "').show();
                    }
                 },
              });";
    }
    else {
      return '';
    }

  }
}




class SearchWidget extends Widget {
  
  function __construct($title = 'Search') {
    parent::__construct($title);
  }
  function can_display($resource_uri, $available_properties, $graph) {
    return TRUE;
  }

  function get_content($resource_uri, $available_properties, $graph) {
    $html = '';
    
    $html .= '        <form action="/search.html" method="get" class="inline">';
    $html .= '          <input class="text" name="q" id="q" value="" type="text" style="width:180px; "> <input name="search" value="Search" type="image" src="/images/srch.jpg">';
    $html .= '        </form>';
    return $html;
  }
}

class AboutWidget extends Widget {
  
  function __construct($title = 'About') {
    parent::__construct($title);
  }
  function can_display($resource_uri, $available_properties, $graph) {
    return TRUE;
  }

  function get_content($resource_uri, $available_properties, $graph) {
    $html = '';
    $html .= '            <p>This is the <a href="/about">Semantic Library</a>, a rich view of the bibilographic world.</p>';
            
          
    $html .= '            <p class="badge">';
    $html .= '              <a href="http://www.talis.com/"><img src="/images/talis.gif" width="88" height="68" alt="Talis"></a>';
    $html .= '            </p>';
    $html .= '            <p class="badge">';
    $html .= '              <a href="http://linkeddata.org/"><img src="/images/lodlogo.gif" width="120" height="27" alt="Linking Open Data"></a>';
    $html .= '            </p>';
    return $html;
  }
}

class PeriodicalWidget extends FeatureWidget {
  function __construct($title = 'About this series') {
    parent::__construct($title);
    $this->add_properties_used( array(
              'http://purl.org/dc/terms/hasPart'
              ) );
    
  }


  function get_content($resource_uri, $available_properties, $graph) {
    $html = '';

    $resource_list = $graph->get_resource_triple_values($resource_uri, 'http://purl.org/dc/terms/isPartOf');
    if (count($resource_list) > 0) {
      $html .= '<p>Is part of ' . $this->link_uri($resource_list[0], '', $graph) . '</p>';
    }

    $html .= $this->render_property_group($resource_uri, array('http://purl.org/dc/terms/abstract'), "", $graph, 'p');

    $info = '';

    $volume = $graph->get_first_literal($resource_uri, 'http://purl.org/ontology/bibo/volume');
    if ($volume) $info .= 'Volume ' . $volume;

    $issue = $graph->get_first_literal($resource_uri, 'http://purl.org/ontology/bibo/issue');
    if ($issue) {
      if ($info) {
        $info .= ', number ' . $issue;
      }
      else {
        $info .= 'Number ' . $issue;
      }
    }
    
    if ($info) {
      $html .= '<p>' . $info . '</p>';
    }

    $val = $graph->get_first_resource($resource_uri, 'http://example.org/terms/order');
    if ($val) $html .= '<p><a href="' . htmlspecialchars($val) . '">Order from BL Direct</a></p>' . "\n";

    $val = $graph->get_first_literal($resource_uri, 'http://example.org/terms/copyrightStatus');
    if ($val) $html .= '<p>Copyright status: ' . htmlspecialchars($val) . '</p>' . "\n";

    $val = $graph->get_first_literal($resource_uri, 'http://example.org/terms/accession');
    if ($val) $html .= '<p>Accession: ' . htmlspecialchars($val) . '</p>' . "\n";

    $resource_list = $graph->get_resource_triple_values($resource_uri, 'http://purl.org/dc/terms/creator');
    
    $formatted_resources = array();
    foreach ($resource_list as $resource) {
      $formatted_resources[] = $this->link_uri($resource, '', $graph);
    }

    if (count($formatted_resources) > 0) {
      $html .= "\n<h3>Authors</h3>\n";
      $html .= "\n<ul>\n  <li>";
      $html .= join("</li>\n  <li>", $formatted_resources);
      $html .= "</li>\n</ul>\n";
    }

    
    $resource_list = $graph->get_resource_triple_values($resource_uri, 'http://purl.org/dc/terms/hasPart');
    
    $formatted_resources = array();
    foreach ($resource_list as $resource) {
      $formatted_resources[] = $this->link_uri($resource, '', $graph);
    }

    if (count($formatted_resources) > 0) {
      $html .= "\n<h3>Parts</h3>\n";
      $html .= "\n<ul>\n  <li>";
      $html .= join("</li>\n  <li>", $formatted_resources);
      $html .= "</li>\n</ul>\n";
    }
    return $html;
  }
}

