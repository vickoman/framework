<?php

   namespace Bitphp\Modules\Utilities;

   class Random {
      private static function generate($length, $pool) {
         $limit = (strlen($pool) - 1);
         $out = '';

         for ($i = 1;$i <= $length; $i++) {
            $out .= $pool[rand(0,$limit)];
         }

         return $out;
      }

      public static function string($length) {
         $pool    = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ012345678901234567899_';
         return self::generate($length, $pool);
      }

      public static function number($length) {
         $pool    = '012345678901234567899';
         return self::generate($length, $pool);
      }
   }