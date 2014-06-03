<?php

use Phpconsole\Phpconsole;

class PhpconsoleTest extends PHPUnit_Framework_TestCase {

    public function testSend()
    {
        $config = Mockery::mock('Phpconsole\Config');

        $queue = Mockery::mock('Phpconsole\Queue');
        $queue->shouldReceive('add')->once();

        $snippet = Mockery::mock('Phpconsole\Snippet');
        $snippet->shouldReceive('setOptions')->once();
        $snippet->shouldReceive('setPayload')->once();

        $snippetFactory = Mockery::mock('Phpconsole\SnippetFactory');
        $snippetFactory->shouldReceive('create')->andReturn($snippet);

        $dispatcher = Mockery::mock('Phpconsole\Dispatcher');
        $dispatcher->shouldReceive('dispatch')->once();

        $phpconsole = new Phpconsole($config, $queue, $snippetFactory, $dispatcher);

        $result = $phpconsole->send('Hello world!');
        $expected = 'Hello world!';

        $this->assertEquals($expected, $result);
    }

    public function testSendToAll()
    {
        $config = Mockery::mock('Phpconsole\Config');

        $queue = Mockery::mock('Phpconsole\Queue');
        $queue->shouldReceive('add')->once();

        $snippet = Mockery::mock('Phpconsole\Snippet');
        $snippet->shouldReceive('setOptions')->once();
        $snippet->shouldReceive('setPayload')->once();

        $snippetFactory = Mockery::mock('Phpconsole\SnippetFactory');
        $snippetFactory->shouldReceive('create')->andReturn($snippet);

        $dispatcher = Mockery::mock('Phpconsole\Dispatcher');
        $dispatcher->shouldReceive('dispatch')->once();

        $phpconsole = new Phpconsole($config, $queue, $snippetFactory, $dispatcher);

        $result = $phpconsole->sendToAll('Hello world!');
        $expected = 'Hello world!';

        $this->assertEquals($expected, $result);
    }
}
