<?php

   namespace Bitphp\Modules\Utilities;

   /**
    * clase para validacion de datos
    *
    * @author Eduardo B Romero
    */
   class ItIs {

      public static function url($subject) {
         return filter_var($subject, FILTER_VALIDATE_URL);
      }

      public static function ipAddress($subject) {
         return filter_var($subject, FILTER_VALIDATE_IP);
      }

      public static function integer($subject) {
         return filter_var($subject, FILTER_VALIDATE_INT);
      }

      public static function float($subject) {
         return filter_var($subject, FILTER_VALIDATE_FLOAT);  
      }

      public static function email($subject) {
         return filter_var($subject, FILTER_VALIDATE_EMAIL);
      }

      public static function boolean($subject) {
         return filter_var($subject, FILTER_VALIDATE_BOOLEAN);
      }

      public static function securePass($subject, $min=8) {
         return preg_match('/(?=^.{'.$min.',}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/', $subject);
      }

      public static function phoneNumber($subject) {
         return preg_match('/^\+?\d{1,3}?[- .]?\(?(?:\d{2,3})\)?[- .]?\d\d\d[- .]?\d\d\d\d$/', $subject);
      }

      public static function creditCardNumber($subject) {
         return preg_match('/^((67\d{2})|(4\d{3})|(5[1-5]\d{2})|(6011))(-?\s?\d{4}){3}|(3[4,7])\ d{2}-?\s?\d{6}-?\s?\d{5}$/', $subject);
      }

      public static function postalCode($subject) {
         return preg_match('/^([1-9]{2}|[0-9][1-9]|[1-9][0-9])[0-9]{3}$/', $subject);
      }

      public static function validUserName($subject, $min=4, $max=15) {
         return preg_match('/^[a-z\d_]{'.$min.','.$max.'}$/i', $subject);
      }
   }