<?php

   namespace Bitphp\Tests;

   class MockCache extends \Bitphp\Core\Cache {

      public static function getGenerateName($data) {
         return self::generateName($data);
      }
   }

   class CacheTest extends \PHPUnit_Framework_TestCase {

      protected static $file;

      protected function setUp() {
         \Bitphp\Core\Globals::registre('base_path', '/foo');

         self::$file = array();
         self::$file['params'] = [
              'foo' => 'abc123'
            , 'bar' => 'zxc456'
            , 'baz' => 'qwe789'
         ];
         self::$file['content'] = 'Something here';

         $hash = md5(json_encode(self::$file['params']));
         self::$file['name'] = '/foo/olimpus/cache/' . $hash . '.lock';
      }

      public function testToGenerateCacheFilePath() {

         $actual = MockCache::getGenerateName(self::$file['params']);
         $expect = self::$file['name'];
         $this->assertEquals($expect, $actual);
      }

      public function testToGetFile() {
         \Bitphp\Core\Config::set('cache', false);

         $actual = MockCache::isCached(self::$file['params']);
         $expect = false;
         $this->assertEquals($expect, $actual);
      }
   }