<?php

   use \Bitphp\Core\Globals;

   class GlobalsTest extends \PHPUnit_Framework_TestCase {

      public function testToSetAndGetSomeParam() {
         Globals::registre('foo', 'bar');
         $actual = Globals::get('foo');
         $expect = 'bar';
         $this->assertEquals($expect, $actual);
      }
   }