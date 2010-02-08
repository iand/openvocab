<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
  define('PHPUnit_MAIN_METHOD', 'OpenVocab_AllTests::main');
}

require_once MORIARTY_PHPUNIT_DIR . 'PHPUnit' . DIRECTORY_SEPARATOR . 'Framework.php';
require_once MORIARTY_PHPUNIT_DIR . 'PHPUnit' . DIRECTORY_SEPARATOR . 'TextUI' . DIRECTORY_SEPARATOR . 'TestRunner.php';

require_once MORIARTY_TEST_DIR . 'fakehttprequest.class.php';
require_once MORIARTY_TEST_DIR . 'fakerequestfactory.class.php';

require_once dirname(__file__) . '/termdescription.test.php';

class OpenVocab_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('OpenVocab Tests');

        $suite->addTestSuite('TermDescriptionTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'OpenVocab_AllTests::main') {
    OpenVocab_AllTests::main();
}

?>
