<?php
   
   require 'bitphp/autoload.php';

   use \Bitphp\Base\SocketServer;

   $server = new SocketServer();

   $server->web_socket = true;

   $server->doEvent('binded', function($host, $port) {
      echo " ~ Enlazado a $host en el puerto $port". PHP_EOL;
   });

   $server->doEvent('connection', function ($client) use ($server) {
      echo " ~ Nueva conexion ($client->ip)", PHP_EOL;

      $welcome_message = $server->maskMessage(PHP_EOL . ' ~ Bienvenido a la sala de chat' . PHP_EOL);
      $client->send($welcome_message);

      $server->sendToAll(" ~ $client->ip se ha unido" . PHP_EOL);
   });

   $server->doEvent('disconnection', function ($client) use ($server) {
      echo " ~ Conexion perdida ($client->ip)", PHP_EOL;
      $server->sendToAll(" ~ $client->ip se ha ido" . PHP_EOL);
   });

   $server->doEvent('message', function ($client, $data) use ($server) {
      $server->sendFrom($client, $data);
   });

   $server->bind('localhost:777');
   $server->run();