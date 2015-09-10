<?php

   namespace Bitphp\Modules\Hmvc;

   use \Bitphp\Core\Globals;
   use \Bitphp\Modules\Hmvc\Route;

   /**
    *    Clase qué permite realizar llamadas HMVC
    *    para una aplicación modular
    */
   class Service {

      private static $environment_backup;

      private static function createEnvironment($application) {

         self::$environment_backup = array(
            'app_path' => Globals::get('app_path')
         );

         Globals::registre([
            'app_path' => Globals::get('base_path') . "/app/modules/$application"
         ]);
      }

      private static function restoreEnvironment() {
         Globals::registre(self::$environment_backup);
      }

      private static function registreAutoload($application) {
         /**
          * Ya sea al autocargador de bitphp o el de composer
          * se llama $loader y se encuentra en el ambito global
          */
         global $loader;

         $loader->add("\\$application\\Models", "app/modules/$application/models");
         $loader->add("\\$application\\Controllers", "app/modules/$application/controllers");
      }

      /**
       *   Corre el modulo especificado
       *
       *   @param string $request Ruta del modulo a correr
       */
      public static function run($request, $_args = array(), $print = true) {
         $route = Route::parse($request);
         extract($route);

         self::createEnvironment($application);
         self::registreAutoload($application);
         
         $controller_file =  Globals::get('app_path') . "/controllers/$controller.php";
         if(!file_exists($controller_file)) {
            trigger_error("Error al correr '$request', el archivo '$controller_file' no existe.");
            return;
         }

         # se deja al autocargador hacer su trabajo
         $full_class = "\\$application\\Controllers\\" . $controller;
         $controller = new $full_class;

         if(!method_exists($controller, $action)) {
            $message  = "La clase del controlador '$controller' del modulo '$request' ";
            $message .= "no contiene el metodo '$action'";
            trigger_error($message);
            return;
         }

         ob_start();
         call_user_func_array(array($controller, $action), $_args);

         $result = ob_get_clean();
         self::restoreEnvironment();

         if(!$print) {
            return $result;
         }

         echo $result;
      }
   }