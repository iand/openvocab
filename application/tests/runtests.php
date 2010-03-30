<?php
//define('BASEPATH', dirname(__file__));
if (file_exists('/home/iand/web/lib/')) {
  define('LIB_DIR', '/home/iand/web/lib/');
}
else {
  define('LIB_DIR', '/var/www/lib/');
}

define('MORIARTY_DIR', LIB_DIR . 'moriarty' . DIRECTORY_SEPARATOR);
define('MORIARTY_ARC_DIR', LIB_DIR . 'arc_2008_11_18' . DIRECTORY_SEPARATOR);
define('MORIARTY_TEST_DIR', MORIARTY_DIR . 'tests/');

if (!defined('PHPUnit_MAIN_METHOD')) {
  define('PHPUnit_MAIN_METHOD', 'OpenVocab_AllTests::main');
}

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once MORIARTY_TEST_DIR . 'fakehttprequest.class.php';
require_once MORIARTY_TEST_DIR . 'fakerequestfactory.class.php';

require_once dirname(__file__) . '/termdescription.test.php';
require_once dirname(__file__) . '/rdfmodel.test.php';



/*
|---------------------------------------------------------------
| SYSTEM FOLDER NAME
|---------------------------------------------------------------
|
| This variable must contain the name of your "system" folder.
| Include the path if the folder is not in the same  directory
| as this file.
|
| NO TRAILING SLASH!
|
*/
  $system_folder = LIB_DIR . "codeigniter/system";

/*
|---------------------------------------------------------------
| APPLICATION FOLDER NAME
|---------------------------------------------------------------
|
| If you want this front controller to use a different "application"
| folder then the default one you can set its name here. The folder
| can also be renamed or relocated anywhere on your server.
| For more info please see the user guide:
| http://codeigniter.com/user_guide/general/managing_apps.html
|
|
| NO TRAILING SLASH!
|
*/
  $application_folder = "../application";

/*
|===============================================================
| END OF USER CONFIGURABLE SETTINGS
|===============================================================
*/


/*
|---------------------------------------------------------------
| SET THE SERVER PATH
|---------------------------------------------------------------
|
| Let's attempt to determine the full-server path to the "system"
| folder in order to reduce the possibility of path problems.
| Note: We only attempt this if the user hasn't specified a
| full server path.
|
*/
if (strpos($system_folder, '/') === FALSE)
{
  if (function_exists('realpath') AND @realpath(dirname(__FILE__)) !== FALSE)
  {
    $system_folder = realpath(dirname(__FILE__)).'/'.$system_folder;
  }
}
else
{
  // Swap directory separators to Unix style for consistency
  $system_folder = str_replace("\\", "/", $system_folder);
}

/*
|---------------------------------------------------------------
| DEFINE APPLICATION CONSTANTS
|---------------------------------------------------------------
|
| EXT   - The file extension.  Typically ".php"
| FCPATH  - The full server path to THIS file
| SELF    - The name of THIS file (typically "index.php")
| BASEPATH  - The full server path to the "system" folder
| APPPATH - The full server path to the "application" folder
|
*/
define('EXT', '.'.pathinfo(__FILE__, PATHINFO_EXTENSION));
define('FCPATH', __FILE__);
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('BASEPATH', $system_folder.'/');

if (is_dir($application_folder))
{
  define('APPPATH', $application_folder.'/');
}
else
{
  if ($application_folder == '')
  {
    $application_folder = 'application';
  }

  define('APPPATH', BASEPATH.$application_folder.'/');
}



class OpenVocab_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('OpenVocab Tests');

//        $suite->addTestSuite('TermDescriptionTest');
        $suite->addTestSuite('RDFModelTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'OpenVocab_AllTests::main') {
    OpenVocab_AllTests::main();
}

?>
