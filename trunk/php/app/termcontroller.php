<?php
require_once 'constants.inc.php';
require_once MORIARTY_DIR . 'store.class.php';
require_once MORIARTY_DIR . 'sparqlservice.class.php';

class TermController extends k_Controller
{
  function get_description() {
    $term_uri = local_to_remote($this->url());
    $store = new Store(STORE_URI);
    $mb = $store->get_metabox();
    $desc_response = $mb->describe($term_uri, 'json');
    if ($desc_response->is_success()) {
      $desc = new SimpleGraph();
      $desc->from_json($desc_response->body);    
      $desc->remove_property_values($term_uri, 'http://schemas.talis.com/2005/dir/schema#etag');
      $desc->set_namespace_mapping('ov', 'http://open.vocab.org/terms/');
      $desc->set_namespace_mapping('status', 'http://www.w3.org/2003/06/sw-vocab-status/ns#');
      $desc->set_namespace_mapping('label', 'http://purl.org/net/vocab/2004/03/label#');      
      return $desc;
    }
    else {
      $response = new k_http_Response(500);
      $response->setHeader("Content-type", "text/html");
      $response->setContent("Could not retrieve description of term");
    }
   
  }


  function generate_response($type, $content) {
    $response = new k_http_Response(200);
    $response->setHeader("Content-type", $type);
    $response->setContent($content);
    throw $response;
  }

  function forward($name) {
    $this->uri =  local_to_remote($this->url());
    $store = new Store(STORE_URI);
    $mb = $store->get_metabox();
    if ($mb->has_description($this->uri)) {
  
      if ($name == 'rdf') {
        $desc = $this->get_description();
        $this->generate_response("application/rdf+xml", $desc->to_rdfxml() );
      }
      else if ($name == 'turtle') {
        $desc = $this->get_description();
        $this->generate_response("text/plain", $desc->to_turtle() );
      }
      else if ($name == 'json') {
        $desc = $this->get_description();
        $this->generate_response("application/json", $desc->to_json() );
      }
      else if ($name == 'xml') {
        $desc = $this->get_description();
        $this->generate_response("application/xml", $desc->to_rdfxml() );
      }
      else if ($name == 'html') {
        $this->description = $this->get_description();
        $cs_query =  "prefix cs: <http://purl.org/vocab/changeset/schema#>
                      select ?cs ?creator ?date ?reason
                      where {
                        ?cs cs:subjectOfChange <" . $this->uri . "> ;
                        cs:creatorName ?creator ;
                        cs:createdDate ?date ;
                        cs:changeReason ?reason .
                      }
                      order by DESC(?date)";
     
        $store = new Store(STORE_URI);
        $sparql = $store->get_sparql_service();
        $params['history'] = $sparql->select_to_array($cs_query);
        
        
        return $this->render("templates/term.tpl.php", $params);       
      }
      else {
        $response = new k_http_Response(404);
        $response->setHeader("Content-type", "text/html");
        $response->setContent("<html><head><title>404 Not Found</title></head><body><h1>404 Not Found</h1></body></html>");
        throw $response;
      }     
    }

  }

  function GET() {
    $term_uri =  local_to_remote($this->url());
    $store = new Store(STORE_URI);
    $mb = $store->get_metabox();
    if ($mb->has_description($term_uri)) {
      $guessed_output = guess_output_type($_SERVER["HTTP_ACCEPT"]);
      throw new k_http_Redirect($this->url() . '/' . $guessed_output);
    }
  }
}
?>
