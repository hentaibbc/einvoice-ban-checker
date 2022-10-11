<?php

if (! function_exists('buildQuery') ) {
    function buildQuery($data)
    {
        ksort($data);

        return http_build_query($data);
    }
}

if (! function_exists('responseJson') ) {
    function responseJson(array $data = [], int $code = 200)
    {
        header('HTTP/1.0 '.$code.' '.statusMessage($code));
        header('Content-Type: application/json');

        echo json_encode($data);
    }
}

if (! function_exists('statusMessage') ) {
    function statusMessage(int $code = 200)
    {
        static $map = [
            200     => 'OK',
            400     => 'Bad Request',
            401     => 'Unauthorized',
            403     => 'Forbidden',
            404     => 'Not Found',
            405     => 'Method Not Allowed',
            422     => 'Unprocessable Entity',
        ];

        return $map[$code] ?? 'Unknown';
    }
}

if (! function_exists('formatDate') ) {
    function formatDate($format, ?int $time = null)
    {
        $time ??= time();
        return date($format, $time);
    }
}

if (! function_exists('generateAuthToken') ) {
    function generateAuthToken(string $str)
    {
        global $config;

        return hash('sha256', $str.$config['secret']);
    }
}

if (! function_exists('validateAuthToken') ) {
    function validateAuthToken(string $str, string $token)
    {
        return generateAuthToken($str) == $token;
    }
}

if (! function_exists('dd') ) {
    function dd(...$args)
    {
        foreach ($args as $arg) {
            var_export($arg);
            echo str_repeat(PHP_EOL, 2);
        }

        exit;
    }
}