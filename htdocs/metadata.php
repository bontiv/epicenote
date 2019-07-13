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
$settings = $auth->getSettings();
$metadata = $settings->getSPMetadata();
$errors = $settings->validateMetadata($metadata);
if (empty($errors)) {
    header('Content-Type: text/xml');
    echo $metadata;
} else {
    var_dump($errors);
}
