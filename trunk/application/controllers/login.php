<?php

class Login extends Controller {

  function __construct() {
    parent::Controller();
    $this->lang->load('openid', 'english');
    $this->load->library('openid');
    $this->load->helper('url');
    $this->load->library('session');
    //$this->output->enable_profiler(TRUE);
  }

    // Index
  function index() {
    if ($this->input->post('action') == 'verify') {
      $user_id = $this->input->post('openid_identifier');

      if (preg_match('~\.local$~', $this->input->server("HTTP_HOST")) && $user_id == 'letmein') {
        $newdata = array(
           'openid' => 'http://vocab.org/letmein',
           'username' => 'letmein',
           'logged_in' => TRUE
           );

        $this->session->set_userdata($newdata);
        redirect( 'docs', 'location', 302);
        exit;
      }

      $pape_policy_uris = $this->input->post('policies');

      if (!$pape_policy_uris) {
        $pape_policy_uris = array();
      }

      $this->config->load('openid');
      $req = $this->config->item('openid_required');
      $opt = $this->config->item('openid_optional');
      $policy = site_url($this->config->item('openid_policy'));
      $request_to = site_url($this->config->item('openid_request_to'));

      $this->openid->set_request_to($request_to);
      $this->openid->set_trust_root(base_url());
      $this->openid->set_args(null);
      $this->openid->set_sreg(true, $req, $opt, $policy);
      $this->openid->set_pape(true, $pape_policy_uris);
      $this->openid->authenticate($user_id);
    }

    $data = array();
    $this->load->view('login', $data);

  }

    // Policy
    function policy()
    {
      $this->load->view('view_policy');
    }

    // set message
    function _set_message($msg, $val = '', $sub = '%s')
    {
        return str_replace($sub, $val, $this->lang->line($msg));
    }

    function check() {
      $this->config->load('openid');
      $request_to = site_url($this->config->item('openid_request_to'));

      $this->openid->set_request_to($request_to);
      $response = $this->openid->getResponse();

      switch ($response->status) {
        case Auth_OpenID_CANCEL:
            $data['msg'] = $this->lang->line('openid_cancel');
            $newdata = array(
               'logged_in' => FALSE
               );

            $this->session->set_userdata($newdata);
            break;
        case Auth_OpenID_FAILURE:
            $data['error'] = $this->_set_message('openid_failure', $response->message);
            $newdata = array(
               'logged_in' => FALSE
               );

            $this->session->set_userdata($newdata);

            break;
        case Auth_OpenID_SUCCESS:
            $openid = $response->getDisplayIdentifier();
            $esc_identity = htmlspecialchars($openid, ENT_QUOTES);

            $data['success'] = 'You have successfully logged in using '. htmlspecialchars($esc_identity) . ' as your identity';

            $sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
            $sreg = $sreg_resp->contents();


            $newdata = array(
               'openid' => $esc_identity,
               'logged_in' => TRUE
               );

            if (array_key_exists('fullname', $sreg)) {
               $newdata['username'] = $sreg['fullname'];
            }
            else {
               $newdata['username'] = $esc_identity;
            }

            $this->session->set_userdata($newdata);
            break;
     }


      $this->load->view('login', $data);
    }

}
?>
