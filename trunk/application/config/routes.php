<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|   example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
| http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
| $route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
| $route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved
| routes must come before any wildcard or regular expression routes.
|
*/

$route['default_controller'] = "welcome";
$route['scaffolding_trigger'] = "";

if (config_item('term_delimiter') == '/') {
  $route[config_item('term_path') . '/([a-zA-Z0-9-]+)'] = "terms/do_303/$1";
  $route[config_item('term_path') . '/([a-zA-Z0-9-]+)\.html'] = "terms/do_html_redirect/$1";
  $route[config_item('term_path') . '/([a-zA-Z0-9-]+)\.rdf'] = "terms/do_rdf_redirect/$1";
  $route[config_item('term_path') . '/([a-zA-Z0-9-]+)\.ttl'] = "terms/do_ttl_redirect/$1";
  $route[config_item('term_path') . '/([a-zA-Z0-9-]+)\.json'] = "terms/do_json_redirect/$1";

  $route[config_item('term_document_path') . '/([a-zA-Z0-9-]+)'] = "termdocs/do_conneg/$1";
  $route[config_item('term_document_path') . '/([a-zA-Z0-9-]+)\.html'] = "termdocs/do_html/$1";
  $route[config_item('term_document_path') . '/([a-zA-Z0-9-]+)\.rdf'] = "termdocs/do_rdf/$1";
  $route[config_item('term_document_path') . '/([a-zA-Z0-9-]+)\.ttl'] = "termdocs/do_turtle/$1";
  $route[config_item('term_document_path') . '/([a-zA-Z0-9-]+)\.json'] = "termdocs/do_json/$1";
  $route[config_item('term_document_path') . '/([a-zA-Z0-9-]+)\.atom'] = "termdocs/do_atom/$1";
}


$route[config_item('change_path') . '$'] = "changes/do_conneg/$1";
$route[config_item('change_path') . '\.html$'] = "changes/do_html/$1";
$route[config_item('change_path') . '\.rdf$'] = "changes/do_rdf/$1";
$route[config_item('change_path') . '\.ttl$'] = "changes/do_turtle/$1";
$route[config_item('change_path') . '\.json$'] = "changes/do_json/$1";
$route[config_item('change_path') . '\.atom$'] = "changes/do_atom/$1";
$route[config_item('change_path') . '/([a-zA-Z0-9-]+)'] = "change/do_conneg/$1";
$route[config_item('change_path') . '/([a-zA-Z0-9-]+)\.html'] = "change/do_html/$1";
$route[config_item('change_path') . '/([a-zA-Z0-9-]+)\.rdf'] = "change/do_rdf/$1";
$route[config_item('change_path') . '/([a-zA-Z0-9-]+)\.ttl'] = "change/do_turtle/$1";
$route[config_item('change_path') . '/([a-zA-Z0-9-]+)\.json'] = "change/do_json/$1";


$route[config_item('term_path') . '$'] = "vocab/do_303/$1";
$route[config_item('term_document_path') . '$'] = "vocabdocs/do_conneg/$1";
$route[config_item('term_document_path') . '\.html$'] = "vocabdocs/do_html/$1";
$route[config_item('term_document_path') . '\.rdf$'] = "vocabdocs/do_rdf/$1";
$route[config_item('term_document_path') . '\.ttl$'] = "vocabdocs/do_turtle/$1";
$route[config_item('term_document_path') . '\.json$'] = "vocabdocs/do_json/$1";

$route['login'] = "login";
$route['create'] = "create";
$route['about'] = "about";
$route['about/rights'] = "about";
$route['about/privacy'] = "about";
$route['about/availability'] = "about";
$route['forms/newprop'] = "propertycontroller/add";
$route['forms/editprop'] = "propertycontroller/edit";
$route['forms/newclass'] = "classcontroller/add";
$route['forms/editclass'] = "classcontroller/edit";
$route['forms/deleteterm'] = "deleteterm";
$route['forms/recdelete'] = "recommenddeletion";
$route['forms/rectesting'] = "recommendtesting";


/* End of file routes.php */
/* Location: ./system/application/config/routes.php */


