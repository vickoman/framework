<?php 

   namespace Bitphp\Modules\Database;

   use \PDO;
   use \Bitphp\Modules\Database\Abstracts\Provider;

   /**
    *   Proporciona una capa de abstracción para una conexión 
    *   a basesde datos mysql a través de PDO
    *
    *   @author Eduardo B Romero
    */   
   class MySql extends Provider {
      
      /** Declaracion de la consulta realizada */
      private $statement;
      /** Objeto PDO */
      public $pdo;

      public function __construct() {
         parent::__construct();

         $params = 'mysql:host='.$this->host.';charset=utf8';
         $this->pdo = new PDO($params, $this->user, $this->pass);
      }

      /**
       * Realiza la conexión a la base de datos indicada a través de PDO
       *
       * @param string $name Nombre o alias de la base de datos
       * @return void
       */
      public function database($name) {
         # obtiene el nombre real, por si es un alias
         $name = $this->realName($name);
         $this->pdo->query("USE $name");
      }

      /**
       *  Ejecuta la consulta indicada a traves del POD
       *
       *  @param string $query Consulta que se va a ejecutar
       *  @return MySql Retorna un objeto de si mismo
       */
      public function execute($query) {
         $this->statement = $this->pdo->query($query);
         return $this;
      }

      /**
       *  Retorna el mensaje error (si se produjo)
       *
       *  @return mixed Mensaje de error, false si no lo hubo
       */
      public function error() {
         $error = $this->pdo->errorInfo()[2];

         if($error === null)
            return false;

         return $error;
      }

      /**
       *  Retorna un array asociativo de la consulta
       *  
       *  @return array Resultado de la consulta 
       */
      public function result() {
         if(false !== ($error = $this->error())) {
            trigger_error($error);
            return false;
         }
         
         return $this->statement->fetchAll(PDO::FETCH_ASSOC);
      }
   }