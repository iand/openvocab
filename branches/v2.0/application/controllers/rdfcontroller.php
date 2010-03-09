<?php
require_once MORIARTY_DIR . 'store.class.php';


abstract class RDFController extends Controller {
  var $model;

  abstract function load_model();
  abstract function load_view($data);


  function __construct() {
    if (count($_GET)) show_404('page');
    parent::Controller();
    //$this->output->enable_profiler(TRUE);

    $this->load->library('session');
    $this->load->helper('local_link');

    $this_host = $this->input->server("HTTP_HOST");
    $path = $this->uri->uri_string();
    $this->request_uri = 'http://' . $this_host . $path;

    $this->type = '';

    $this->resource_path = '/';

    //$this->config->item('resource_base') . $this->uri->uri_string();

    if (preg_match('~^(.+)\.(html|rdf|ttl|json)$~', $path, $m) ) {
      //$this->resource_uri = $this->config->item('resource_base')  . str_replace('/' . $this->config->item('term_document_path') .'/', '/' .  $this->config->item('term_path') . '/', $m[1]);
      $this->doc_uri = $this->request_uri;
      $this->doc_type = $m[2];
    }
    else {
      //$this->resource_uri = $this->config->item('resource_base')  . str_replace('/' .$this->config->item('term_document_path') . '/', '/' . $this->config->item('term_path') . '/', $path);
      $this->doc_uri = 'http://' . $this_host . $path . '.html';
      $this->doc_type = 'html';

      $preferred_types = array('application/rdf+xml' => 'rdf', 'text/html' => 'html', 'application/xml' => 'rdf', 'application/json'=>'json', 'text/turtle' => 'ttl', 'text/plain' => 'ttl');

      $selected_extension = 'html';
      foreach ($preferred_types as $media_type => $extension) {
        if ( preg_match("~" . preg_quote($media_type) . "~i", $this->input->server("HTTP_ACCEPT")) ) {
          $this->doc_uri = 'http://' . $this_host . $path . '.' . $extension;          $this->doc_type = $extension;
          break;
        }
      }

    }
    $this->resource_uri =$this->get_resource_uri($this->request_uri, $path);
    $this->load_model();

    $this->model->set_uri($this->resource_uri);
    $this->model->load_from_network();

    if (!$this->model->has_data() ) {
      show_404('page');
    }
  }

  function get_resource_uri($request_uri, $request_path) {
    return $this->config->item('resource_base') . $this->uri->uri_string();
  }



  function do_conneg($id) {
    header("content-location: " . $this->doc_uri);
    if ($this->doc_type == 'rdf') {
      $this->do_rdf($id);
    }
    elseif ($this->doc_type == 'json') {
      $this->do_json($id);
    }
    elseif ($this->doc_type == 'ttl') {
      $this->do_turtle($id);
    }
    else {
      $this->do_html($id);
    }
  }


  function do_html($id) {
    header("content-type:text/html");
    $data = array();
    $data['model'] = $this->model;
    $data['uri'] = $this->resource_uri;

    $this->load_view($data);
  }

  function do_rdf($id) {
    header("content-type:application/rdf+xml");
    echo $this->model->to_rdfxml();
  }

  function do_turtle($id) {
    header("content-type:text/n3");
    echo $this->model->to_turtle();
  }

  function do_json($id) {
    header("content-type:application/json");
    echo $this->model->to_json();
  }

}
