<?php

   namespace Bitphp\Base;

   use \Closure;
   use \Exception;
   use \Bitphp\Core\Globals;
   use \Bitphp\Base\Abstracts\Server;
   use \Bitphp\Base\MicroServer\Route;
   use \Bitphp\Base\MicroServer\Pattern;
   use \Bitphp\Exceptions\HttpMethodException;
   use \Bitphp\Exceptions\UndefinedUriException;

   /**
    *   Implementacion del servidor base para crear 
    *   un servicio de clausulas (funciones) basadas
    *   en rutas
    *
    *   Para la ruta "/say/hello" ejecutar funcion X
    *
    *   @author Eduardo B <eduardo@root404.com>
    */
   class MicroServer extends Server {

      /** Rutas registradas */
      protected $routes;
      /** Metodos agregados dinamicamente a la clase */
      protected $binded;
      /** Ruta solicitada */
      public $action;
      /** Metodo http de la solicitud */
      public $method;

      /**
       *   Durante el contructor se obtiene informacion
       *   de la ruta, la ruta en si, el metodo http que
       *   se solicita
       */
      public function __construct() {
         parent::__construct();

         $route = Route::parse( Globals::get('request_uri') );

         Globals::registre('uri_params', $route['params']);
         $this->action = $route['action'];
         $this->method = $route['method'];            
         
         $this->routes = array();
         $this->binded = array();
      }

      /**
       *   Al ejecutar un metodo que en principio no
       *   existe en la clase, se verifica si este fue
       *   generado dinamicamente y si es asi se llama
       *
       *   @throw Exception cuando el metodo llamado definitivamente no existe
       */
      public function __call($method, array $args) {
         if(isset($this->binded[$method])) {
            return call_user_func_array($this->binded[$method], $args);
         }

         if(preg_match('/^do(\w+)$/', $method, $matches)) {
            $http_method = strtoupper($matches[1]);
            $route = $args[0];
            $callback = $args[1];
            return $this->registreRoute($http_method, $route, $callback);
         }

         if(preg_match('/^set(\w+)$/', $method, $matches)) {
            return $this->set(lcfirst($matches[1]), $args[0]);
         }

         throw new Exception('La clase ' . __CLASS__ . " no contiene el metodo $method", 1);
      }

      /**
       *  Registra una funcion para su respectiva ruta para el
       *  metodo http indicado
       *
       *  @param string $http_method Metodo http en formato UPERCASE
       *  @param string $route uri o ruta para la funcion
       *  @param Clousure $callback funcion que responde a la ruta indicada
       *  @return void
       */
      protected function registreRoute($http_method, $route, $callback) {
         $this->routes[$http_method][$route] = $callback;
      }

      /**
       *   Agrega metodos o propiedades dinamicamente
       *   a la clase MicroServer
       *
       *   @param string $item nombre del metodo o propiedad
       *   @param mixed $value valor, ya sea como el de una variable o un metodo
       *   @return void
       */
      public function set($item, $value) {
         if(is_callable($value)) {
            $this->binded[$item] = Closure::bind($value, $this, get_class());
            return;
         }

         $this->$item = $value;
      }

      /**
       *   Obtiene las rutas definidas para el metodo solicitado
       *   la compara mediante un patron regular previamente generado
       *   y si la ruta soolicitada a sido definida ejecuta su callback
       *   
       *   @throw Exception cuando la ruta solicitada no esta definida
       *   @return void
       */
      public function run() {
         if(!isset($this->routes[$this->method]))
            throw new HttpMethodException('Unused request method');

         foreach ($this->routes[$this->method] as $route => $callback) {
            $pattern = Pattern::create($route);
            if(preg_match($pattern, $this->action, $args)) {
              array_shift($args);
              call_user_func_array($callback, $args);
              return;
            }
         }

         throw new UndefinedUriException('Unused request uri');
      }
   }