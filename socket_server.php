<?php  
require 'vendor/autoload.php';

use Workerman\Worker;
use PHPSocketIO\SocketIO;
use Utils\Message;
use Utils\UserList;

use function Utils\Helpers\isRealString;

$io = new SocketIO(2020);
$io->userList = new UserList;

$io->on('connection', function($socket) use($io){
    echo "New user connected\n";

    //Emit event to let the user know connection was succesfull.
    //Which is completely unnecessary
    $socket->emit('connect');
    
    $socket->on('join', function($data, $callback) use($socket, $io){
        
        if( !$io->userList->isValidUser($data) )
        {
            $callback([
                'error' => 'No room name or user name provided'
            ]);
            return;
        }

        $user = $io->userList->addUser($socket->id, $data['name'], $data['room']);

        //Join a room
        $socket->join($user->room);

        //Send message to all users in the room except sender
        $socket->to($user->room)->broadcast
            ->emit('newMessage', Message::fromAdmin($user->name, 'joined'));

        //Send message to user
        $socket->emit('welcome', Message::fromAdmin($user->name, 'greeting'));

        //Send message to all users in the room
        $io->in($user->room)
            ->emit('updateUserList', $io->userList->getRoomUsers($user->room));

        $callback([
            'success' => 'Welcome to the chat room'
        ]);
    });

    $socket->on('createMessage', function($data, $callback) use($socket, $io) {
        $user = $io->userList->getUser($socket->id);

        if($user === NULL)
        {
            $callback([
                'error' => 'You must log in', 
                'noUser' => TRUE
            ]);
            return;
        }

        if( !isRealString($data['text']) )
        {
            $callback([
                'error' => 'Please send a valid message'
            ]);
            return;
        }

        $io->in($user->room)
            ->emit('newMessage', Message::create($user->name, $data['text']));

        $callback([
            'success' => 'Message send succesfully'
        ]);
    });

    $socket->on('shareLocation', function($data, $callback) use($socket, $io) {
        $user = $io->userList->getUser($socket->id);

        if($user === NULL)
        {
            $callback([
                'error' => 'You must log in', 
                'noUser' => TRUE
            ]);
            return;
        }

        $io->in($user->room)
            ->emit(
                'newLocationUrl', 
                Message::shareUserLocation(
                    $user->name, 
                    $data['latitude'], 
                    $data['longitude']
                )
            )
        ;
    });

    $socket->on('disconnect', function() use($socket, $io){
        echo "User disconnected\n";

        $user = $io->userList->getUser($socket->id);

        if($user === NULL)
            return;

        $socket->leave($user->room);
        $io->userList->removeUser($user->id);

        $io->in($user->room)
            ->emit('newMessage', Message::fromAdmin($user->name, 'leave'));

        $io->in($user->room)
            ->emit(
                'updateUserList', 
                $io->userList->getRoomUsers($user->room)
            )
        ;

        
    });

});

Worker::runAll();

?>