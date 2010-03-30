<?php
require_once 'localconfig.inc.php'; // defines the STORE_URI, USER_NAME and USER_PWD constants

define('SP_MARKDOWNDESCRIPTION', 'http://open.vocab.org/terms/markdownDescription');
define('VOCAB_SCHEMA', 'http://open.vocab.org/terms');
define('VOCAB_NS', VOCAB_SCHEMA . '/');
define('OV_APP_DIR', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR);
define('WWW_LIB_DIR', dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR);
define('OV_LIB_DIR', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR);
define('MORIARTY_DIR', WWW_LIB_DIR . 'moriarty' . DIRECTORY_SEPARATOR);

define('OV_ARC_DIR', WWW_LIB_DIR . "arc_2008_11_18");
define('OV_KONSTRUKT_DIR', WWW_LIB_DIR . "konstrukt" . DIRECTORY_SEPARATOR . "lib");
define('MORIARTY_ARC_DIR', OV_ARC_DIR . DIRECTORY_SEPARATOR);
define('MORIARTY_OPT_NO_ETAG', TRUE);
if (file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'cache')) {
  define('MORIARTY_HTTP_CACHE_DIR', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'cache');
}

//define('MORIARTY_HTTP_CACHE_READ_ONLY', TRUE ); // always use a cached response if one exists
define('MORIARTY_HTTP_CACHE_USE_STALE_ON_FAILURE', TRUE ); // use a cached respone if network fails

ini_set('include_path',
  ini_get('include_path')
  .PATH_SEPARATOR.OV_APP_DIR
  .PATH_SEPARATOR.OV_KONSTRUKT_DIR
  .PATH_SEPARATOR.OV_ARC_DIR
  .PATH_SEPARATOR.MORIARTY_DIR
  .PATH_SEPARATOR.'/home/iand/wip/paget2'
);

//require_once OV_LIB_DIR . DIRECTORY_SEPARATOR . 'httpclient' . DIRECTORY_SEPARATOR . 'http.php';
//require_once OV_LIB_DIR . DIRECTORY_SEPARATOR . 'sasl' . DIRECTORY_SEPARATOR . 'sasl.php';
require_once OV_APP_DIR . 'utility.func.php';


error_reporting(E_ALL);

require_once 'konstrukt/konstrukt.inc.php';

date_default_timezone_set('Europe/London');
set_error_handler('k_exceptions_error_handler');
spl_autoload_register('k_autoload');
k()
  // Enable file logging
  ->setLog(dirname(__FILE__) . '/logs/debug.log')
  // Uncomment the nexct line to enable in-browser debugging
  //->setDebug()
  // Dispatch request
  ->run('Root')
  ->out();



//$application = new Root();
//$application->dispatch();

?>
