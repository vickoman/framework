<?php

   namespace Bitphp\Core;

   use \Bitphp\Core\Globals;
   use \Bitphp\Modules\Utilities\File;

   class Log {

      /**
       *   Añade un registro de error al archivo de errores
       *   en formato JSON, retorna id del error si el registro
       *   fue satisfactorioo false si este falló
       *
       *   @param int $code indica el codigo de error
       *   @param string $message mensaje de error
       *   @param string $file archivo donde se produjo el error
       *   @param int $line linea donde se produjo el error
       *   @param array $trace trasa de pila
       *   @return mixed false si no se pudo guardar el error, un string
       *                  con el identificador del error si este se guardo
       */
      protected static function record($level, $message, $context) {
         # id es un hash md5 formado por la fecha y un numero aleatorio
         $date = date(DATE_ISO8601);
         $identifier  = md5($date . rand(0, 9999));

         $log = [
              'level' => $level
            , 'date' => $date
            , 'message' => $message
            , 'id' => $identifier
            , 'context' => $context
         ];

         $log = json_encode($log) . PHP_EOL;

         $log_file = Globals::get('base_path') . '/olimpus/log/errors.log';
         if(false === File::append($log_file, $log))
          return false;

         return $identifier;
      }

      public static function emergency($message, $context=array()) {
         return self::record('emergency', $message, $context);
      }

      public static function alert($message, $context=array()) {
         return self::record('alert', $message, $context);
      }

      public static function critical($message, $context=array()) {
         return self::record('critical', $message, $context);
      }

      public static function error($message, $context=array()) {
         return self::record('error', $message, $context);
      }

      public static function warning($message, $context=array()) {
         return self::record('warning', $message, $context);
      }

      public static function notice($message, $context=array()) {
         return self::record('notice', $message, $context);
      }

      public static function info($message, $context=array()) {
         return self::record('info', $message, $context);
      }

      public static function debug($message, $context=array()) {
         return self::record('debug', $message, $context);
      }
   }