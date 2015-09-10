<?php

   namespace Bitphp\Core;

   use \Bitphp\Core\Config;
   use \Bitphp\Core\Globals;
   use \Bitphp\Modules\Utilities\File;
   use \Bitphp\Modules\Cli\StandardIO;
   use \Bitphp\Modules\Utilities\Random;

   /**
    * Clase para el manejo y manipulacion de la papelera de bitphp
    *
    * @author Eduardo B Romero
    */
   class Trash {

      /**
       * Recibe como parametro un lista de rutas de archivos y/o carpetas
       * de esta lista identifica cuales se han indicado para omitir desde
       * el archivo de configuracion y los remueve de la lista
       *
       * @param array $files Lista de archivos para limpiar
       * @return array La lista de archivos sin los archivos omitidos
       */
      private static function indentifySkipeds($files) {
         $skipeds = Config::param('trash.ignore');
         if($skipeds === null)
            return $files;
       
         foreach ($skipeds as $skiped) {
            $pattern = str_replace('/', '\\/', $skiped);
            $result = preg_grep('/^' . $pattern . '$/', $files);
            foreach ($result as $index => $name) {
               unset($files[$index]);
            }
         }

         return $files;
      }

      /**
       * Crea un arreglo asociativo de los respaldos en la papelera
       * de la forma:
       *
       * [
       *    [<backup_name>] => [
       *       'meta' => <metadatos del respaldo>,
       *       'path' => /full/path/to/backup/folder/
       *    ]
       * ]
       *
       * @return array
       */
      public static function getBackupsList() {
         $trash_dir = Globals::get('base_path') . '/olimpus/bitphp-trash/';
         $list = File::explore($trash_dir, false);
         array_shift($list);
         
         $backups = array();
         foreach ($list as $backup) {
            $name = basename($backup);
            $meta = file_get_contents($backup . '/meta.json');

            $backups[$name] = [
                 'meta' => json_decode($meta, true)
               , 'path' => $backup
            ];
         }

         return $backups;
      }

      /**
       * Restaura los archivos del backup indicado (si existe)
       * y los sobreescribe si ya existen (y si se indica en el segundo parametro)
       *
       * @param string $name Nombre del respaldo
       * @param bool Forzar sobre-escritura de los archivos
       * @return mixed
       */
      public static function restore($name, $force_restore=null) {
         $result = array();
         $files = array();
         $backups = self::getBackupsList();
         foreach ($backups as $backup => $data) {
            if($backup == $name) {
               $preserve_backup = false;
               $result['count'] = $data['meta']['count'];
               $result['files'] = [
                    'restored' => array()
                  , 'to_overwrite' => array()
                  , 'missing' => array()
               ];

               foreach ($data['meta']['files'] as $file => $original_path) {
                  $file_trash_path = $data['path'] . "/$file";
                  
                  if(!file_exists($file_trash_path)) {
                     $result['files']['missing'][] = $original_path;
                     continue;
                  }

                  if(file_exists($original_path) && !$force_restore) {
                     $preserve_backup = true;
                     $result['files']['to_overwrite'][] = $original_path;
                     continue;
                  }

                  $result['files']['restored'][] = $original_path;
                  File::write($original_path, file_get_contents($file_trash_path));
                  unlink($file_trash_path);
               }

               if($preserve_backup == false) {
                  unlink($data['path'] . '/meta.json');
                  rmdir($data['path']);
               }

               return $result;
            }
         }

         return false;
      }

      /**
       * Mueve los archivos indicados (ruta absoluta) a una carpeta
       * de respaldo en la basura de bitphp, si el backup ya existe
       * agrega algunos numeros aleatorios al nombre
       *
       * @param array $files rutas absolutas de los ficheros a mover
       * @param string $nombre del respaldo
       * @return string Ruta del backup
       */
      public static function remove($files, $backup_name) {
         $meta = array();
         $meta['files'] = array();

         $backup = Globals::get('base_path') . '/olimpus/bitphp-trash/' . $backup_name;

         if(is_dir($backup))
            $backup = $backup . '_' . Random::number(3);

         foreach ($files as $file) {
            $hash = md5($file);
            $meta['files'][$hash] = $file;
            File::write("$backup/$hash", file_get_contents($file));
            unlink($file);
         }

         $meta['date']  = $date = date(DATE_ISO8601);
         $meta['count'] = count($files);

         File::write("$backup/meta.json", json_encode($meta, JSON_PRETTY_PRINT));
         return $backup;         
      }

      /**
       * Crea un arrglo con las rutas de archivos a remover
       * escanea por defecto /app, /public e index.php
       * manda llamar la funcion identifySkipeds para descartar
       * los archivos omitidos desde la configuracion y tambien
       * agrega al escaneo los directorios y/o archivos
       * indicados en la configuracion
       *
       * @return array
       */
      public static function scan() {
         $files = array();
         $base = Globals::get('base_path');

         Config::load('app');

         $dirs  = [
              $base . '/app'
            , $base . '/public'
            , $base . '/index.php'
         ];

         $configured_dirs = Config::param('trash.scan');
         if(
                 $configured_dirs !== null
              && is_array($configured_dirs)
           ) 
         {
            $dirs = array_merge($dirs, $configured_dirs);
         }

         foreach ($dirs as $dir) {
            $files = array_merge($files, File::explore($dir));
         }

         $files = self::indentifySkipeds($files);
      
         foreach ($files as $file) {
            # File::explore() devuelve en la lista tambien los 
            # directorios escaneados, solo se requieren los archivos
            if(is_dir($file)) {
               $index = array_search($file, $files);
               unset($files[$index]);
               continue;
            }
         }

         return $files;
      }
   }