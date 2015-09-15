<?php

   namespace Bitphp\Base;

   use \Bitphp\Modules\Cli\Arguments;
   use \Bitphp\Base\Abstracts\CliService;
   use \Bitphp\Base\CliApplication\Pattern;

   /**
    *   Clase que proporcina las bases para la creacion
    *   de aplicaciones de linea de comandos, se definen
    *   funciones que responden a ciertos comando, parecido
    *   a las clausulas basadas en rutas
    *
    *   @author Eduardo B <eduardo@root404.com>
    */
   class CliApplication extends CliService {

      /** Comandos registrados */
      protected $commands;
      /** Metodos agregados dinamicamente */
      protected $binded;

      /**
       *   El constructor registra el directorio base
       *   en las variables globales de bitphp
       */
      public function __construct() {
         parent::__construct();

         $this->commands = array();
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

         if(preg_match('/^set(\w+)$/', $method, $matches)) {
            return $this->set(lcfirst($matches[1]), $args[0]);
         }

         throw new Exception('La clase ' . __CLASS__ . " no contiene el metodo $method", 1);
      }

      /**
       *   Define la clausula (funcion) que responde al
       *   comando indicado
       *
       *   $cli->doCommand('mycli some_action', function() {
       *       echo 'I do something...';
       *   })
       *
       *   @param string $command comando para la funcion indicada
       *   @param Clousure $callback funcion que responde al comando
       *   @return void
       */
      public function doCommand($command, $callback) {
         $command = Pattern::create($command);
         $this->commands[$command] = $callback;
      }

      /**
       *   Agrega metodos o propiedades dinamicamente
       *   a la clase MicroServer
       *
       *   @param string $item nombre del metodo o propiedad
       *   @param mixed $value valor, ya sea como el de una variable o un metodo
       *   @return void
       */
      public function set( $item, $value ) {
         if(is_callable($value)) {
            $this->binded[$item] = Closure::bind($value, $this, get_class());
            return;
         }

         $this->$item = $value;
      }

      /**
       *   Verifica que no se este corriendo desde un servidor web
       *   si no es asi prosigue a buscar el comando ingresado en
       *   los comandos definidos, si lo encuentra ejecuta su callback
       *   si no fue definido el comando ingresado buscara el comando
       *   <b>default</b>, si este tampoco fue definido retornara falso
       *
       *   @return bool
       */
      public function run() {
         $executed = Arguments::command();

         foreach ($this->commands as $command => $callback) {
            if(@preg_match($command, $executed, $arguments)) {
               array_shift($arguments);
               call_user_func_array($callback, $arguments);
               return true;
            }
         }

         $default = Pattern::create('default');

         if(isset($this->commands[$default])) {
            call_user_func($this->commands[$default]);
            return true;
         }

         return false;
      }
   }