<?php
class ParticipateController extends k_Controller
{
  function GET() {
    $params = Array();
    return $this->render("templates/participate.tpl.php", $params);
  }
}
?>
