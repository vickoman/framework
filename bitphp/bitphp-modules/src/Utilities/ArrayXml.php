<?php 

   namespace BitPHP\Modules\Utilities;

   use \DOMDocument;
   use \SimpleXMLElement;

   class ArrayXml {

      private static function parse($data, &$xml) {
            foreach($data as $key => $value) {
              if(is_array( $value )) {
                  if(!is_numeric( $key )){
                      $subnode = $xml->addChild( $key );
                      self::parse( $value, $subnode );
                  } else {
                      $subnode = $xml->addChild( "item" );
                      //$subnode->addAttribute( 'id',$key );
                      self::parse( $value, $subnode );
                  }
              } else {
                  $xml->addChild( $key,$value );
              }
            }
       }

       public static function encode( $name, $data ) {
          $xml = new SimpleXMLElement("<$name></$name>");
           self::parse( $data, $xml );
         return $xml->asXML();
       }

        public static function decode( $xml ) {
            $obj = simplexml_load_string($xml);
            $json = json_encode($obj);
            $array = json_decode($json, true);
            return $array;
        }
   }