<?php 
   
   namespace Bitphp\Modules\Layout;

   use \Bitphp\Core\Globals;
   use \Bitphp\Core\Config;
   use \Bitphp\Core\Cache;

   /**
    *   Modulo para el manejo de vistas
    *
    *   @author Eduardo B Romero
    */
   class View {

      /** Rutas de las vistas qué se van cargando */
      protected $loaded;
      /** Variables qué se van a pasar a las vistas */
      protected $variables;
      /** Extencion del archivo de las vistas */
      protected $mime;
      /** Resultado de la vista ya generada */
      protected $output_buffer;
      /** Contenido de la vista antes de generarse */
      protected $source;
      /** Para usar un directorio alternativo para las vistas */
      public $views_path;

      /**
       *  Durante el contructor se inicializan las propiedades
       *  y se define el agente para el cache
       */
      public function __construct() {
         $this->clean();
         $this->output_buffer = 'empty';
         $this->mime = '.php';
         $this->force_root = false;

         //set cache angent
         Cache::$agent = 'views';
      }

      /**
       *   Resetea las variables de la clase
       *
       *   @return void
       */
      protected function clean() {
         $this->source = '';
         $this->loaded = array();
         $this->variables = array();
      }

      /**
       *  Lee el contenido de las vistas cargadas
       *  para irlo almacenando en $this->source
       *
       *  @return void
       */
      protected function render() {
         foreach ($this->loaded as $file) {
            $this->source .= file_get_contents($file);
         }
      }

      /**
       *  Genera el resultado final de la vista y lo almacena
       *  en $this->output_buffer, si no se a cargado ninguna 
       *  vista manda error, también verifica si la vista se 
       *  encuentra en cache, de ser utiliza el cache
       *
       *  @return Object Retorna la instancia de la clase
       */
      protected function make() {
         if(empty($this->loaded)) {
            $message  = 'No se pudo mostrar la(s) vista(s) ';
            $message .= 'ya que no se han cargado ninguna';
            trigger_error($message);
            return;
         }

         $this->output_buffer = Cache::read([$this->loaded, $this->variables]);

         if(false !== $this->output_buffer)
            return;

         $this->render();
         $_ROUTE = Globals::all();

         ob_start();
         extract($this->variables);
         eval("?> $this->source <?php ");
         $this->output_buffer = ob_get_clean();

         Cache::save([$this->loaded, $this->variables], $this->output_buffer);
         $this->clean();
         return $this;
      }

      /**
       *  Crea la ruta absoluta al archivo de la vista
       *
       *  @param string $name Nombre de la vista
       *  @return string Ruta completa al archivo de la vista
       */
      protected function getViewPath($name) {
        if(!$this->views_path)
          $this->views_path = Globals::get('app_path') . "/views";

        $path = $this->views_path . "/$name" . $this->mime;
        return $path;
      }

      /**
       *  Metodo para cargar desde una vista. 
       *  Envia como parametros a la vista los qué recibioreon
       *  la vista qué lo usa.
       *
       *  @param $name Nombre de la vista
       *  @return void
       */
      public function required($name) {
         $loader = new View();
         $loader->views_path = $this->views_path;
         $loader->load($name)->with($this->variables)->draw();
         $loader = null;
      }

      /**
       *   Verifica qué el fichero de la vista exista y
       *   lo agrega a la lista de ficheros, si no existe 
       *   manda error
       *
       *  @param mixed $name Nombre(s) de la(s) vista(s)
       *  @return Object Retorna la instancia de la clase
       */
      public function load($name) {

          if(is_array($name)) {
            foreach ($name as $view) {
              $this->load($view);
            }

            return $this;
          }

         $file = $this->getViewPath($name);

         if(false === file_exists($file)) {
            trigger_error("No se pudo cargar la vista '$name'. El archivo '$file' no existe");
            return $this;
         }

         $this->loaded[] = $file;
         return $this;
      }

      /**
       *   Setea las variables qué se le pasaran a la vista
       *
       *   @param array $vars Array asociativo de la variables
       *   @return Object Retorna la instancia de la clase
       */
      public function with($vars) {
         $this->variables = $vars;
         return $this;
      }

      /**
       *  Ejecuta make() y muestra directamente el resultado
       *  de la vista
       *
       *  @return void
       */
      public function draw() {
        $this->make();
        echo $this->output_buffer;
      }

      /**
       *  Ejecuta make() y retorna el resultado de la vista
       *
       *  @return string resultado de la vista
       */
      public function read() {
        $this->make();
        return $this->output_buffer;
      }

      /**
       *  Metodo estatico para cargar, setear variables y mostar
       *  la vista en un solo paso
       *
       *  @param $name Nombre de la vista
       *  @param array $vars Arrglo asociativo de parametros para la vista
       *  @return void
       */
      public static function quick($name, $vars = array()) {
         $loader = new View();
         $loader->load($name)->with($vars)->draw();
         $loader = null;
      }
   }