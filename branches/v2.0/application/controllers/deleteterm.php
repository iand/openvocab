<?php
class DeleteTerm extends Controller {
  function __construct() {
    parent::Controller();
//    $this->output->enable_profiler(TRUE);
    $this->load->helper('url');
    $this->load->helper('local_link');
    $this->load->library('session');
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
      $this->load->model('Term', 'model');
      $this->model->set_uri($uri);
      $this->model->load_from_network();
      $data['uri'] = $uri;
      $data['model'] = $this->model;
      $this->load->view('form.deleteterm.php', $data);
    }
    else {
      $this->load->model('Term', 'model');
      $this->model->set_uri($uri);
      $response = $this->model->delete_from_network();
      redirect( 'docs', 'location', 302);
      exit;
    }

  }

}
