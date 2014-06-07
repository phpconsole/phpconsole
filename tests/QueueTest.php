<?php

use Phpconsole\Queue;

class QueueTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testAdd()
    {
        $config = Mockery::mock('Phpconsole\Config');

        $snippet = Mockery::mock('Phpconsole\Snippet');

        $queue = new Queue($config);

        $result = $queue->add($snippet);
        $expected = $snippet;

        $this->assertEquals($expected, $result);
    }

    public function testFlush()
    {
        $config = Mockery::mock('Phpconsole\Config');

        $snippet1 = Mockery::mock('Phpconsole\Snippet');
        $snippet2 = Mockery::mock('Phpconsole\Snippet');

        $queue = new Queue($config);
        $queue->add($snippet1);
        $queue->add($snippet2);

        $result = $queue->flush();
        $expected = array($snippet1, $snippet2);

        $this->assertEquals($expected, $result);
    }
}
