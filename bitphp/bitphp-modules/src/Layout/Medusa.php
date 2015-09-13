<?php 

   namespace Bitphp\Modules\Layout;

   use \Bitphp\Core\Globals;
   use \Bitphp\Core\Config;

   define('MT_MEDUSA_COMMENT', '/\/\*(.*)?\*\//Usx');
   define('MT_HTML_COMMENT', '/\/\-(.*)?\-\//Usx');
   define('MT_SHORT_ECHO', '/\{\{(.*)\}\}/U');
   define('MT_SHORT_CSS_INCLUDE', '/:css(\s+)+(.+)/');
   define('MT_SHORT_JS_INCLUDE', '/:js(\s+)+(.+)/');
   define('MT_STATEMENTS_START', '/:(if|elseif|for|foreach)(\s+)+(.*)/');
   define('MT_STATEMENTS_END', '/:(endif|endforeach|endfor)/');
   define('MT_ELSE', '/:else/');
   define('MT_INCLUDE', '/:(require|include)(\s+)?(\S+)/');
   define('MT_SHORT_ARRAY', '/\$(\w+)\.(\w+)/');
   define('MT_ARGS', '/:args/');
   define('MT_BASE_LINK', '/:base/');
   define('MT_VAR', '/:var(\s+)+(\w+)(\s+)+(.+)/');
   define('MT_EXTENDS', '/:extends(\s+)(\S+)(\s+)/Usx');
   define('MT_PARENT', '/:parent/Usx');
   define('MT_BLOCK_START', ':block(\s+)');
   define('MT_BLOCK_END', ':endblock');
   define('MT_BLOCK', '/' . MT_BLOCK_START . '(\S+)(\s+)(.*)' . MT_BLOCK_END . '/Usx');

   class Medusa extends View {

      public $blocks;

      public function __construct() {
         parent::__construct();
         $this->mime = '.medusa.php';
      }

      private function readBlocks($source) {
         $tokens = null;
         preg_match_all(MT_BLOCK, $source, $tokens);

         $blocks = count($tokens[0]);
         for($i = 0; $i < $blocks; $i++) {

            $name = $tokens[2][$i];
            $content = $tokens[4][$i];

            if(isset($this->blocks[$name])) {
               if(1 === preg_match(MT_PARENT, $content)) {
                  $this->blocks[$name] .= $content;
                  continue;
               }
            }

            $this->blocks[$name] = $content;
         };
      }

      private function checkInheritance($source) {
         preg_match_all(MT_EXTENDS, $source, $extends);
         if(empty($extends[2]))
            return $source;

         $name = $extends[2][0];

         $file = $this->getViewPath($name);

         if(!file_exists($file)) {
            $message  = "No se pudo heredar la plantilla $name. ";
            $message .= "No exite $file";
            trigger_error($message);
            return null;
         }

         $parent = file_get_contents($file);
         $this->readBlocks($parent);
         $this->readBlocks($source);

         foreach ($this->blocks as $name => $value) {
            $pattern = '/' . MT_BLOCK_START . $name . '(\s+)(.*)' . MT_BLOCK_END . '/Usx';
            $parent = preg_replace($pattern, $value, $parent);
         }

         return $parent;
      }

      public function compile($source) {
         $source = $this->checkInheritance($source);

         $rules = [
              MT_MEDUSA_COMMENT
            , MT_HTML_COMMENT
            , MT_SHORT_ECHO
            , MT_SHORT_CSS_INCLUDE
            , MT_SHORT_JS_INCLUDE
            , MT_STATEMENTS_START
            , MT_STATEMENTS_END
            , MT_ELSE
            , MT_INCLUDE
            , MT_SHORT_ARRAY
            , MT_ARGS
            , MT_BASE_LINK
            , MT_VAR
            , MT_PARENT
            
         ];

         $replaces = [
              ''
            , '<!--$1-->'
            , '<?php echo $1 ?>'
            , '<link rel="stylesheet" href="<?php echo $_ROUTE[\'base_url\'] ?>/public/css/$2.css">'
            , '<script src="<?php echo $_ROUTE[\'base_url\'] ?>/public/js/$2.js"></script>'
            , '<?php $1 ($3): ?>'
            , '<?php $1; ?>'
            , '<?php else: ?>'
            , '<?php $this->required($3); ?>'
            , '$$1["$2"]'
            , '$this->variables'
            , '$_ROUTE[\'base_url\']'
            , '<?php $$2 = $4 ?>'
            , ''
         ];

         return preg_replace($rules, $replaces, $source);
      }

      public function compress($data) {
         $pattern = '#(?ix)(?>[^\S ]\s*|\s{2,})(?=(?:(?:[^<]++|<(?!/?(?:textarea|pre)\b))*+)(?:<(?>textarea|pre)\b|\z))#';
         return preg_replace($pattern, '', $data);
      }

      protected function render() {
         #parent::render();

         foreach ($this->loaded as $file) {
            $source = file_get_contents($file);
            $this->source .= $this->compile($source);
         }

         $compress = Config::param('medusa.compress');
         if(true === $compress)
            $this->source = $this->compress($this->source);
      }

      public static function quick($name, $vars = array()) {
         $loader = new Medusa();
         $loader->load($name)->with($vars)->draw();
         $loader = null;
      }
   }