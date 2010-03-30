<?php
class DeleteTerm extends Controller {
  function __construct() {
    parent::Controller();
//    $this->output->enable_profiler(TRUE);
    $this->load->helper('url');
    $this->load->helper('local_link');
    $this->load->library('session');
  }


  function fill_term() {
    $this->term->is_defined_by = NULL;
    $this->term->userdocs = NULL;
    $this->term->status = "deprecated";


    $this->term->label = NULL;
    $this->term->plural = NULL;
    $this->term->comment = NULL;
    $this->term->is_functional = FALSE;
    $this->term->is_inverse_functional = FALSE;
    $this->term->is_symmetrical = FALSE;
    $this->term->is_transitive = FALSE;

  }

  function fill_change() {
    $this->change->set_uri(config_item('resource_base') . '/' . config_item('change_path') . '/' . strtolower(md5(uniqid('', TRUE))));
    $this->change->term = $this->term->get_uri();
    $this->change->creator = config_item('resource_base') . '/' . config_item('user_path') . '/' . strtolower(md5($this->session->userdata('openid')));
    $this->change->openid = trim($this->session->userdata('openid'));
    $this->change->date = gmdate('Y-m-d\TH:i:s\Z');

    $this->change->label = 'Deleted';
    $this->change->reason = trim($this->input->post('reason'));
  }


  function index() {

    if (!$this->session->userdata('logged_in')) {
      redirect('login');
      exit;
    }

    $data = array();

    $uri = $this->input->get_post('uri');

    $this->load->library('form_validation');
    $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
    $this->form_validation->set_rules('confirm', 'Confirm', 'trim|required');

    if ($this->form_validation->run() == FALSE) {
      $this->load->model('Term', 'term');
      $this->term->set_uri($uri);
      $this->term->load_from_network();
      $data['uri'] = $uri;
      $data['model'] = $this->term;
      $this->load->view('form.deleteterm.php', $data);
    }
    else {
      $this->load->model('Term', 'term');
      $this->load->model('TermChange', 'change');

      $this->term->set_uri($uri);
      $this->fill_term();
      $this->fill_change();

      $this->load->model('Term', 'original');
      $this->original->set_uri($this->term->get_uri());
      $this->original->load_from_network(FALSE); // ignore cache

      $response = $this->term->save_to_network($this->original);
      if ($response->is_success()) {
        $response = $this->change->save_to_network();
        $this->load->model('TermWithChanges', 'termwithchanges');
        $this->termwithchanges->load_from_network(FALSE); // repopulate cache
        redirect( 'docs', 'location', 302);
        exit;
      }
      else {
        $data['error'] = 'Your changes could not be saved. The response from the Talis Platform store was: ' . $response->body . ' (code ' . $response->status_code . ')';
        $data['uri'] = $uri;
        $data['model'] = $this->term;
        $this->load->view('form.deleteterm.php', $data);
      }
    }

  }

}
