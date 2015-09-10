<?php

   use \Bitphp\Base\MvcServer\Route;

   class RouteTest extends \PHPUnit_Framework_TestCase {

      protected $route;

      protected function compareArrays($array1, $array2) {
         $count = 0;

         foreach ($array1 as $element) {
            if( $array2[$count] != $element ) {
               return false;
            }

            $count++;
         }

         return true;
      }

      protected function setUp() {
         $this->route1 = '/foo/bar/baz/1/2/';
         $this->route2 = 'foo/bar/baz/1/2';
         $this->route3 = '/';
      }

      public function testParseController() {
         $actual = Route::parse($this->route1)['controller'];
         $expect = 'foo';
         $this->assertEquals($expect, $actual);

         $actual = Route::parse($this->route2)['controller'];
         $expect = 'foo';
         $this->assertEquals($expect, $actual);

         $actual = Route::parse($this->route3)['controller'];
         $expect = 'main';
         $this->assertEquals($expect, $actual);
      }

      public function testParseAcction() {
         $actual = Route::parse($this->route1)['action'];
         $expect = 'bar';
         $this->assertEquals($expect, $actual);

         $actual = Route::parse($this->route2)['action'];
         $expect = 'bar';
         $this->assertEquals($expect, $actual);

         $actual = Route::parse($this->route3)['action'];
         $expect = '__index';
         $this->assertEquals($expect, $actual);
      }

      public function testUriParams() {
         $actual = Route::parse($this->route1)['params'];
         $expect = ['baz', 1, 2];
         $this->assertTrue($this->compareArrays($expect, $actual));

         $actual = Route::parse($this->route2)['params'];
         $expect = ['baz', 1, 2];
         $this->assertTrue($this->compareArrays($expect, $actual));

         $actual = Route::parse($this->route3)['params'];
         $expect = [];
         $this->assertTrue($this->compareArrays($expect, $actual));
      }
   }