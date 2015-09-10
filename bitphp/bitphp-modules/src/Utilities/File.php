<?php

   namespace Bitphp\Modules\Utilities;

   class File {

      public static function createDirIfDontExists($file) {
         $dir = dirname($file);
        
         if(!is_dir($dir))
            mkdir($dir, 0777, true);
      }

      public static function explore($dir, $recursive = true) {
         if(!is_dir($dir)) {
            if(is_file($dir)) {
               return [$dir];
            }

            return [];
         }

         $dir = realpath($dir);
         $list = array($dir);
         $dir_obj = dir($dir);
         while (false !== ($item = $dir_obj->read())) {
            if($item == '.' || $item == '..')
               continue;

            $complete_path = "$dir/$item";
            if(is_dir($complete_path)) {
               if($recursive) {
                  $list = array_merge($list, self::explore($complete_path));
               } else {
                  $list[] = $complete_path;
               }

               continue;
            }

            $list[] = $complete_path;
         }

         $dir_obj->close();
         return $list;
      }

      public static function write( $file, $content ) {
         self::createDirIfDontExists($file);
         return @file_put_contents($file, $content);
      }

      public static function append($file, $content) {
         self::createDirIfDontExists($file);
         return @file_put_contents($file, $content, FILE_APPEND);
      }

      public static function prepend($file, $content) {
         $_content = '';
         if(file_exists($file))
            $_content = file_get_contents($file);

         self::write($file, $content . $_content);
      }

      public static function read($file) {
         if(!file_exists($file))
            return false;

         return file_get_contents($file);
      }
   }