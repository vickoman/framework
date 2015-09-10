<?php

   namespace Bitphp\Modules\Events\Interfaces;

   interface Listener {

      public function handle(&$event);
   }