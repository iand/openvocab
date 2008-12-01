<?php
require_once 'constants.inc.php';
require_once MORIARTY_DIR . 'store.class.php';
require_once MORIARTY_DIR . 'simplegraph.class.php';
require_once MORIARTY_DIR . 'sparqlservice.class.php';

class TermListController extends k_Controller
{

  function forward($name) {
    $next_controller = new TermController($this, $name);
    return $next_controller->handleRequest()  ;
  }

  function generate_response($type, $content) {
    $response = new k_http_Response(200);
    $response->setHeader("Content-type", $type);
    $response->setContent($content);
    throw $response;
  }


  function GET() {

    $term_uri =  local_to_remote($this->url());
    $format = null;
    
    if (preg_match('~^(.+)\.(html|rdf|xml|turtle|json)$~', $term_uri, $m)) {
      $term_uri = $m[1];
      $format = $m[2];
    }    
    else {  
      $guessed_output = guess_output_type($_SERVER["HTTP_ACCEPT"]);
      throw new k_http_Redirect($this->url() . '.' . $guessed_output);
    }

    $vars = array( 'results' => null, 'q' => '' );

    if ( empty($this->GET['q']) ) {
      $store = new Store(STORE_URI);
      $sparql = $store->get_sparql_service();

      if ($format == 'html') {

        $terms_query =  "prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
prefix owl: <http://www.w3.org/2002/07/owl#>
select ?term ?label ?comment
where {
  {
    ?term a rdf:Property ;
          rdfs:label ?label .
          optional { ?term rdfs:comment ?comment .}
  }
  union {
    ?term a owl:Class ;
          rdfs:label ?label .
          optional { ?term rdfs:comment ?comment .}
  }
}
order by ?label
";

        $vars['terms'] = $sparql->select_to_array($terms_query);
      }
      else {
        $terms_query =  "prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
prefix owl: <http://www.w3.org/2002/07/owl#>
describe <" . htmlspecialchars(VOCAB_SCHEMA) . "> ?term
where {
  {
    ?term a rdf:Property .
  }
  union {
    ?term a owl:Class .
  }
}
order by ?label
";
        $response = $sparql->graph($terms_query);
        if ($response->is_success()) {
          $desc = new SimpleGraph();
          $desc->from_rdfxml( $response->body );

          if ($format == 'rdf') {
            $this->generate_response("application/rdf+xml", $desc->to_rdfxml() );
          }
          else if ($format == 'turtle') {
            $this->generate_response("text/plain", $desc->to_turtle() );
          }
          else if ($format == 'json') {
            $this->generate_response("application/json", $desc->to_json() );
          }
          else if ($format == 'xml') {
            $this->generate_response("application/xml", $desc->to_rdfxml() );
          }
          else {
            $response = new k_http_Response(500);
            $response->setHeader("Content-type", "text/html");
            $response->setContent("Could not retrieve description of terms");
          }
          
        }
        else {
          $response = new k_http_Response(500);
          $response->setHeader("Content-type", "text/html");
          $response->setContent("Could not retrieve description of terms");
        }
      }
    }
    else {
      $query = $this->GET['q'];
      $store = new Store(STORE_URI);
      $cb = $store->get_contentbox();

      $vars['results'] = $cb->search_to_resource_list($query);
      $vars['q'] = $query;
    }

    return $this->render("templates/termlist.tpl.php", $vars);
  }
}
?>
