<?php

/**
 * Bitphp Framework
 */

require 'bitphp/autoload.php';

use \Bitphp\Base\MicroServer;
use \Bitphp\Modules\Layout\Medusa;
use \Bitphp\Core\Log;

$server = new MicroServer();

$server->doGet('/', function() {
   Medusa::quick('welcome');
});

$server->run();