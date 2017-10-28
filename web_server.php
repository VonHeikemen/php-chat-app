<?php  

define('BASEPATH', __DIR__);

require 'vendor/autoload.php';

use flight\Engine;

$app = new Engine();

$app->set('flight.views.path', BASEPATH.'/views');
$app->set('socket.host', 'http://localhost:2020');

$app->route('/', function() use($app){
    $app->render('chat', [
        'socket_host' => $app->get('socket.host')
    ]);
});

$app->start();

?>