<?php
require_once 'constants.inc.php';
require_once MORIARTY_DIR . 'store.class.php';
require_once MORIARTY_DIR . 'sparqlservice.class.php';
require_once MORIARTY_DIR . 'simplegraph.class.php';

class BrowseController extends k_Controller
{

  function forward($name) {
    if ($name == 'graph') {
      $terms_query =  "prefix cs: <http://purl.org/vocab/changeset/schema#>
  prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
  prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
  construct {
  ?p1 rdfs:range ?b .
  ?p2 rdfs:domain ?a .
  }
  where {
   {
    ?p1 rdfs:range ?b .
   }
   union 
   {
    ?p2 rdfs:domain ?a .
   }
  }";

      $store = new Store(STORE_URI);
      //$store = new Store('http://api.talis.com/stores/schema-cache');
      $sparql = $store->get_sparql_service();
      $terms = $sparql->construct_to_simple_graph($terms_query);  
      $node_index = array();
      $nodes = array();
      $edges = array();
      $index = $terms->get_index();
      
      $count = 0;
      foreach ($index as $s=>$p_list) {
        if ($count++ < 30) {
          $property_domains = array();
          $property_ranges = array();
          
          foreach ($p_list as $p => $v_list) {
            if ($p == RDFS_RANGE) {
              foreach ($v_list as $v_info) {
                if ($v_info['type'] == 'uri') {
                  if (!array_key_exists($v_info['value'], $node_index)) {
                    $node_index[$v_info['value']] = 'n' . count($node_index); 
                    $nodes[] = '<node id="' . $node_index[$v_info['value']] . '" text="' . htmlspecialchars($v_info['value']) . '" color="cccccc" textcolor="0000ff" link="' . htmlspecialchars($v_info['value']) . '" type="CircleTextNode"/>';
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
                    $nodes[] = '<node id="' . $node_index[$v_info['value']] . '" text="' . htmlspecialchars($v_info['value']) . '" color="cccccc" textcolor="0000ff" link="' . htmlspecialchars($v_info['value']) . '" type="CircleTextNode"/>';
                  }
                  $property_domains[] = $node_index[$v_info['value']];                
                } 
              }
            }
          }
          
          foreach ($property_domains as $domain) {
            foreach ($property_ranges as $range) {
              $edges[] = '<edge sourceNode="' . $domain . '" targetNode="' . $range . '" label="' . htmlspecialchars($s). '" textcolor="555555" scale="100" edgesize="4"/>';
            }
          }
          
        }
      }
      
  
      $content = '<graph title="Simple Graph Demo" bgcolor="ffffff" linecolor="cccccc" viewmode="display" width="725" height="600" type="directed" segmentlength="1">';
      foreach ($nodes as $node) {
        $content .= $node . "\n"; 
      }
      foreach ($edges as $edge) {
        $content .= $edge . "\n"; 
      }
      $content .= '</graph>';
      
      $response = new k_http_Response(200);
      $response->setHeader("Content-type", 'application/xml');
      $response->setContent($content);
      throw $response;          
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
    return $this->render("templates/browse.tpl.php", $vars);
  }
}
?>
