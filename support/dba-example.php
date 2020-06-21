<?php
    DB::$user = 'tlvUser';
    DB::$password = 'tlvUser';
    DB::$dbName = 'tlv';
    DB::$encoding = 'utf8';
    DB::$host = 'localhost';
    // DB::$host = '123.111.10.23'; // Use for webserver
    DB::$port = '3306';

    // Make use of user login and registration feature
    $useRegistration = false; // true OR false
