<?php

   namespace Bitphp\Base\MicroServer;

   /**
    * Clase qué identificar los parametros de entrada
    * para la arquitectura de micro MVC
    *
    * @author Eduardo B Romero
    */
   class Route {

      /**
       * Identifica el metodo http de la solicitud
       *
       * @return string
       */
      private static function requestMethod() {
         return $_SERVER['REQUEST_METHOD'];
      }

      /**
       * Identifica la ruta solicitada
       *
       * @return string
       */
      private static function action( $uri ) {
         if(empty($uri))
            return '/';

         return '/' . rtrim($uri, '/');
      }

      /**
       * Identifica los parametros de la url
       *
       * @return array
       */
      private static function uriParams( $uri ) {
         # /parametro1/parametro2/etc
         if(!empty($uri)) {
            return $uri;
         }

         # si no hay parametros retorna un array vacio
         return array();
      }

      /**
       * Retorna un arreglo con los elementos de la url solicitada
       *
       * @return array
       */
      public static function parse( $request_uri ) {
         $array = trim($request_uri, '/');
         $array = explode('/', $array);

         $result = [
              'action' => self::action($request_uri)
            , 'params' => self::uriParams($array)
            , 'method' => self::requestMethod()
         ];

         return $result;
      }
   }