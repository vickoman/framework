<?php

   namespace Bitphp\Modules\Traits;

   use \Closure;

   trait Mutator {

      protected $mutators;
      protected $binded;

      public function __call($method, array $args) {
         if(isset($this->binded[$method])) {
            return call_user_func_array($this->binded[$method], $args);
         }

         if(preg_match('/^set(\w+)$/', $method, $matches)) {
            return $this->setter(lcfirst($matches[1]), $args[0]);
         }

         if(preg_match('/^get(\w+)$/', $method, $matches)) {
            return $this->getter($matches[1]);
         }

         throw new Exception('La clase ' . __CLASS__ . " no contiene el metodo $method", 1);
      }

      public function __get($var) {
         return $this->getter(ucfirst($var));
      }

      protected function getter($var) {
         $forger = "forge$var";
         if(method_exists($this, $forger))
            return call_user_func(array($this, $forger));

         trigger_error('Propiedad indefinida ' . __CLASS__ . "::\$$var");
      }

      protected function setter($var, $val) {
         if(is_callable($val)) {
            $this->binded[$var] = Closure::bind($val, $this, get_class());
            return;
         }

         $this->$var = $val;
      }
   }