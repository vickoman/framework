<?php

   namespace Bitphp\Modules\Auth;

   use \Bitphp\Modules\Auth\Token;
   use \Bitphp\Modules\Http\Input;

   class Session {

      private static $initialized;
      protected static $payload = array();

      public static function set($var, $val) {
         if(is_array($var)) {
            foreach ($var as $_var => $_val) {
               self::set($_var, $_val);
            }

            return;
         }

         self::$payload[$var] = $val;
      }

      public static function get($var) {
         if(!isset(self::$payload[$var]))
            return null;

         return self::$payload[$var];
      }

      public static function verify() {
         $token = Input::cookie('__atoken');
         if(null === $token)
            return false;

         self::$payload = Token::decode($token);
         if(Token::error())
            return false;

         return true;
      }

      public static function start($remember=false) {
         self::$initialized = true;
         $token = Token::encode(self::$payload);

         // 94608000 seconds in 3 years
         $life = $remember ? time() + (94608000) : 0;
         setcookie('__atoken', $token, $life, '/', null, false, true);
      }

      public static function stop() {
         setcookie('__atoken', '', time() - 1);  
      }

      public static function initialized() {
         return self::$initialized;
      }
   }