<?php

   namespace Bitphp\Modules\Cli;

   class Arguments {

      public static function command() {
         array_shift($_SERVER['argv']);
         return implode(' ', $_SERVER['argv']);
      }

      public static function get($index) {
         $arguments = $_SERVER['argv'];

         if(!is_numeric($index)) {
            $index = array_search($index, $arguments);
            return self::get($index + 1);
         }

         if(isset($arguments[$index])) {
            return $arguments[$index];
         }

         return null;
      }

      public static function flag($flag) {
         $arguments = $_SERVER['argv'];

         $large_flag = array_search("--$flag", $arguments);
         $short_flag = array_search("-$flag[0]", $arguments);

         if($large_flag !== false)
            return "--$flag";

         if($short_flag !== false)
            return "-$flag[0]";

         return false;
      }
   }