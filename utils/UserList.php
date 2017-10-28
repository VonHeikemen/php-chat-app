<?php 

namespace Utils;

use function Utils\Helpers\arrayFind;
use function Utils\Helpers\isRealString;

/**
* Users utils
*/
class UserList
{
    public $users;

    function __construct()
    {
        $this->users = [];
    }

    /**
     * @param array $userData input submitted by the user
     */

    public function isValidUser($userData)
    {
        return ( 
            ! empty($userData)
            && isRealString($userData['name'])
            && isRealString($userData['room'])
        );
    }

    public function addUser($id, $name, $room)
    {
        $newUser = (object) [
            'id' => $id,
            'name' => $name,
            'room' => $room
        ];

        array_push($this->users, $newUser);

        return $newUser;
    }

    public function getUser($userId)
    {
        $test_func = function($user) use($userId){
            return $user->id === $userId;
        };

        return arrayFind($this->users, $test_func);
    }

    public function getRoomUsers($room)
    {
        $filter_func = function($user) use($room){
            return $user->room === $room;
        };

        $filteredUsers = array_filter($this->users, $filter_func);

        if( empty($filteredUsers) )
            return NULL;

        $map_func = function($user){
            return $user->name;
        };

        $usernames = array_map($map_func, $filteredUsers);

        return array_values($usernames);
    }

    public function removeUser($userId)
    {
        $filter_func = function($user) use($userId){
            return $user->id !== $userId;
        };

        $newList = array_filter($this->users, $filter_func);

        $this->users = array_values($newList);
    }
}
