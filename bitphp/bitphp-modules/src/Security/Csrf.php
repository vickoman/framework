<?php

   namespace Bitphp\Modules\Security;

   use \Bitphp\Modules\Auth\Session;
   use \Bitphp\Modules\Utilities\Random;
   use \Bitphp\Modules\Http\Input;

   class Csrf {

      public static function create() {
         if(Session::initialized()) {
            trigger_error("No se puede setear el csrf token despues de inicializar la sesion");
            return false;
         }

         $token = Random::string(16);
         Session::set('csrf_token', $token);
      }

      public static function verify($other_origin=false) {
         $csrf_token = $other_origin !== false ? $other_origin : Input::standard('csrf_token');
         if(!$csrf_token || ($csrf_token != Session::get('csrf_token')))
            return false;

         return true;
      }

      public static function token() {
         return Session::get('csrf_token');
      }
   }