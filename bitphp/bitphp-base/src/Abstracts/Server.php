<?php 

   namespace Bitphp\Base\Abstracts;

   use \Bitphp\Core\Error;
   use \Bitphp\Core\Config;
   use \Bitphp\Core\Globals;

   /**
    *   Base para los distintos tipos de servicios
    *   http de bitphp, se encarga de cargar la 
    *   configuracion, setar parametros como la url
    *   base, el directorio base, registrar variables
    *   globales de bitphp y el manejador de errores
    *
    *   @author Eduardo B <eduardo@root404.com>
    */
   abstract class Server {

      /**
       *   Durante el constructor se registra el directorio
       *   base de la aplicacion, la url base completa del la
       *   aplicacion y la url solicitada, se carga el archivo 
       *   de configuracion y se registra el Error handler de 
       *   bitphp
       */
      public function __construct() {

         Globals::registre([
              'base_path' => realpath('')
            , 'base_url' => $this->getBaseUrl()
            , 'request_uri' => $this->getRequestUri()
            , 'app_path' => realpath('') . '/app'
         ]);

         Config::load('app');
         $errorHandler = new Error();
         $errorHandler->registre();
      }

      /**
       *   Crea una direccion base del servidor 
       *   eg. http://foo.com/
       *       https://foo.com/test
       *   Dependiendo de donde se encuentre
       *
       *   @return string Url base, completa, del servidor
       */
      private function getBaseUrl() {
         $base_url  = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
         $base_url .= $_SERVER['SERVER_NAME'];
         $dirname = dirname($_SERVER['PHP_SELF']);
         $base_url .= $dirname == '/' ? '' : $dirname;
         return $base_url;
      }

      /**
       *  Devuelve la url solicitada que se genera en
       *  $_GET['_bitphp'] a traves del htaccess
       *
       *  @return string Uri solicitada
       */
      private function getRequestUri() {
         return filter_input(INPUT_GET, '_bitphp', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      }

      /**
       *  Debe ser implementada por la aplicacion servidor hija
       */
      abstract public function run();
   }