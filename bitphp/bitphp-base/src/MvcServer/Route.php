<?php 

   namespace Bitphp\Base\MvcServer;

   /**
    *   Recibe la url solicitada y en base a ella identifica 
    *   el controlador, la accion, y los parametros.
    *
    *   @author Eduardo B Romero
    */
   class Route {

      /**
       *  Identifica el controlador en la uri
       *  recibida, debe ser el primer elemento
       *  si este no existe retorna "main"
       *
       *  @param array $uri Uri solicitada
       *  @return string Controlador qué se debe ejecutar
       */
      private static function controller($uri) {
         if(empty($uri[0]))
            return 'Main';

         return ucwords($uri[0], '_');
      }

      /**
       *  Identifica la accion (funcion) en la uri
       *  recibida, debe ser el segundo elemento si
       *  este no existe retorna "__index"
       *
       *  @param array $uri Uri solicitada
       *  @return string Accion identificada
       */
      private static function action($uri) {
         # __index es la accion por defecto
         if(empty($uri[1]))
            return '__index';

         return $uri[1];
      }

      /**
       *  Retorna los parametros (variables)
       *  en la uri recibida, qué son los elemntos
       *  a partir del 3ro, en caso de no haber ninguno
       *  retorna un array vacio
       *
       *  @param array $uri Uri solicitada
       *  @return array parametros presentes en la uri
       */
      private static function uriParams($uri) {
         if(2 < count($uri)) {
            $params = $uri;
            unset($params[0], $params[1]);
            return array_values($params);
         }

         return array();
      }

      /**
       *  Recibe la uri solicitada y retorna un arreglo 
       *  con los componentes de esta
       *
       *  @param string $request_uri Uri solicitada
       *  @return array Arreglo asociativo de los elementos de la uri
       */
      public static function parse($request_uri) {
         $request_uri = trim($request_uri, '/');
         $request_uri = explode('/', $request_uri);

         $result = [
              'controller' => self::controller($request_uri)
            , 'action' => self::action($request_uri)
            , 'params' => self::uriParams($request_uri)
         ];

         return $result;
      }
   }