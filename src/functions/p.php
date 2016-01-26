<?php

if (!function_exists('p')) {

    function p($payload, $options = array(), $metadata = array())
    {
        global $phpconsoleObject;

        if (is_null($phpconsoleObject)) {

            $config = new \Phpconsole\Config;
            $config->loadFromArray(array(
                'backtraceDepth' => 3
            ));

            $phpconsoleObject = new \Phpconsole\Phpconsole($config);
        }

        $phpconsoleObject->send($payload, $options, $metadata);

        return $payload;
    }
}
