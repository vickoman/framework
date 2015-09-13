<?php 

   namespace Bitphp\Core;
   
   use \Exception;
   use \Bitphp\Core\Globals;
   use \Bitphp\Core\Config;
   use \Bitphp\Core\Log;
   use \Bitphp\Modules\Layout\Medusa;

   /**
    *   Clase para registrar los error_handlers de bitphp
    *
    *   uso: $errorHandler = new \Bitphp\Core\Error();
    *       $errorHandler->registre();
    *
    *   requiere qué se haya registrado previamente la variable global
    *   "base_path" con la clase \Bitphp\Core\Globals
    *
    *   @author Eduardo B <eduardo@root404.com>
    */
   class Error {
      
      private $errors;
      private $file;
      private $medusa;

      private function encode($code, $message, $file, $line, $trace=null) {
        if(null === $trace) {
          $exception = new Exception();
          $trace = $exception->getTrace();
        }
        
        $context = [
            'code' => $code
          , 'message' => $message
          , 'file' => $file
          , 'line' => $line
          , 'trace' => $trace
          , 'request_uri' => Globals::get('base_url') . '/' . Globals::get('request_uri')
        ];

        $save_log = Config::param('errors.log');
        
        if(false !== $save_log)
          $context['identifier'] = $this->log->error($message, $context);

        $this->errors[] = $context;
      }

      /**
       *  Registra las funciones para el manejo de errores
       *
       */
      public function registre() {
          #ini_set('display_errors', 0);
          error_reporting(E_ALL);
          
          set_error_handler(array($this, 'globalErrorHandler'));
          register_shutdown_function(array($this, 'fatalErrorHandler'));
          set_exception_handler(array($this, 'exceptionHandler'));

          $this->errors = array();
          $this->log = new Log();
          $this->medusa = new Medusa();
          $this->medusa->views_path = Globals::get('base_path') . '/olimpus/system/pages';
      }

      /**
       *  Gestion de Excepciones no controladas
       *
       */
      public function exceptionHandler(Exception $exception) {
        $this->encode(
            $exception->getCode()
          , 'Excepción no controlada #' . $exception->getCode() . ' \'' . $exception->getMessage()
          , $exception->getFile()
          , $exception->getLine()
          , $exception->getTrace()
        );
      }

      /**
       *   Bitphp gestiona todos los errores de php
       *
       *   @param int $code codigo de error
       *   @param string $message mensaje del error
       *   @param string $file archivo donde se produjo el error
       *   @param int $line linea donde se produjo el error
       *   @return void
       */
      public function globalErrorHandler($code, $message, $file, $line) {
         $this->encode($code, $message, $file, $line);
      }

      /**
       *   Se ejecuta cuando el script finaliza y verifica si hubo errores fatales
       *   en caso de ser así carga la vista de error de bitphp
       *
       *   @return void
       */
      public function fatalErrorHandler() {      
         $error = error_get_last();
         
         if(null !== $error)
             $this->encode(E_ERROR, $error['message'], $error['file'], $error['line']);

         if (!empty($this->errors)) {
            $display = Config::param('errors.debug');
            if(false !== $display)
              $display = true;

            if($display) {
               $this->medusa->load('Error')
                            ->with([
                              'errors' => $this->errors
                            ])
                            ->draw();
            } else {
               header('HTTP/1.1 404 Not Found');
               $this->medusa->load('NotFound')
                            ->draw();
            }
         }
      }
   }