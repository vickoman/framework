<?php

   namespace Bitphp\Modules\Database\Cadabra;

   /**
    * Generador de consultas para el ORM
    *
    * @author Eduardo B Romero
    */
   class QueryBuilder {
      protected $builded_query;
      protected $where_statement;
      public $table;

      public function __construct($table) {
         $this->table = $table;
      }

      public function __call($method, $args) {
         if(preg_match('/(and|or)Where/', $method, $match)) {
            array_shift($match);
            $args = array_merge($args, $match);
            return call_user_func_array(array($this, 'where'), $args);
         }
      }

      public function select(...$items) {
         $fields = '';

         foreach ($items as $item) {
            $fields .= "$item,";
         }

         $fields = trim($fields, ',');

         $this->builded_query = "SELECT $fields FROM $this->table";
         return $this;
      }

      public function update($params) {
         $values = array();
         foreach ($params as $key => $value) {
            $values[] = "$key='$value'";
         }

         $values = implode(',', $values);

         $this->builded_query = "UPDATE $this->table SET $values";
         return $this;
      }

      public function insert($params) {
         $keys = array();
         $values = array();

         foreach ($params as $key => $value) {
            $keys[] = $key;
            $values[] = "'$value'";
         }

         $keys = implode(',', $keys);
         $values = implode(',', $values);

         $this->builded_query = "INSERT INTO $this->table ($keys) VALUES ($values)";
         return $this;
      }

      public function delete() {
         $this->builded_query = "DELETE FROM $this->table";
         return $this;
      }

      public function where($query, $value, $prefix = 'OR') {
         if(!$this->where_statement) {
            $this->builded_query .= ' WHERE ';
            $this->where_statement = true;
            $prefix = null;
         }

         $this->builded_query .= " $prefix (" . str_replace('?', "'$value'", $query) . ')';
         return $this;
      }

      public function order($item, $type = 'DESC') {
         $this->builded_query .= " ORDER BY $item $type";
         return $this;
      }

      public function make() {
         return $this->builded_query;
      }
   }