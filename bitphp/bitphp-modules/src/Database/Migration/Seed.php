<?php
   
   namespace Bitphp\Modules\Database\Migration;

   use \ReflectionProperty;

   /**
    * ¡Es importante qué el usuario de la conexion tenga privilegios para crear bases de datos y tablas!
    * 
    * @author Eduardo B Romero
    */
   abstract class Seed {

      protected $database;

      protected $indexes = array();
      protected $keys = array();
      protected $primary_keys = array();
      protected $foreign_keys = array();
      protected $fields = array();

      public $executed_queries = array();


      /**
       * Determina si la tabla de la clase existe
       *
       * @return bool
       */
      private function tableExists($name) {
         $query = "SELECT 1 FROM $name LIMIT 1";
         $this->database->execute($query);

         $this->executed_queries[] = $query;

         if($this->database->error())
            return false;

         return true;
      }

      private function databaseExists($name) {
        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$name'";
        $this->database->execute($query);

        $this->executed_queries[] = $query;

        if(empty($this->database->result()))
          return false;

        return true;
      }

      protected function field($data) {
        $this->fields[] = "  $data";
        return $this;
      }

      protected function primaryKey($field) {
        $this->primary_keys[] = "  PRIMARY KEY ($field)";
        return $this;
      }

      protected function foreignKey($data) {
        $this->foreign_keys[] = "  FOREIGN KEY $data";
        return $this;
      }

      protected function key($data) {
        $this->keys[] = "  KEY $data";
        return $this;
      }

      protected function index($data) {
        $this->indexes[] = "  INDEX $data";
        return $this;
      }

      public function createDatabaseQuery($name) {
        $query = "CREATE DATABASE IF NOT EXISTS $name DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;";
        return $query;
      }

      /**
       * Crea una tabla en base a las propiedades PUBLICAS de la clase
       * 
       * @return void
       */
      public function createTableQuery($name) {

         $table_query = array_merge(
              $this->fields
            , $this->primary_keys
            , $this->foreign_keys
            , $this->keys
            , $this->indexes
         );

         $query = PHP_EOL . "CREATE TABLE IF NOT EXISTS $name (" . PHP_EOL;         
         $query .= implode(", " . PHP_EOL, $table_query);
         $query .= PHP_EOL . ") engine=innodb DEFAULT charset=utf8;";

         return $query;
      }

      /**
       * Crea la tabla y base de datos si no existen
       *
       * @return void
       */
      public function up() {
         $this->database = new $this->provider;
         $this->database_name = $this->database->realName($this->database_name);

         if(!$this->databaseExists($this->database_name)) {
            $query = $this->createDatabaseQuery($this->database_name);
            $this->database->execute($query);

            $this->executed_queries[] = $query;

            if(false !== ($error = $this->database->error()))
              trigger_error($error);
         }

         $this->database->database($this->database_name);

         if(!$this->tableExists($this->table_name)) {
            $query = $this->createTableQuery($this->table_name);
            $this->database->execute($query);

            $this->executed_queries[] = $query;
            
            if(false !== ($error = $this->database->error()))
              trigger_error($error);
         }
      }

      public function down($drop_db=false) {
        $this->database = new $this->provider;
        $this->database_name = $this->database->realName($this->database_name);
        
        if($drop_db) {
          $query = "DROP DATABASE IF EXISTS $this->database_name";
          $this->database->execute($query);

          $this->executed_queries[] = $query;

          if(false !== ($error = $this->database->error()))
              trigger_error($error);

          return;
        }

        $query = "DROP TABLE IF EXISTS $this->table_name";
        $this->database->database($this->database_name);
        $this->database->execute($query);

        $this->executed_queries[] = $query;

        if(false !== ($error = $this->database->error()))
              trigger_error($error);
      }

      abstract public function setup();
   }