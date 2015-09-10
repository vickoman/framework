<?php

   namespace Bitphp\Base\SocketServer;

   class RemoteClient {

      private $server;
      public $socket;
      public $ip;

      public function __construct(&$socket, &$server) {
         $this->server = $server;
         $this->socket = $socket;
         socket_getpeername($socket, $this->ip);
      }

      public function set($var, $val) {
         $this->$var = $val;
      }

      public function close() {
         socket_close($this->socket);
      }

      public function read() {
         return @socket_read($this->socket, 1024);
      }

      public function send($data) {
         socket_write($this->socket, $data, strlen($data));
      }
   }