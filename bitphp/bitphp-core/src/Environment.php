<?php

   namespace Bitphp\Core;

   class Environment {

      protected static $info;

      /**
       *  Obtiene los datos de /config/environment.json
       *  si "current" no esta seteado lo pone en "develop"
       *
       *  @return array
       */
      public static function info() {
        if(null !== self::$info)
            return self::$info;

        $path = Globals::get('base_path') . '/config/environment.json';

        if(file_exists($path)) {
          $content = file_get_contents($path);
          self::$info = json_decode($content, true);
          
          if(empty(self::$info['current']))
            self::$info['current'] = 'develop';

          return self::$info;
        }

        self::$info = array('current' => 'develop');
        return self::$info;
      }

      /**
       * Alterna entre los entornos disonibles
       *
       * @param string $next_environment Entorno a utilizar, si no se indica se 
       *                                 se utilizara el siguiente disponible
       */
      public static function doSwitch($next_environment=null) {
         $environment = self::info();

         if(empty($environment['available'])) {
            return false;
         }

         if($next_environment === null) {
            $index = (array_search($environment['current'], $environment['available'])) + 1;
            
            if($index >= count($environment['available']))
               $index = 0;

         } else {
            $index = array_search($next_environment, $environment['available']);
            if(false === $index)
               return false;
         }

         $environment['current'] = $environment['available'][$index];

         $info = json_encode($environment, JSON_PRETTY_PRINT);

         $path = Globals::get('base_path') . '/config/environment.json';
         if(false === (@file_put_contents($path, $info)))
            return false;

         return $environment['current'];
      }
   }