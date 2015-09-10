<?php

   namespace Bitphp\Modules\Database\Cadabra;

   /**
    *  Orm sencillo, por defecto proporciona metodos para CRUD
    *
    *  @author Eduardo B Romero
    */
   abstract class Orm {

      /** Nombre de la tabla a la qué se conecta */
      protected $table;
      /** Nombre de la base de datos */
      protected $database;
      /** Generador de consultas */
      protected $builder;

      /**
       *   El constructor manda llamar la funcion map del trait
       */
      public function __construct($connect=true) {
         if($connect) {
            /* Implemented on mapper */
            $map = $this->map();

            if(!$map['provider'])
              trigger_error('No se indico proveedor de base de datos');

            $this->database = new $map['provider'];
            $this->database->database($map['database']);

            if(false !== ($error = $this->database->error()))
              trigger_error($error);

            $this->table = $map['table'];
         }
      }

      /**
       * Convierte un array asosiativo en propiedades de una clase
       *
       * @param array $properties Array asocietivo para convertir en propiedades
       * @return Object objeto de la clase (hijo) con los valores del array seteados como propiedades
       */
      protected function factory($properties) {
         $class = get_class($this);
         $object = new $class;

         foreach ($properties as $property => $value) {
            $object->$property = $value;
         }

         $object->{"array_vars"} = $properties;
         return $object;
      }

      /**
       *  Retorna un array de las propiedades de la clase, despues
       *  de qué la clase paso por factory()
       *
       *  @return array
       */
      public function toArray() {
        if(isset($this->array_vars))
          return $this->array_vars;

        return array();
      }

      /**
       * Ejecuta una consulta de actualizacion donde $property sea igual a $conditional
       * $property por defecto es 'id', su valor, por defecto lo lee de $this->$property
       *
       * @param array $params Parametros para actualizar
       * @param string $property Propiedad a tomar en cuenta para la condicional de actualizacion
       * @param mixed $conditional Valor para la condicional
       * @return bool True, si la consulta fue exitosa, false de lo contrario
       */
      public function update($params, $property = 'id') {
         $values = '';
         foreach ($params as $key => $value) {
            $values .= "$key='$value',";
         }

         $values = trim($values, ',');

         if(!isset($this->$property)) {
            trigger_error("No se pudo actualizar, '$property' no es un campo");
            return false;
         }

         $conditional = $this->$property;

         $query = "UPDATE $this->table SET $values WHERE $property='$conditional'";
         $this->database->execute($query);
         if(false !== ($error = $this->database->error())) {
            trigger_error($error);
            return false;
         }

         return $this;
      }

      /**
       * Crea un registro en la tabla
       *
       * @param array asociativo con los valores a insertar
       * @return bool True, si la consulta fue exitosa, false de lo contrario
       */
      public function create(array $params) {
         $keys = '';
         $values = '';

         foreach ($params as $key => $value) {
            $keys = "$key,";
            $values = "'$value',";
         }

         $keys = trim($keys, ',');
         $values = trim($values, ',');

         $query = "INSERT INTO $this->table ($keys) VALUES ($values)";
         $this->database->execute($query);
         if(false !== ($error = $this->database->error())) {
            trigger_error($error);
            return false;
         }

         return $this->factory($params);
      }

      /**
       * Realiza una consulta para encontrar el registro con el valor $value en $item
       *
       * @param mixed $value Valor qué debe tener $item para la condicional
       * @param string $item Elemento para la condicional, id por defecto
       * @return Object Objeto de la clase relacional
       */
      public function find($value, $item='id') {
         $query = "SELECT * FROM $this->table WHERE $item='$value'";
         $result = $this->database->execute($query)
                                  ->result();

         if(!empty($result))
            return $this->factory($result[0]);

         return null;
      }

      /**
       * Realiza una consulta para borrar el registro con el valor $value en $item
       *
       * @param mixed $value Valor qué debe tener $item en la condicional
       * @param string $item Elemento para la condicional, id por defecto
       * @return bool True, si la consulta fue exitosa, false de lo contrario
       */
      public function delete($property='id') {
         if(!isset($this->$property)) {
            trigger_error("No se pudo borrar, '$property' no es un campo");
            return false;
         }

         $conditional = $this->$property;

         $query = "DELETE FROM $this->table WHERE $property='$conditional'";
         $this->database->execute($query)
                        ->result();

         if(false !== ($error = $this->database->error())) {
            trigger_error($error);
            return false;
         }

         return true;
      }

      /**
       *  Realiza una consulta para obtener todos los registros de la tabla
       *
       * @param string $order Fragmento de consulta para indicar el orden, por defecto null
       * @return array Areglo de objetos de la clase relacional, null si la tabla esta vacia o falla la consulta
       */
      public function all($order = null) {
         $objects = array();
         $result = $this->database->execute("SELECT * FROM $this->table " . $order)
                                  ->result();

         if(false !== ($error = $this->database->error())) {
            trigger_error($error);
            return null;
         }

         if(!empty($result)) {
            foreach ($result as $array) {
               $objects[] = $this->factory($array);
            }

            return $objects;
         }

         return null;
      }

      public function findAll($item, $value, $order = null) {
         $objects = array();
         $result = $this->database
                        ->execute("SELECT * FROM $this->table WHERE $item='$value'" . $order)
                        ->result();

         if(false !== ($error = $this->database->error())) {
            trigger_error($error);
            return null;
         }

         if(!empty($result)) {
            foreach ($result as $array) {
               $objects[] = $this->factory($array);
            }

            return $objects;
         }

         return null;
      }

      public function drop() {
        $this->database->execute("DROP TABLE $this->table");
      }

      public function truncate() {
        $this->database->execute("TRUNCATE $this->table");
      }

      /**
       * Crea el objeto del proveedor en $this->database
       * Determina el nombre de la base de datos y la conecta a través del proveedor
       * Determina el nombre de la base de datos y la setea en $this->database
       * Crea la tabla si no existe
       *
       * @return void
       */
      abstract protected function map();
   }