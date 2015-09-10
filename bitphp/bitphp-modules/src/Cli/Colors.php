<?php

   namespace Bitphp\Modules\Cli;

   class Colors {

      private static $linuxPallete = [
           'gray' => '[1;30m'
         , 'red' => '[0;31m'
         , 'green' => '[0;32m'
         , 'yellow' => '[0;33m'
         , 'blue' => '[0;34m'
         , 'purple' => '[0;35m'
         , 'cyan' => '[0;36m'
         , 'white' => '[0;37m'
         , 'bold_red' => '[1;31m'
         , 'bold_green' => '[1;32m'
         , 'bold_yellow' => '[1;33m'
         , 'bold_blue' => '[1;34m'
         , 'bold_purple' => '[1;35m'
         , 'bold_cyan' => '[1;49;36m'
         , 'bold_white' => '[1;37m'
         , 'back_red' => '[7;49;31m'
         , 'back_green' => '[7;49;92m'
         , 'back_white' => '[7;49;39m'
         , 'reset' => '[0m'
      ];

      public static function paint($string) {
         $sistem = substr(PHP_OS, 0, 3);
         $sistem = strtoupper( $sistem );

         foreach (self::$linuxPallete as $color => $value) {
            if( $sistem == 'WIN' ) {
               $value = '';
            } else {
               $value = chr(27) . $value;
            }

            $string = str_replace("[$color]", $value, $string);
         }

         return $string;
      }
   }