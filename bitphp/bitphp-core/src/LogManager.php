<?php

   namespace Bitphp\Core;

   use \Bitphp\Core\Globals;
   use \Bitphp\Modules\Utilities\TimeDiff;
   use \Bitphp\Modules\Utilities\File;

   /**
    * Clase para la manipulacion y control del archivo de logeo de errores
    *
    * @author Eduardo B Romero
    */
   class LogManager {

      /**
       * Retorna un arreglo qué contiene las cadenas json
       * de los errores registrados
       *
       * @return array
       */
      public static function getArrayLog() {
         $log_file = Globals::get('base_path') . '/olimpus/log/errors.log';
         $logs = File::read($log_file);
         
         if(!$logs)
            return array();

         return explode("\n", $logs);
      }

      /**
       * Elimina el archivo de registro de errores
       *
       * @return bool Segun el caso de exito o fracaso en la accion
       */
      public static function dump() {
         return @unlink('olimpus/log/errors.log');
      }

      /**
       *
       */
      public static function search($search) {
         $errors = self::getArrayLog();

         foreach ($errors as $error) {
            $error = json_decode($error, true);

            if(empty($error))
               continue;

            $id   = $error['id'];

            if($search == $id)
               return $error;
         }

         return false;
      }
   }