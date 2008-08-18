<?php
require_once 'moriarty.inc.php';
require_once MORIARTY_DIR . 'httprequest.class.php';

class LoginController extends k_Controller
{
  function GET() {
    $params = Array();
    return $this->render("templates/login.tpl.php", $params);
  }
  
  function POST() {
    
    $request = new HttpRequest('GET', $_POST['url']);
    $request->set_accept("*/*");
    $response = $request->execute();
        

    preg_match( "<link rel=\"openid.server\" href=\"(.*?)\" />", $response->body, $found );
    $url = $found[1];

    $return_to = "http://open.vocab.org.local/";

    $url .= "?openid.mode=checkid_setup";
    $url .= "&openid.identity=".urlencode( $_POST['url'] );
    $url .= "&openid.return_to=".urlencode( $return_to );

    throw new k_http_Redirect($url);
  }
}
?>
