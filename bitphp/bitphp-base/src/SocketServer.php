<?php

   namespace Bitphp\Base;

   use \Closure;
   use \Exception;
   use \Bitphp\Core\Globals;
   use \Bitphp\Base\Abstracts\CliService;
   use \Bitphp\Base\SocketServer\RemoteClient;
   use \Bitphp\Base\SocketServer\WsHandshake;

   class SocketServer extends CliService {

      protected $host;
      protected $port;
      protected $events;
      protected $clients;
      protected $main_socket;
      public $web_socket = false;

      public function __construct() {
         parent::__construct();

         set_time_limit(0);
         $this->events  = array();
         $this->clients = array(
              'sockets' => array()
            , 'instances' => array()
         );
      }

      protected function triggerEvent($event, array $parameters) {
         if(!isset($this->events[$event]))
            return;

         call_user_func_array($this->events[$event], $parameters);
      }

      protected function acceptConnections() {
         $socket = socket_accept($this->main_socket);

         $client = new RemoteClient($socket, $this);

         if($this->web_socket) {
            $header = $client->read();
            $payload = WsHandshake::perform($header, $this->host, $this->port);
            $client->send($payload);
         }

         $this->clients['instances'][] =& $client;
         $this->clients['sockets'][]   =& $client->socket;

         $this->triggerEvent('connection', array($client));
      }

      protected function serveConnections(&$sockets) {
         foreach ($sockets as $socket) {
            $client_id = array_search($socket, $this->clients['sockets']);
            $client = $this->clients['instances'][$client_id];

            while(@socket_recv($socket, $buffer, 1024, 0) >= 1)
            {
               $buffer = $this->unmaskMessage($buffer);
               $this->triggerEvent('message', array($client, $buffer));
               break 2;
            }

            if(false === $client->read()) {
               $this->triggerEvent('disconnection', array($client));
               unset($this->clients['instances'][$client_id]);
               unset($this->clients['sockets'][$client_id]);
            }
         }
      }

      public function unmaskMessage($data) {
         if(!$this->web_socket)
            return $data;

         return WsHandshake::unmask($data);
      }

      public function maskMessage($data) {
         if(!$this->web_socket)
            return $data;

         return WsHandshake::mask($data);  
      }

      public function doEvent($event, $callback) {
         if(!is_callable($callback))
            return;

         $this->events[$event] = Closure::bind($callback, $this, get_class());
      }

      public function disconnectClient(RemoteClient $client) {
         $this->triggerEvent('disconnection', array($client));
         $client->close();

         $client_id = array_search($client->socket, $this->clients['sockets']);
         unset($this->clients['instances'][$client_id]);
         unset($this->clients['sockets'][$client_id]);
      }

      public function sendToAll($data) {
         $data = $this->maskMessage($data);
         foreach ($this->clients['instances'] as $client) {
            $client->send($data);
         }
      }

      public function sendFrom(RemoteClient $client, $data) {
         $data = $this->maskMessage($data);
         $clients = $this->clients['instances'];
         $sender = array_search($client, $clients);

         unset($clients[$sender]);

         foreach ($clients as $client) {
            $client->send($data);
         }   
      }

      public function bind($address) {
         list($this->host, $this->port) = explode(':', $address);

         $this->main_socket = @socket_create(AF_INET, SOCK_STREAM, 0);
         
         if(false === ($this->main_socket))
            throw new Exception('Can not create the socket');

         if(false === (@socket_bind($this->main_socket, $this->host, $this->port)))
            throw new Exception(socket_strerror(socket_last_error($this->main_socket)));
            

         socket_getsockname($this->main_socket, $this->host, $this->port);

         if(false == (@socket_set_option($this->main_socket, SOL_SOCKET, SO_REUSEADDR, 1)))
            throw new Exception('Can not create the socket');

         socket_listen($this->main_socket);

         $this->triggerEvent('binded', array($this->host, $this->port));
      }

      public function run() {
         do {

            $sockets = $this->clients['sockets'];
            //to scan main_socket too
            $sockets[] = $this->main_socket;

            @socket_select($sockets, $write=NULL, $except=NULL, $tv_sec=5);

            if(in_array($this->main_socket, $sockets)) {
               $main_socket_id = array_search($this->main_socket, $sockets);
               unset($sockets[$main_socket_id]);
               
               $this->acceptConnections();
            }

            if(!empty($sockets))
               $this->serveConnections($sockets);

         } while (true);

         socket_close($this->main_socket);
      }
   }