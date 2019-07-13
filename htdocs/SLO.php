<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define('NPE_INDEX', true);
define('CUSTOM', true);

require_once '_include.php';

$auth = new \OneLogin\Saml2\Auth();

if (isset($_SESSION) && isset($_SESSION['LogoutRequestID'])) {
    $requestID = $_SESSION['LogoutRequestID'];
} else {
    $requestID = null;
}

$_SESSION['user'] = false;
unset($_SESSION['user']);
$_SESSION = array();

$auth->processSLO(false, $requestID);

$errors = $auth->getErrors();
#var_dump($errors);
redirect('index');
