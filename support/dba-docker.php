<?php
    DB::$user = getenv('DB_USER') ?: 'tlvUser';
    DB::$password = getenv('DB_PASSWORD') ?: 'tlvUser';
    DB::$dbName = getenv('DB_NAME') ?: 'tlv';
    DB::$encoding = 'utf8';
    DB::$host = getenv('DB_HOST') ?: 'db';
    DB::$port = (int)(getenv('DB_PORT') ?: '3306');

    // Make use of user login and registration feature
    $useRegistration = filter_var(getenv('USE_REGISTRATION'), FILTER_VALIDATE_BOOLEAN); // true OR false

    // Make use of pretty urls, might raise 404 errors on docker and Pi4
    $usePrettyURLs = filter_var(getenv('USE_PRETTY_URLS'), FILTER_VALIDATE_BOOLEAN); // true OR false
