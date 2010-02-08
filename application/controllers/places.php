<?php
require_once 'resourcedescription.php';
require_once 'widgets.php';

class Places extends ResourceDescription {
  
  
  function get_widgets() {
    $widgets = array();
    $widgets[] = new FeatureWidget();
    $widgets[] = new Widget('Other Information');
    return $widgets;
  }  
}
