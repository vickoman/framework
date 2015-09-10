<?php

   namespace Bitphp\Modules\Cli;

   use \Bitphp\Modules\Cli\Colors;

   class StandardIO {

      public static function output( $string, $type = null, $new_line=PHP_EOL ) {
         
         $color = '';
         switch (strtolower($type)) {
            case 'emergency':
            case 'alert':
               $color = '[back_red]';
               break;

            case 'error':
            case 'critical':
               $color = '[bold_red]';
               break;

            case 'warning':
               $color = '[bold_yellow]';
               break;

            case 'notice':
               $color = '[bold_green]';
               break;

            case 'info':
               $color = '[bold_cyan]';
               break;

            case 'debug':
               $color = '[back_white]';
               break;

            default:
               $color = '[bold_white]';
               break; 
         }

         echo Colors::paint( $color . $string . '[reset]' . $new_line );
      }

      public static function input() {
         echo " ";
         return trim( fgets( STDIN ) );
      }
   }