<?php
   
   namespace Bitphp\Modules\Database\Cadabra;

   use \ReflectionProperty;
   use \PDOException;

   /**
    * Trait para el mapeo relacional
    * 
    * @author Eduardo B Romero
    */
   trait Mapper {

      /**
       * Determina el nombre de la bd en base nombre de espacio
       *
       * @return string Nombre de la base de datos
       */
      private static function databaseName() {
         $parts = explode('\\', __CLASS__);
         return strtolower($parts[count($parts) - 2]);
      }

      /**
       * Determina el nombre de la tabla en base nombre de espacio
       *
       * @return string Nombre de la tabla
       */
      private static function tableName() {
         $parts = explode('\\', __CLASS__);
         return strtolower($parts[count($parts) - 1]);
      }

      /**
       * Crea el objeto del proveedor en $this->database
       * Determina el nombre de la base de datos y la conecta a través del proveedor
       * Determina el nombre de la base de datos y la setea en $this->database
       * Crea la tabla si no existe
       *
       * @return array
       */
      public function map() {
         $map = array();

         if(!isset($this->provider))
          trigger_error('No se indico ningún proveedor de base de datos');

         $map['provider'] = $this->provider;
         
         $map['database'] = $this->databaseName();
         if(isset($this->alias))
            $map['database'] = 'alias.' . $map['database'];

         $map['table'] = $this->tableName();
         return $map;
      }
   }