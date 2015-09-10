<?php

   namespace Bitphp\Modules\Events;

   class Event {

      private $listeners = array();
      private $stop = false;

      protected function addListener($namespace) {
         if(is_array($namespace)) {
            foreach ($namespace as $listener) {
               $this->addListener($listener);
            }

            return;
         }

         $this->listeners[] = $namespace;
      }

      public function stopPropagation() {
         $this->stop = true;
      }

      public function fire() {
         foreach ($this->listeners as $listener) {
            $listener = new $listener;
            $listener->handle($this);

            if($this->stop)
               break;
         }
      }
   }