<?php
class RecommendTesting extends Controller {
  function __construct() {
    parent::Controller();
//    $this->output->enable_profiler(TRUE);
    $this->load->helper('url');
    $this->load->helper('local_link');
    $this->load->library('session');
  }


  function fill_change() {
    $this->change->set_uri(config_item('resource_base') . '/' . config_item('change_path') . '/' . strtolower(md5(uniqid('', TRUE))));
    $this->change->term = $this->term->get_uri();
    $this->change->creator = config_item('resource_base') . '/' . config_item('user_path') . '/' . strtolower(md5($this->session->userdata('openid')));
    $this->change->openid = trim($this->session->userdata('openid'));
    $this->change->date = gmdate('Y-m-d\TH:i:s\Z');

    $this->change->label = "Recommended for upgrade to 'testing' status";
    $this->change->reason = trim($this->input->post('reason'));
  }


  function index() {

    if (!$this->session->userdata('logged_in')) {
      redirect('login');
      exit;
    }

    $data = array();

    $uri = $this->input->get_post('uri');

    $this->load->model('Term', 'term');
    $this->term->set_uri($uri);
    $this->load->model('TermChange', 'change');

    if ($this->input->post('action')) {

      $this->fill_change();
      $validation_errors = $this->change->validate();

      if (count($validation_errors) > 0) {
        $data['error'] = 'The following errors were encountered:<ul>';
        foreach ($validation_errors as $error_info) {
          $data['error'] .= '<li>' . $error_info['message'] . '</li>';
        }
        $data['error'] .= '</ul>';

        $this->term->load_from_network();
        $data['uri'] = $uri;
        $data['model'] = $this->term;
        $this->load->view('form.recommendtesting.php', $data);
      }
      else {

        $response = $this->change->save_to_network();
        if ($response->is_success()) {
          redirect( 'docs', 'location', 302);
          exit;
        }
        else {
          $data['error'] = 'Your changes could not be saved. The response from the Talis Platform store was: ' . $response->body . ' (code ' . $response->status_code . ')';
          $data['uri'] = $uri;
          $data['model'] = $this->term;
          $this->load->view('form.recommendtesting.php', $data);
        }
      }
    }
    else {
      $this->term->load_from_network();
      $data['uri'] = $uri;
      $data['model'] = $this->term;
      $this->load->view('form.recommendtesting.php', $data);
    }
  }

}
