<?php

use phpconsole\phpconsole;

class phpconsoleTest extends PHPUnit_Framework_TestCase
{
    public function testSend()
    {
        $phpconsole = new phpconsole;

        $result = $phpconsole->send('Hello world!');
        $expected = 'Hello world!';

        $this->assertEquals($expected, $result);
    }

    public function testSendToAll()
    {
        $phpconsole = new phpconsole;

        $result = $phpconsole->sendToAll('Hello world!');
        $expected = 'Hello world!';

        $this->assertEquals($expected, $result);
    }

}
