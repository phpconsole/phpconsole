<?php

/**
 * A detached logging facility for PHP to aid your daily development routine.
 *
 * Watch quick tutorial at: https://vimeo.com/58393977
 *
 * @link http://phpconsole.com
 * @link https://github.com/phpconsole
 * @copyright Copyright (c) 2012 - 2014 phpconsole.com
 * @license See LICENSE file
 * @version 3.5.1
 */

namespace Phpconsole;

class Debugger
{
    protected $config;

    public function __construct(Config &$config = null)
    {
        $this->config = $config ?: new Config;
    }

    public function displayDebugInfo()
    {
        if ($this->config->debug) {

            echo '
            <style>
                .phpconsole-debugger {
                    background-color: #E7E6E3;
                    padding: 20px;
                    font-family: Verdana;
                }

                .phpconsole-debugger .phpconsole-header {
                    font-size: 24px;
                    margin: 0 0 20px;
                }

                .phpconsole-debugger .phpconsole-subheader {
                    font-size: 14px;
                    margin: 0 0 20px;
                }

                .phpconsole-debugger .phpconsole-subheader a {
                    color: #08c;
                    text-decoration: none;
                }

                .phpconsole-debugger .phpconsole-subheader a:hover {
                    color: #005580;
                    text-decoration: underline;
                }

                .phpconsole-debugger .phpconsole-table {
                    background-color: #fff;
                    border: 1px solid #aaaaaa;
                    width: 100%;
                    border-spacing: 0;
                    border-collapse: collapse;
                    font-size: 12px;
                    margin-bottom: 20px;
                }

                .phpconsole-debugger .phpconsole-table td {
                    padding: 7px;
                    margin: 0;
                    vertical-align: top;
                }

                .phpconsole-debugger .phpconsole-table td:first-child {
                    width: 150px;
                }

                .phpconsole-debugger .phpconsole-table thead td {
                    font-weight: bold;
                }

                .phpconsole-debugger .phpconsole-table tbody td {
                    border-top: 1px solid #ddd;
                }

                .phpconsole-debugger .phpconsole-table tbody tr:nth-child(odd) {
                    background-color: #f9f9f9;
                }

                .phpconsole-highlight {
                    color: #c00;
                }
            </style>
            ';


            $log = $this->getLog();
            $log_html = '';

            foreach ($_ENV['PHPCONSOLE_DEBUG_LOG'] as $row) {
                $log_html .= '
                <tr class="'.($row[2]?'phpconsole-highlight':'').'">
                    <td>
                        '.$row[0].'
                    </td>
                    <td>
                        '.$row[1].'
                    </td>
                </tr>
                ';
            }

            $info = $this->getInfo();

            echo '
            <div class="phpconsole-debugger">

                <h1 class="phpconsole-header">
                    Phpconsole debug info
                </h1>

                <p class="phpconsole-subheader">
                    Need help? Contact support: <a href="mailto:support@phpconsole.com">support@phpconsole.com</a>
                </p>

                <table class="phpconsole-table">
                    <thead>
                        <tr>
                            <td>Timestamp</td>
                            <td>Event</td>
                        </tr>
                    </thead>
                    <tbody>
                        '.$log_html.'
                    </tbody>
                </table>

                <table class="phpconsole-table">
                    <thead>
                        <tr>
                            <td>Name</td>
                            <td>Value</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Phpconsole version</td>
                            <td>'.$info['phpconsoleVersion'].'</td>
                        </tr>
                        <tr>
                            <td>PHP version</td>
                            <td>'.$info['phpVersion'].'</td>
                        </tr>
                        <tr>
                            <td>cURL enabled</td>
                            <td>'.$info['curlEnabled'].'</td>
                        </tr>
                        <tr>
                            <td>Hostname</td>
                            <td>'.$info['hostname'].'</td>
                        </tr>
                        <tr>
                            <td>Config values</td>
                            <td>
                                <pre>'.$info['config'].'</pre>
                            </td>
                        </tr>
                        <tr>
                            <td>$_SERVER values</td>
                            <td>
                                <pre>'.$info['server'].'</pre>
                            </td>
                        </tr>
                        <tr>
                            <td>Config exists</td>
                            <td>'.$info['classExists']['Config'].'</td>
                        </tr>
                        <tr>
                            <td>Debugger exists</td>
                            <td>'.$info['classExists']['Debugger'].'</td>
                        </tr>
                        <tr>
                            <td>Dispatcher exists</td>
                            <td>'.$info['classExists']['Dispatcher'].'</td>
                        </tr>
                        <tr>
                            <td>Encryptor exists</td>
                            <td>'.$info['classExists']['Encryptor'].'</td>
                        </tr>
                        <tr>
                            <td>MetadataWrapper exists</td>
                            <td>'.$info['classExists']['MetadataWrapper'].'</td>
                        </tr>
                        <tr>
                            <td>P exists</td>
                            <td>'.$info['classExists']['P'].'</td>
                        </tr>
                        <tr>
                            <td>Phpconsole exists</td>
                            <td>'.$info['classExists']['Phpconsole'].'</td>
                        </tr>
                        <tr>
                            <td>Queue exists</td>
                            <td>'.$info['classExists']['Queue'].'</td>
                        </tr>
                        <tr>
                            <td>Snippet exists</td>
                            <td>'.$info['classExists']['Snippet'].'</td>
                        </tr>
                        <tr>
                            <td>SnippetFactory exists</td>
                            <td>'.$info['classExists']['SnippetFactory'].'</td>
                        </tr>
                    </tbody>
                </table>

            </div>
            ';

        }
    }

    public function getLog()
    {
        return $_ENV['PHPCONSOLE_DEBUG_LOG'];
    }

    public function getInfo()
    {
        $info = array(
            'phpconsoleVersion' => Phpconsole::VERSION,
            'phpVersion' => phpversion(),
            'curlEnabled' => in_array('curl', get_loaded_extensions())?'yes':'no',
            'hostname' => gethostname(),
            'config' => print_r((array)$this->config, true),
            'server' => print_r($_SERVER, true),
            'classExists' => array(
                'Config'          => class_exists('Phpconsole\Config')?'yes':'no',
                'Debugger'        => class_exists('Phpconsole\Debugger')?'yes':'no',
                'Dispatcher'      => class_exists('Phpconsole\Dispatcher')?'yes':'no',
                'Encryptor'       => class_exists('Phpconsole\Encryptor')?'yes':'no',
                'MetadataWrapper' => class_exists('Phpconsole\MetadataWrapper')?'yes':'no',
                'P'               => class_exists('Phpconsole\P')?'yes':'no',
                'Phpconsole'      => class_exists('Phpconsole\Phpconsole')?'yes':'no',
                'Queue'           => class_exists('Phpconsole\Queue')?'yes':'no',
                'Snippet'         => class_exists('Phpconsole\Snippet')?'yes':'no',
                'SnippetFactory'  => class_exists('Phpconsole\SnippetFactory')?'yes':'no',
            )
        );

        return $info;
    }
}
