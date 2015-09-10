<?php
   
   namespace Bitphp\Modules\Database;

   use \Bitphp\Core\Config;
   use \Bitphp\Core\Globals;
   use \Bitphp\Modules\Utilities\File;

   class Migration {

      protected function upOrDown($subject, $action) {
         Config::load('migrations', true, false);

         $result = array(
              'tables' => array()
            , 'queries' => array()
         );

         list($database, $table) = explode('/', $subject);
         
         $database = ucwords($database, '_');
         $table = ucwords($table, '_');

         $database = ($database == 'All') ? '' : $database . '_Migration';
         $table = ($table == 'All') ? '' : $table . '_Seed.php';

         $migrations_path = Globals::get('base_path') . "/app/migrations";
         $seeds = File::explore("$migrations_path/$database/$table");

         foreach ($seeds as $seed) {
            if(is_file($seed)) {
               //getting the class with namespace from path
               $class = str_replace($migrations_path, '', $seed);
               $class = dirname($class) . '\\' . basename($class, '.php');
               $class = '\\App\\Migrations' . str_replace('/', '\\', $class);

               $seed = new $class();

               if($action == 'up') {
                  $seed->setup();
                  $seed->up();
                  $result['queries'] = $seed->executed_queries;
               } else {
                  $drop_db = ($table == '') ? true : false;
                  $seed->down($drop_db);
                  $result['queries'] = $seed->executed_queries;
               }

               $result['tables'][] = $class;
            }
         }

         return $result;
      }

      public static function up($subject) {
         return self::upOrDown($subject, 'up');
      }

      public static function down($subject) {
         return self::upOrDown($subject, 'down');
      }
   }