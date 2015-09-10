<?php 
   
   namespace Bitphp\Base;

   use \Bitphp\Core\Globals;
   use \Bitphp\Base\MvcServer\Route;
   use \Bitphp\Base\Abstracts\Server;

   /**
    *   Implementacion del servidor base para crear 
    *   un servicio MVC
    *
    *   @author Eduardo B <eduardo@root404.com>
    */
   class MvcServer extends Server {

      /** Controlador solicitado */
      protected $controller;
      /** Fichero del controlador solicitado */
      protected $controller_file;
      /** Espacio de nombre del controlador */
      protected $controller_namespace;
      /** Accion del controlador a ejecutar */
      protected $action;

      /**
       *   Durante el constructor se parsea la ruta de la
       *   url solicitada para obtener el controlador, la
       *   accion y los parametros.
       */
      public function __construct() {
         parent::__construct();
    
         $route = Route::parse(Globals::get('request_uri'));
         extract($route);

         Globals::registre('uri_params', $params);

         $this->controller_namespace = '\App\Controllers\\';
         $this->controller_file =  Globals::get('app_path') . "/controllers/$controller.php";
         
         $this->controller = $controller;
         $this->action = $action;
      }

      /**
       *   Se verifica y carga el archivo del controlador
       *   y se ejecuta la accion (metodo) solicitado
       *
       *   @return void
       */
      public function run() {
         if(false === file_exists($this->controller_file)){
            $message  = "Error al cargar el controlador '$this->controller.' ";
            $message .= "El archivo del controlador '$this->controller_file' no existe";
            trigger_error($message);
            return;
         }
         
         # se deja al autocargador hacer su trabajo
         $full_class = $this->controller_namespace . $this->controller;
         $controller = new $full_class;

         if(!method_exists($controller, $this->action)) {
            $message  = "La clase del controlador '$this->controller' ";
            $message .= "no contiene el metodo '$this->action'";
            trigger_error($message);
            return;
         }

         call_user_func(array($controller, $this->action));
      }
   }