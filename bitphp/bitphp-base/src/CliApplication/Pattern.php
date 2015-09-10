<?php
   
   namespace Bitphp\Base\CliApplication;

   /**
    * Convierte las uri's de las rutas de comandos
    * en patrones regulares
    *
    * @author Eduardo B Romero
    */
   class Pattern {

      /**
       *  Crea el patron regular de la uri del comando
       *
       *  @param string $command Uri del comando para convertir
       *  @return string
       */
      public static function create($command) {
         $search = [
              '/(\s+)/'
            , '/\((\$\w+)?\)/'
         ];

         $replace = [
              '[\s+]'
            , '(\S+)'
         ];

         return '/^' . preg_replace($search, $replace, $command) . '/x';
      }
   }