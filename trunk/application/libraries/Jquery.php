<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Jquery {

var $externalJquery='jquery.js';
var $jqueryScript='';

var $jqDocumentReady=false; //whether to automatically output $(document).ready()stuff

var $externalScripts=array();
var $errors=array();

//php4 constructor points to php5 constructor
function Jquery(){
  $this->__constructor();
}
//php5 constructor
function __constructor(){

  //from php.net
  //in php4 http_build_query is not available, this is a handwritten version
  //php5 has such a function //is used for POST and GET stuff
  if (!function_exists('http_build_query')) {
    function http_build_query($data, $prefix='', $sep='', $key='') {
       $ret = array();
       foreach ((array)$data as $k => $v) {
           if (is_int($k) && $prefix != null) $k = urlencode($prefix . $k);
           if (!empty($key)) $k = $key.'['.urlencode($k).']';
          
           if (is_array($v) || is_object($v))
               array_push($ret, http_build_query($v, '', $sep, $k));
           else    array_push($ret, $k.'='.urlencode($v));
       }
    
       if (empty($sep)) $sep = ini_get('arg_separator.output');
       return implode($sep, $ret);
    }
  }
}
//set the url of jquery.js //defaults to jquery.js but if you want to alter name/path 
function setExternalJquery($url){

  $this->externalJquery=$url;
}
//add external script like <script src='$url' type='text/javascript'>, loads into an array to be looped in the final jqeury process
function addExternalScript($url,$type='text/javascript'){
  $this->externalScripts[]='<script type="'.$type.'"  src="'.$url.'"></script>';
  return true;
}
//sets $this->jqueryScript
function setJqueryScript($script=''){

  $this->jqueryScript=$script;
  return true;

}
//appends $script to $this->jqueryScript
function addJqueryScript($script=''){

  $this->setJqueryScript($this->jqueryScript . $script);
  return true;  
}
//outputs the variable $this->jqueryScript (no <script> stuff, handy if you want to load some jquery via ajax)
function printJqueryScript(){
  echo $this->jqueryScript;
}
//If set to true $(document).ready(function(){ $this->jqueryScript }); will be outputted, instead of without the $(document).ready stuff //saves a line of code that is often used. Defaults to false
function setJqDocumentReady($int){
  $this->jqDocumentReady=$int;
  return true;
}
function jQueryError($error, $description=false){
  $this->errors[]=$error . ' - '. $description;
  return true;
}
///outputs the entire Jquery Javascript stuff, including the externally loaded scripts.
function processJquery(){
  $result= '<script type="text/javascript"  src="'.$this->externalJquery.'"></script>';

  foreach($this->externalScripts as $externalScript){
    $result.=$externalScript;
  }
  if(!empty($this->errors)) {
    $result.='<script type="text/javascript">';
    $result.='$(document).ready(function(){';
    $errors='';
    foreach($this->errors as $error)
      $errors.=$error;
    $result.='alert("'.trim($errors).'")';
    $result.='});';
    $result.='</script>';
    return $result;
  }
  
  $result.='<script type="text/javascript">';
  
  if($this->jqDocumentReady==true) $this->jqueryScript='$(document).ready(function(){'. $this->jqueryScript . '});';
  
  
  $result.=$this->jqueryScript;
  

  
  $result.='</script>';
  return $result;
}
  //outputs the script, include <script> stuff
  function printJquery(){
    echo $this->processJquery();
  }

  function JqueryObject($instanceName,$type){
  $type=strtolower($type);
  if($type=='ajax') $oName='JqueryAjax';
  elseif($type=='assign') $oName='JqueryAssign';
  elseif($type=='event') $oName='JqueryEvent';
  
  $this->$instanceName=new $oName();
  
  }
  function addJqueryObject($instanceName){
    $result=$this->$instanceName->getJquery();
    $this->addJqueryScript($result);
  }
  function getJqueryObject($instanceName){
    $result=$this->$instanceName->getJquery();
    return $result;//$this->getJqueryScript($result);
  }


}

class JqueryEvent{

  var $script;
  var $eventType='bind';
  var $value='ddddd';
  var $elementName;
  var $objectName;
  var $event='click';
  function JqueryEvent(){}

  function assignEventTo($elementName){
  $this->elementName=$elementName;
  }
  function assignJavascript($script){
  $this->script=$script;
  }
  function assignType($eventType){ //Bind, Unbind that list
    $this->eventType=$eventType;
  }
  function assignEvent($event){
    $this->event=$event;
  }
  function getJquery(){
   return '$("'.$this->elementName.'").'.$this->eventType.'("'.$this->event.'",'.$this->script.');';

  }
}
class JqueryAjax{

var $ajaxTypes=array();
var $ajaxDataTypes=array();

var $requestType='POST';
var $datatype='html';
var $data=array();
var $url=false;
var $success_function=false;
var $errors=array();
function JqueryAjax(){

$this->__constructor();
}
function __constructor($dd='',$ss='',$sa=''){
  $this->ajaxTypes=array('POST','GET');
  $this->ajaxDataTypes=array('html','script','xml','json');
  $this->requestType='POST';
  


}


function jQueryAjaxError($error, $description=false){
  $this->errors[]=$error . ' - '. $description;
  print_r($this->errors);
  die();
}
function setDataType($datatype){

  $this->datatype=strtolower($datatype);
  if(!in_array($this->datatype,$this->ajaxDataTypes))  $this->jQueryAjaxError('Wrong datatype given','$datatype = '.$this->datatype);
}
function setRequestType($requestType){
  $this->requestType=strtoupper($requestType);
    if(!in_array($this->requestType,$this->ajaxTypes))  $this->jQueryAjaxError('Wrong type given','$type = '.$this->requestType);
}
function setRequestUrl($url){
  $this->url=$url;
}
function setSuccessFunction($script){
$this->success_function=$script;
}
function setDataRaw($data){
$this->data=$data;

}
  function setData($data){
  
    if(!$this->data=http_build_query($data))  $this->jQueryAjaxError('Wrong data given','$data'.$this->data);
  }
  function getJquery(){
    
  
  $ajaxCall='$.ajax({';
  $ajaxCall.=' type: "'.$this->requestType .'"';
  $ajaxCall.=', url: "'.$this->url.'"';
  $ajaxCall.=', data: "'.$this->data.'"' ;
  $ajaxCall.=', dataType: "'.$this->datatype.'"' ;
  if($this->success_function!=false) $ajaxCall.=',success: function(data) {' .$this->success_function .'}';
   
  $ajaxCall.='});';
  
  return $ajaxCall;
  }


}

class JqueryAssign{
  var $script;
  var $location='after';
  var $value='ddddd';
  var $elementName;
  var $objectName;

  
  function JqueryAssign(){

  
  }
  function assignValueTo($elementName){
  $this->elementName=$elementName;
  }
  function assignValue($value){

  $this->value=$value;
  }
  function assignLocation($location){
  $this->location=$location;
  }
  function getJquery(){
  
  return '$("'.$this->elementName.'").'.$this->location.'(" '.$this->value.' " );';
  }
}
