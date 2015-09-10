<?php
   
   namespace Bitphp\Core;

   $_BITPHP = array();

   /**
    *   Registra y menja las variables globales de bitphp
    *
    *   @author Eduardo B <eduardo@root404.com>
    */
   class Globals {

      protected static $variables;

      /**
       *   Registra una variable en el ambito global
       *   tambien puede registrar varias variables pasando
       *    $var como un arreglo asosiativo de ["var" => "val"]
       *
       *   @param mixed $var nombre de la variable
       *   @param mixed $val valor de la variable
       */
      public static function registre($var, $val = null) {

         if(is_array($var)) {
            foreach ($var as $name => $value) {
               self::registre($name, $value);
            }
            return;
         }

         self::$variables[$var] = $val;
      }

      /** 
       *   Retorna el valor de una variable global
       *
       *   @param string $val nombre de la variable
       *   @return mixed null si la variable no esta registrada y su valor si existe
       */
      public static function get($var) {
         return isset(self::$variables[$var]) ? self::$variables[$var] : null;
      }

      /**
       *   Retorna todas las variables globales y sus valores
       *
       *   @return array
       */
      public static function all() {
         return self::$variables;
      }
   }