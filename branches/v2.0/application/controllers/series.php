<?php
require_once 'resourcedescription.php';
require_once 'widgets.php';

class Series extends ResourceDescription {
  
  function get_widgets() {
    $widgets = array();
    $widgets[] = new SeriesWidget();
    return $widgets;
  }  
  

}
