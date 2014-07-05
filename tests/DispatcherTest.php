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
        $snippet->payload           = 'one';

        $snippet->type              = 'two';
        $snippet->projectApiKey     = 'three';
        $snippet->encryptionVersion = 'four';
        $snippet->isEncrypted       = 'five';

        $snippet->fileName          = 'six';
        $snippet->lineNumber        = 'seven';
        $snippet->context           = 'eight';
        $snippet->address           = 'nine';
        $snippet->hostname          = 'ten';

        $dispatcher = new Dispatcher($config, $client);

        $result = $dispatcher->prepareForDispatch(array($snippet));

        $expected = array(
            array(
                'payload'           => 'one',

                'type'              => 'two',
                'projectApiKey'     => 'three',
                'encryptionVersion' => 'four',
                'isEncrypted'       => 'five',

                'fileName'          => 'six',
                'lineNumber'        => 'seven',
                'context'           => 'eight',
                'address'           => 'nine',
                'hostname'          => 'ten'
            )
        );

        $this->assertEquals($expected, $result);

        $this->assertInternalType('array', $result);
    }
}
