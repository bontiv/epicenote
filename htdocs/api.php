<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('NPE_INDEX', true);

if (file_exists('bootstrap.php')) {
    include_once('bootstrap.php');
}

$action = 'api';

//Auto config
if ($_SERVER['PATH_INFO'] == '/.well-known/openid-configuration') {
    $page = 'config';
} elseif ($_SERVER['PATH_INFO'] == '/authorize') {
    $page = 'authorize';
} elseif ($_SERVER['PATH_INFO'] == '/token') {
    $page = 'token';
} elseif ($_SERVER['PATH_INFO'] == '/jwks.json') {
    $page = 'jwks';
} elseif ($_SERVER['PATH_INFO'] == '/userinfo') {
    $page = 'userinfo';
} elseif ($_SERVER['PATH_INFO'] == '/register') {
    $page = 'register';
} elseif ($_SERVER['PATH_INFO'] == '/signup') {
    $page = 'signup';
} else {
    echo $_SERVER['PATH_INFO'];
    exit;
}

require_once $srcdir . DIRECTORY_SEPARATOR . 'loader.php';
run();
