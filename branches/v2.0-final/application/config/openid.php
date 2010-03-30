<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$config['openid_storepath'] = '/tmp';
$config['openid_policy'] = 'login/policy';
$config['openid_required'] = array();
$config['openid_optional'] = array('fullname');
$config['openid_request_to'] = 'login/check';

?>
