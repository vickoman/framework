<?php

   namespace Bitphp\Tests;

   class MockConfig extends \Bitphp\Core\Config {

      protected static $files = array();

      public static function setFiles($files) {
         self::$files = $files;
      }

      public static function load($file) {
         if( isset(self::$files[$file]) ) {
            self::$params = self::$files[$file];
         }
      }
   }

   class ConfigTest extends \PHPUnit_Framework_TestCase {

      protected function setUp() {
         MockConfig::setFiles([
            'config.json' => [
                 'cache' => false
               , 'debug' => true
               , 'database.user' => 'foo'
            ]
         ]);

         MockConfig::load('config.json');
      }

      public function testToGetConfigParam() {
         //first
         $actual = MockConfig::param('cache');
         $expect = false;
         $this->assertEquals($expect, $actual);

         //second
         $actual = MockConfig::param('debug');
         $expect = true;
         $this->assertEquals($expect, $actual);

         //third
         $actual = MockConfig::param('database.user');
         $expect = 'foo';
         $this->assertEquals($expect, $actual);
      }

      public function testToGetNonExistentConfigParam() {
         $actual = MockConfig::param('imDontExists');
         $expect = null;
         $this->assertEquals($expect, $actual);
      }

      public function testToSetConfigParam() {
         MockConfig::set('bar', 'baz');
         $actual = MockConfig::param('bar');
         $expect = 'baz';
         $this->assertEquals($expect, $actual);
      }
   }