<?php
require_once '../controllers/vocabdocs.php';

class TermDescriptionTest extends PHPUnit_Framework_TestCase {


  function test_is_property() {
    $thing_owlclass = new TermDescription('http://example.org/thing');
    $thing_owlclass->from_turtle('<http://example.org/thing> a <http://www.w3.org/2002/07/owl#Class> .');

    $this->assertFalse( $thing_owlclass->is_property() );
  }

}
?>
