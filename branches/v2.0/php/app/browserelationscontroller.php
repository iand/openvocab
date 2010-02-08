<?php
ini_set('memory_limit', '160M');
require_once 'constants.inc.php';
require_once MORIARTY_DIR . 'store.class.php';
require_once MORIARTY_DIR . 'sparqlservice.class.php';
require_once MORIARTY_DIR . 'simplegraph.class.php';

class BrowseRelationsController extends k_Controller
{

  function forward($name) {
    if ($name == 'img') {
      require_once OV_LIB_DIR . 'graphviz/GraphViz.php';

      $graph = new Image_GraphViz(true, array( 'rankdir' => 'LR'), 'G', false);
      $terms_query =  "prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
  prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
  construct {
  ?p1 rdfs:range ?b .
  ?p2 rdfs:domain ?a .
  }
  where {
   {
    ?p1 rdfs:range ?b ; rdfs:isDefinedBy <" . VOCAB_SCHEMA. "> .
   }
   union 
   {
    ?p2 rdfs:domain ?a ; rdfs:isDefinedBy <" . VOCAB_SCHEMA. "> .
   }
 }";

      $store = new Store(STORE_URI);
      $sparql = $store->get_sparql_service();
      $terms = $sparql->construct_to_simple_graph($terms_query);  
      $node_index = array();
      $nodes = array();
      $edges = array();
      $index = $terms->get_index();
      
      foreach ($index as $s=>$p_list) {
        $property_domains = array();
        $property_ranges = array();
        
        foreach ($p_list as $p => $v_list) {
          if ($p == RDFS_RANGE) {
            foreach ($v_list as $v_info) {
              if ($v_info['type'] == 'uri') {
                if (!array_key_exists($v_info['value'], $node_index)) {
                  $node_index[$v_info['value']] = 'n' . count($node_index); 
                  $graph->addNode($node_index[$v_info['value']], array('label' => make_qname($v_info['value'])));
                }
                $property_ranges[] = $node_index[$v_info['value']];                
              } 
            }
          }
          if ($p == RDFS_DOMAIN) {
            foreach ($v_list as $v_info) {
              if ($v_info['type'] == 'uri') {
                if (!array_key_exists($v_info['value'], $node_index)) {
                  $node_index[$v_info['value']] = 'n' . count($node_index); 
                  $graph->addNode($node_index[$v_info['value']], array('label' => make_qname($v_info['value'])));
                }
                $property_domains[] = $node_index[$v_info['value']];                
              } 
            }
          }
        }
        
        foreach ($property_domains as $domain) {
          foreach ($property_ranges as $range) {
            $graph->addEdge(array($domain => $range), array('label' => make_qname($s)));
          }
        }
        
      }
  

      header('content-type: image/png');
      echo $graph->fetch('png', 'fdp');      
    }
    else {
      $response = new k_http_Response(404);
      $response->setHeader("Content-type", "text/html");
      $response->setContent("<html><head><title>404 Not Found</title></head><body><h1>404 Not Found</h1></body></html>");
      throw $response;
    }    
    }

  function GET() {
    $vars = array(  );
    return $this->render("templates/browserelations.tpl.php", $vars);
  }
}
?>
