<?php
    $dbName = getenv('DB_NAME') ?: 'tlv';
    $host = getenv('DB_HOST') ?: 'db';
    $port = (int)(getenv('DB_PORT') ?: '3306');

    DB::$user = getenv('DB_USER') ?: 'tlvUser';
    DB::$password = getenv('DB_PASSWORD') ?: 'tlvUser';

    DB::$dsn = 'mysql:host=' . $host. ';port=' . $port . ';charset=utf8;dbname=' . $dbName;

    // https://meekro.com/docs/logging.html
    DB::$logfile = null;

    // Make use of user login and registration feature
    $useRegistration = filter_var(getenv('USE_REGISTRATION'), FILTER_VALIDATE_BOOLEAN); // true OR false

    // Make use of pretty urls, might raise 404 errors on docker and Pi4
    $usePrettyURLs = filter_var(getenv('USE_PRETTY_URLS'), FILTER_VALIDATE_BOOLEAN); // true OR false
