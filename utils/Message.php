<?php
namespace Utils;

/**
* Message utils
*/
class Message
{
    static function create($from, $text, $opts=['plain' => TRUE])
    {
        return [
            'from' => $from,
            'text' => $text,
            'opts' => $opts,
            'createdAt' => (new \DateTime)->getTimestamp()
        ];
    }

    static function shareUserLocation($from, $latitude, $longitude)
    {
        $text = 'My current location';
        $opts = [
            'anchor' => TRUE,
            'href' => "https://www.google.com/maps?q=$latitude,$longitude"
        ];

        return static::create($from, $text, $opts);
    }

    static function fromAdmin($username, $messageType)
    {
        $admin = 'Admin';
        $messages = [
            'greeting' => 'Welcome, %s',
            'joined' => '%s has joined',
            'leave' => '%s has left'
        ];

        $text = sprintf($messages[$messageType], $username);

        return static::create($admin, $text);

    }
}

