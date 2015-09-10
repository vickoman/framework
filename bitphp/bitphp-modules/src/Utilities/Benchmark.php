<?php

   namespace Bitphp\Modules\Utilities;

   class Benchmark {
      
      protected static $points = array();

      public static function point($name) {
         self::$points[$name] = microtime(true);
      }

      public static function elapsedTime($point1='start', $point2='end', $decimals=4) {
         if(!isset(self::$points[$point1]))
            self::$points[$point1] = $_SERVER['REQUEST_TIME_FLOAT'];

         if(!isset(self::$points[$point2]))
            self::$points[$point2] = microtime(true);

         $time = self::$points[$point2] - self::$points[$point1];
         return number_format($time, $decimals);
      }

      public static function memory() {
         $unit = array('b','kb','mb','gb','tb','pb');
         $memory = memory_get_usage();
         return round($memory/pow(1024,($i=floor(log($memory,1024)))),2).' '.$unit[$i];
      }

      public static function included_files() {
         return get_included_files();
      }
   }