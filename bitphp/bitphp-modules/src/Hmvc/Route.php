<?php 

   namespace Bitphp\Modules\Hmvc;

   /**
    *   Recibe la url solicitada y en base a ella identifica 
    *   la aplicación, controlador, la accion, y los parametros.
    *
    *   @author Eduardo B Romero
    */
   class Route {


      /**
       *  Identifica la aplicacion en la uri
       *  recibida, debe ser el primer elemento
       *  si este no existe retorna "Home"
       *
       *  @param array $uri Uri solicitada
       *  @return string Aplicación qué se debe ejecutar
       */
      private static function application($uri) {
         if(empty($uri[0]))
            return 'Home';

         return ucwords($uri[0], '_');
      }

      /**
       *  Identifica el controlador en la uri
       *  recibida, debe ser el segundo elemento
       *  si este no existe retorna "<Application>\Main"
       *
       *  @param array $uri Uri solicitada
       *  @return string Controlador qué se debe ejecutar
       */
      private static function controller($uri) {
         if(empty($uri[1]))
            return 'Main';

         return ucwords($uri[1], '_');
      }

      /**
       *  Identifica la accion (funcion) en la uri
       *  recibida, debe ser el tercer elemento si
       *  este no existe retorna "__index"
       *
       *  @param array $uri Uri solicitada
       *  @return string Accion identificada
       */
      private static function action($uri) {
         # __index es la accion por defecto
         if(empty($uri[2]))
            return '__index';

         return $uri[2];
      }

      /**
       *  Recibe la uri solicitada y retorna un arreglo 
       *  con los componentes de esta
       *
       *  @param string $request_uri Uri solicitada
       *  @return array Arreglo asociativo de los elementos de la uri
       */
      public static function parse($request_uri) {
         $request_uri = trim($request_uri, '.');
         $request_uri = explode('.', $request_uri);

         $result = [
              'application' => self::application($request_uri)
            , 'controller' => self::controller($request_uri)
            , 'action' => self::action($request_uri)
         ];

         return $result;
      }
   }