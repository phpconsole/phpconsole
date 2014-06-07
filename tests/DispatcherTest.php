<?php

use Phpconsole\Dispatcher;

class DispatcherTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testDispatch()
    {
        $config = Mockery::mock('Phpconsole\Config');

        $client = Mockery::mock('GuzzleHttp\Client');
        $client->shouldReceive('post')->once();

        $snippet = Mockery::mock('Phpconsole\Snippet');

        $queue = Mockery::mock('Phpconsole\Queue');
        $queue->shouldReceive('flush')->andReturn(array($snippet));

        $dispatcher = new Dispatcher($config, $client);

        $dispatcher->dispatch($queue);
    }

    public function testPrepareForDispatch()
    {
        $config = Mockery::mock('Phpconsole\Config');

        $client = Mockery::mock('GuzzleHttp\Client');

        $snippet = Mockery::mock('Phpconsole\Snippet');
        $snippet->payload = 'one';
        $snippet->type = 'two';
        $snippet->projectApiKey = 'three';
        $snippet->fileName = 'four';
        $snippet->lineNumber = 'five';
        $snippet->context = 'six';
        $snippet->address = 'seven';

        $dispatcher = new Dispatcher($config, $client);

        $result = $dispatcher->prepareForDispatch(array($snippet));

        $expected = array(
            array(
                'payload' => 'one',
                'type' => 'two',
                'project_api_key' => 'three',
                'file_name' => 'four',
                'line_number' => 'five',
                'context' => 'six',
                'address' => 'seven'
            )
        );

        $this->assertEquals($expected, $result);

        $this->assertInternalType('array', $result);
    }
}
