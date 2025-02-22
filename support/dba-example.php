<?php
    $dbName = 'tlv';
    $host = 'localhost';
    $port = '3306';

    DB::$user = 'tlvUser';
    DB::$password = 'tlvUser';
   	# mySQL/mariadb
    DB::$dsn = 'mysql:host=' . $host. ';port=' . $port . ';charset=utf8;dbname=' . $dbName;

    # SQLite database
    #DB::$dsn = 'sqlite:/exact/path/to/sqstorage/support/sqlite_db.sq3';

    // https://meekro.com/docs/logging.html
    DB::$logfile = null;

    // Make use of user login and registration feature
    $useRegistration = false; // true OR false

    // Make use of pretty urls, might raise 404 errors on Pi4
    $usePrettyURLs = true; // true OR false
