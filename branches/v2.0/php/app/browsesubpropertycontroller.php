<?php
ini_set('memory_limit', '160M');
require_once 'constants.inc.php';
require_once MORIARTY_DIR . 'store.class.php';
require_once MORIARTY_DIR . 'sparqlservice.class.php';
require_once MORIARTY_DIR . 'simplegraph.class.php';

class BrowseSubPropertyController extends k_Controller
{

  function forward($name) {
    if ($name == 'img') {
      require_once OV_LIB_DIR . 'graphviz/GraphViz.php';

      $graph = new Image_GraphViz(true, array( 'rankdir' => 'LR'), 'G', false);
      $query =  "prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
  prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
  select ?p1 ?p2   
  where {
    ?p1 rdfs:subPropertyOf ?p2 ; rdfs:isDefinedBy <" . htmlspecialchars(VOCAB_SCHEMA) . "> .
 }";

      $store = new Store(STORE_URI);
      $sparql = $store->get_sparql_service();
      $node_index = array();
      $results = $sparql->select_to_array($query);  
      //var_dump ($results);
      foreach ($results as $result) {
        $from = $result['p1']['value'];
        $to = $result['p2']['value'];
        if (!array_key_exists($from, $node_index)) {
          $node_index[$from] = 'n' . count($node_index); 
          $graph->addNode($node_index[$from], array('label' => make_qname($from)));
        }
        if (!array_key_exists($to, $node_index)) {
          $node_index[$to] = 'n' . count($node_index); 
          $graph->addNode($node_index[$to], array('label' => make_qname($to)));
        }

        $graph->addEdge(array($node_index[$from] => $node_index[$to]));
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
    return $this->render("templates/browsesubproperties.tpl.php", $vars);
  }
}
?>
