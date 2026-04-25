<?php

class CsrfHelper {
    private const SESSION_KEY = 'csrf_token';

    public static function token() {
        if (empty($_SESSION[self::SESSION_KEY]) || !is_string($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public static function validate($token) {
        $storedToken = $_SESSION[self::SESSION_KEY] ?? '';
        $incomingToken = is_string($token) ? $token : '';

        return is_string($storedToken)
            && $storedToken !== ''
            && $incomingToken !== ''
            && hash_equals($storedToken, $incomingToken);
    }

    public static function rotate() {
        $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
    }
}