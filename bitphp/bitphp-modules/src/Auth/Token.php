<?php

   namespace Bitphp\Modules\Auth;

   use \Exception;
   use \Bitphp\Core\Config;

   class Token {

      protected static $signature_key;
      protected static $algorithm;
      protected static $life;
      protected static $error;

      private static function b64UrlEncode($input) {
         $base64 = strtr(base64_encode($input), '+/', '-_');
         //to trim
         return str_replace('=', '', $base64);
      }

      private static function b64UrlDecode($input) {
         $length = strlen($input) % 4;
         if($length) {
            $padding = 4 - $length;
            $input .= str_repeat('=', $padding);
         }

         return base64_decode(strtr($input, '-_', '+/-'));
      }

      private static function sing($data, $key, $algorithm) {
         $algorithms = [
              'HS256' => 'sha256'
            , 'HS384' => 'sha384'
            , 'HS512' => 'sha512'
         ];

         if(!isset($algorithms[$algorithm]))
            throw new Exception("Wrong token algorithm '$algorithm'");

         return hash_hmac($algorithms[$algorithm], $data, $key, true);
      }

      private static function _decode($token) {
         $token = explode('.', $token);
         if(3 !== count($token))
            throw new Exception("Invalid token number of segments");
         
         $header = self::b64UrlDecode($token[0]);
         if(null === ($header = json_decode($header, true)))
            throw new Exception("Invalid token header encoding");

         $payload = self::b64UrlDecode($token[1]);
         if(null === ($payload = json_decode($payload, true)))
            throw new Exception("Invalid token payload encoding");

         if(empty($header['alg']))
            throw new Exception("Empty algorithm");
            
         $token_sing = self::b64UrlDecode($token[2]);
         $sing = self::sing("$token[0].$token[1]", self::$signature_key, $header['alg']);
         if($token_sing != $sing)
            throw new Exception("Invalid token signature");

         return $payload;
      }

      protected static function loadConfig() {
         self::$signature_key = Config::param('auth.token.signature');
         if(null === self::$signature_key)
            self::$signature_key = 'R4nd0mStr1ng_';

         self::$life = Config::param('auth.token.life');
         if(null === self::$life)
            self::$life = 300; //seconds

         self::$algorithm = Config::param('auth.token.algorithm');
         if(null === self::$algorithm)
            self::$algorithm = 'HS256';

         self::$error = null;
      }

      public static function encode(array $payload) {
         self::loadConfig();

         $token = array();
         $payload['iat'] = time();
         $payload['exp'] = time() + self::$life;

         $header = [
              'typ' => 'JWT'
            , 'alg' => self::$algorithm
         ];

         $token[] = self::b64UrlEncode(json_encode($header));
         $token[] = self::b64UrlEncode(json_encode($payload));

         $sing = self::sing(implode('.', $token), self::$signature_key, self::$algorithm);
         $token[] = self::b64UrlEncode($sing);

         return implode('.', $token);
      }

      public static function decode($token) {
         self::loadConfig();

         try {
            $payload = self::_decode($token);
         } catch (Exception $e) {
            self::$error = $e->getMessage();
            return null;
         }

         return $payload;
      }

      public static function error() {
         if(null !== self::$error)
            return self::$error;

         return false;
      }
   }