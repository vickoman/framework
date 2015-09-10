<?php

  namespace Bitphp\Modules\Http;

  use \Bitphp\Core\Globals;
  use \Bitphp\Modules\Layout\Medusa;

  class Response {

    protected static $statusCode;

    protected static function getStatusCode() {
      return empty(self::$statusCode) ? 200 : self::$statusCode;
    }

    public static function status($code) {
      $status = [
        200 => 'OK',  
        201 => 'Created',  
        202 => 'Accepted',  
        204 => 'No Content',  
        301 => 'Moved Permanently',  
        302 => 'Found',  
        303 => 'See Other',  
        304 => 'Not Modified',
        400 => 'Bad Request',  
        401 => 'Unauthorized',  
        403 => 'Forbidden',  
        404 => 'Not Found',  
        405 => 'Method Not Allowed',  
        500 => 'Internal Server Error'
      ];

      if ( !isset( $status[ $code ] ) ) {
        trigger_error("Codigo de estado '$code' invalido");
        return;
      }

      $statusMessage = $status[$code];
      header( "HTTP/1.1 $code $statusMessage" );
    }

    public static function xml( $data ) {
      header( 'Content-Type: application/xml;charset=utf-8' );
      echo $data;
    }

    public static function json( $data ) {
      if(is_array($data))
        $data = json_encode($data);

      header( 'Content-Type: application/json;charset=utf-8' );
      echo $data;
    }

    public static function redir( $url, $delay = 0 ) {
      if(!preg_match('/^(\w+)(\:\/\/)(.*)$/', $url))
        $url = Globals::get('base_url') . $url;

      if($delay > 0) {
        $medusa = new Medusa();
        $medusa->views_path = Globals::get('base_path') . '/olimpus/system/pages';
        $medusa->load('Redirection')
               ->with([
                    'url' => $url
                  , 'delay' => $delay
                ])
               ->draw();
        return;
      }

      header("Location: $url");
    }

    public static function notFound() {
      self::status(404);
      $medusa = new Medusa();
      $medusa->views_path = Globals::get('base_path') . '/olimpus/system/pages';
      $medusa->load('NotFound')
              ->draw();
    }
  }