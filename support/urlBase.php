<?php
    $directory = dirname($_SERVER['SCRIPT_NAME']);
    if ($directory === '/') $directory = ltrim($directory, '/');
    $reqScheme = "http";
    if(isset($_SERVER['REQUEST_SCHEME'])){
      $reqScheme = $_SERVER['REQUEST_SCHEME'];
    }
    $urlBase = $reqScheme . '://' . $_SERVER['HTTP_HOST'] . $directory;
