<?php

use Phpconsole\Snippet;

class SnippetTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testSetPayloadLineOfTextWithPrintR()
    {
        $config = Mockery::mock('Phpconsole\Config');
        $config->captureWith = 'print_r';

        $snippet = new Snippet($config);

        $snippet->setPayload('Hello World!');

        $result = base64_decode($snippet->payload);
        $expected = 'Hello World!';

        $this->assertEquals($expected, $result);
    }

    public function testSetPayloadLineOfTextWithVarDump()
    {
        $config = Mockery::mock('Phpconsole\Config');
        $config->captureWith = 'var_dump';

        $snippet = new Snippet($config);

        $snippet->setPayload('Hello World!');

        $result = base64_decode($snippet->payload);
        ob_start();
        var_dump('Hello World!');
        $expected = ob_get_clean();

        $this->assertEquals($expected, $result);
    }

    public function testSetPayloadArrayWithPrintR()
    {
        $config = Mockery::mock('Phpconsole\Config');
        $config->captureWith = 'print_r';

        $snippet = new Snippet($config);

        $snippet->setPayload(array('a', 'b', 'c', array(1, 2, 3)));

        $result = base64_decode($snippet->payload);
        $expected = print_r(array('a', 'b', 'c', array(1, 2, 3)), true);

        $this->assertEquals($expected, $result);
    }

    public function testSetPayloadArrayWithVarDump()
    {
        $config = Mockery::mock('Phpconsole\Config');
        $config->captureWith = 'var_dump';

        $snippet = new Snippet($config);

        $snippet->setPayload(array('a', 'b', 'c', array(1, 2, 3)));

        $result = base64_decode($snippet->payload);
        ob_start();
        var_dump(array('a', 'b', 'c', array(1, 2, 3)));
        $expected = ob_get_clean();

        $this->assertEquals($expected, $result);
    }

    public function testSetPayloadTrueFalseNullReplacedWithPrintR()
    {
        $config = Mockery::mock('Phpconsole\Config');
        $config->captureWith = 'print_r';

        $snippet = new Snippet($config);

        $snippet->setPayload(array(true, false, null)); // will be replace with text representations

        $result = base64_decode($snippet->payload);
        $expected = print_r(array('true', 'false', 'null'), true);

        $this->assertEquals($expected, $result);
    }

    public function testSetPayloadTrueFalseNullReplacedWithVarDump()
    {
        $config = Mockery::mock('Phpconsole\Config');
        $config->captureWith = 'var_dump';

        $snippet = new Snippet($config);

        $snippet->setPayload(array(true, false, null));

        $result = base64_decode($snippet->payload);
        ob_start();
        var_dump(array(true, false, null));
        $expected = ob_get_clean();

        $this->assertEquals($expected, $result);
    }

    public function testSetOptionsEmpty()
    {
        $config = Mockery::mock('Phpconsole\Config');
        $config->shouldReceive('getApiKeyFor')->with('qwerty')->once()->andReturn('fakeapikeyforpeter');
        $config->defaultProject = 'qwerty';

        $snippet = new Snippet($config);

        $snippet->setOptions(array());

        $result = $snippet->type;
        $expected = 'normal';

        $this->assertEquals($expected, $result);

        $result = $snippet->projectApiKey;
        $expected = 'fakeapikeyforpeter';

        $this->assertEquals($expected, $result);
    }

    public function testSetOptionsName()
    {
        $config = Mockery::mock('Phpconsole\Config');
        $config->shouldReceive('getApiKeyFor')->with('peter')->once()->andReturn('fakeapikeyforpeter');

        $snippet = new Snippet($config);

        $snippet->setOptions('peter');

        $result = $snippet->type;
        $expected = 'normal';

        $this->assertEquals($expected, $result);

        $result = $snippet->projectApiKey;
        $expected = 'fakeapikeyforpeter';

        $this->assertEquals($expected, $result);
    }

    public function testSetOptionsType()
    {
        $config = Mockery::mock('Phpconsole\Config');
        $config->shouldReceive('getApiKeyFor')->once()->andReturn('fakeapikeyforpeter');

        $snippet = new Snippet($config);

        $snippet->setOptions(array('type' => 'something'));

        $result = $snippet->type;
        $expected = 'something';

        $this->assertEquals($expected, $result);
    }

    public function testSetMetadata()
    {
        $config = Mockery::mock('Phpconsole\Config');
        $config->backtraceDepth = 3;
        $config->contextSize = 5;

        $debugBacktrace = array(
            '3' => array(
                'file' => '/some/path/to/file.php',
                'line' => 5
                )
            );

        $file = array(
            '<?php',
            '',
            'Route::get(\'/\', function()',
            '{',
            '    p::send(\'Hello World!\');',
            '',
            '    return View::make(\'hello\');',
            '});'
            );

        $server = array(
            'HTTP_HOST' => 'mywebsite.dev',
            'SERVER_PORT' => 8000,
            'REQUEST_URI' => '/file.php?some=value&someother=value'
            );

        $hostname = 'development01';

        $metadataWrapper = Mockery::mock('Phpconsole\MetadataWrapper');
        $metadataWrapper->shouldReceive('debugBacktrace')->once()->andReturn($debugBacktrace);
        $metadataWrapper->shouldReceive('file')->once()->andReturn($file);
        $metadataWrapper->shouldReceive('server')->once()->andReturn($server);
        $metadataWrapper->shouldReceive('gethostname')->once()->andReturn($hostname);

        $snippet = new Snippet($config, $metadataWrapper);

        $snippet->setMetadata();

        $result = base64_decode($snippet->fileName);
        $expected = '/some/path/to/file.php';

        $this->assertEquals($expected, $result);

        $result = base64_decode($snippet->lineNumber);
        $expected = 5;

        $this->assertEquals($expected, $result);

        $result = base64_decode($snippet->context);
        $expected = json_encode(
            array(
                '',
                '<?php',
                '',
                'Route::get(\'/\', function()',
                '{',
                '    p::send(\'Hello World!\');',
                '',
                '    return View::make(\'hello\');',
                '});',
                '',
                ''
            )
        );

        $this->assertEquals($expected, $result);

        $result = base64_decode($snippet->address);
        $expected = 'http://mywebsite.dev:8000/file.php?some=value&someother=value';

        $this->assertEquals($expected, $result);

        $result = base64_decode($snippet->hostname);
        $expected = $hostname;

        $this->assertEquals($expected, $result);
    }
}
