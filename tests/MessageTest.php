<?php

use PHPUnit\Framework\TestCase;
use Utils\Message;

/**
 * @covers \Utils\Message
 */
class MessageTest extends TestCase {

    public function testCreateMessage() {
        $input = [
            'from' => 'SomeUser',
            'text' => 'Some cool message'
        ];

        $message = Message::create($input['from'], $input['text']);

        $this->assertArraySubset($input, $message);
        $this->assertInternalType('integer', $message['createdAt']);
        $this->assertTrue($message['opts']['plain']);
    }

    public function testShareLocation()
    {
        $latitude = '3';
        $longitude = '2';
        $user = 'SomeUser';
        $message = Message::shareUserLocation($user, $latitude, $longitude);

        $expected_message = [
            'from' => $user, 
            'text' => 'My current location'
        ];

        $this->assertArraySubset($expected_message, $message);

        $expected_url = "https://www.google.com/maps?q=$latitude,$longitude";
        $this->assertEquals($expected_url, $message['opts']['href']);
    }

    public function messageTypeProvider()
    {
        return [
            ['greeting'],
            ['joined'],
            ['leave']
        ];
    }

    /**
     * @dataProvider messageTypeProvider
     */
    public function testAdminMessages($type)
    {
        $admin = 'Admin';
        $user = 'SomeUser';

        $message = Message::fromAdmin($user, $type);

        $expected_message = ['from' => $admin];
        $this->assertArraySubset($expected_message, $message);

        $this->assertContains($user, $message['text']);

    }
}
