<?php

$middleware->register(function ($request) {
    if ($request->isJson()) {
        // JSON token check
        $token = $request->header('X_AUTH_TOKEN');
        $source = $request->raw();
    } else {
        // Field token check
        $token = $request->input('_token');
        $data = $request->all();
        if (isset($data['_token'])) {
            unset($data['_token']);
        }
        $source = buildQuery($data);
    }

    // dd($token, $source, generateAuthToken($source));

    // Validate
    if (!$token) {
        throw new RuntimeException('Empty token');
    }
    if (!validateAuthToken($source, $token)) {
        throw new RuntimeException('Invalid Token');
    }
});

$middleware->execute();