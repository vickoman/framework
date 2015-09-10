<?php

   namespace Bitphp\Base\Abstracts;

   use \Bitphp\Core\Globals;
   use \Bitphp\Modules\Http\Response;

   abstract class CliService {

      public function __construct() {
         Globals::registre('base_path', realpath(''));

         if($this->runnigInWebServer()) {
            Response::redir('/');
            exit;
         }
      }

      /**
       *   Verifica si la aplicacion esta corriendo 
       *   en un servidor web
       *
       *   @return bool
       */
      protected function runnigInWebServer() {
         if( !empty($_SERVER['SERVER_NAME']) )
            return true;

         return false;
      }

      abstract public function run();
   }