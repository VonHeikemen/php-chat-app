<?php

use PHPUnit\Framework\TestCase;
use Utils\UserList;

/**
 * @covers \Utils\Helpers
 */
class UserListTest extends TestCase {

    private $userList;

    public function setUp()
    {
        $this->userList = new UserList;

        $this->userList->users = [
            (object) [
                'id' => '1',
                'name' => 'User 1',
                'room' => 'Some Room'
            ], (object)[
                'id' => '2',
                'name' => 'User 2',
                'room' => 'SomeOtherRoom'
            ], (object) [
                'id' => '3',
                'name' => 'User 3',
                'room' => 'Some Room'
            ]
        ];
    }

    public function tearDown()
    {
       $this->userList = NULL;
    }

    public function testAddUsers()
    {
        $user = (object) [
            'id' => '4',
            'name' => 'User 4',
            'room' => 'A room for now'
        ];

        $newUser = $this->userList->addUser(
            $user->id, 
            $user->name, 
            $user->room
        );

        $this->assertEquals($user, $newUser);
        $this->assertCount(4, $this->userList->users);
    }

    public function testGetUser()
    {
        $user = $this->userList->getUser('123');

        $this->assertNull($user);

        $user = $this->userList->getUser('2');
        $expected = $this->userList->users[1];

        $this->assertEquals($expected, $user);
    }

    public function testGetRoomUsers()
    {
        $room = 'Some Room';

        $userNameList = $this->userList->getRoomUsers($room);
        $expected = [
            $this->userList->users[0]->name,
            $this->userList->users[2]->name
        ];

        $this->assertEquals($expected, $userNameList);
    }

    public function testRemoveUser()
    {
        $userId = '3';

        $this->userList->removeUser($userId);

        $this->assertCount(2, $this->userList->users);

        $user = $this->userList->getUser($userId);

        $this->assertNull($user);
    }
}
