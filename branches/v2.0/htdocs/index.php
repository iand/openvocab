<?php
require_once 'localconfig.inc.php'; // defines the STORE_URI, USER_NAME and USER_PWD constants

define('SP_MARKDOWNDESCRIPTION', 'http://open.vocab.org/terms/markdownDescription');
define('VOCAB_SCHEMA', 'http://open.vocab.org/terms');
define('VOCAB_NS', VOCAB_SCHEMA . '/');
define('OV_APP_DIR', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR);
define('OV_LIB_DIR', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR);
define('MORIARTY_DIR', OV_LIB_DIR . 'moriarty' . DIRECTORY_SEPARATOR);

define('OV_ARC_DIR', OV_LIB_DIR . "arc_2008_08_04");
define('OV_KONSTRUKT_DIR', OV_LIB_DIR . "konstrukt" . DIRECTORY_SEPARATOR . "lib");
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
);

//require_once OV_LIB_DIR . DIRECTORY_SEPARATOR . 'httpclient' . DIRECTORY_SEPARATOR . 'http.php';
//require_once OV_LIB_DIR . DIRECTORY_SEPARATOR . 'sasl' . DIRECTORY_SEPARATOR . 'sasl.php';
require_once OV_APP_DIR . 'utility.func.php';

// Loads Konstrukt global symbols
require_once 'k.php';

// This is a default error-handler, which simply converts errors to exceptions
// Konstrukt doesn't need this setup, but it's a pretty sane choice.
// If this makes no sense to you, just let it be. It basically means that old-style errors are
// converted into exceptions instead. This allows a simpler error-handling.
error_reporting(E_ALL);
function exceptions_error_handler($severity, $message, $filename, $lineno) {
  if (error_reporting() == 0) {
    return;
  }
  if (error_reporting() & $severity) {
    throw new ErrorException($message, 0, $severity, $filename, $lineno);
  }
}
set_error_handler('exceptions_error_handler');

// This is a default exceptions-handler. For debugging, it's practical to get a readable
// trace dumped out at the top level, rather than just a blank screen.
// If you use something like Xdebug, you may want to skip this part, since it already gives
// a similar output.
// For production, you should replace this handler with something, which logs the error,
// and doesn't dump a trace. Failing to do so could be a security risk.
function debug_exception_handler($ex) {
  if (php_sapi_name() == 'cli') {
    echo "Error (code:".$ex->getCode().") :".$ex->getMessage()."\n at line ".$ex->getLine()." in file ".$ex->getFile()."\n";
    echo $ex->getTraceAsString()."\n";
  } else {
    echo "<p style='font-family:helvetica,sans-serif'>\n";
    echo "<b>Error :</b>".$ex->getMessage()."<br />\n";
    echo "<b>Code :</b>".$ex->getCode()."<br />\n";
    echo "<b>File :</b>".$ex->getFile()."<br />\n";
    echo "<b>Line :</b>".$ex->getLine()."</p>\n";
    echo "<div style='font-family:garamond'>".nl2br(htmlspecialchars($ex->getTraceAsString()))."</div>\n";
  }
  exit -1;
}
set_exception_handler('debug_exception_handler');





$application = new Root();
$application->dispatch();

?>
