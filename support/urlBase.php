<?php
    $directory = dirname($_SERVER['SCRIPT_NAME']);
    if ($directory === '/') $directory = ltrim($directory, '/');
    $urlBase = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $directory;
