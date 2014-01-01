<?php

/**
 * Page de déparrage du framework
 * @package FrameTool
 */

/**
 * Cosntante annonçant que le framework est instancié
 */
define('NPE_INDEX', true);

/**
 * Racourci pour le DIRECTORY_SEPARATOR
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * Répertoire racine du projet
 */
$root = dirname(__FILE__) . DS;

session_start();
require('config.php');
require 'libs' . DS . 'common.php';

//Initialisation du PDO
$pdo = new PDO($dsn, $db_user, $db_pass);

// Initialisation du système de template
include 'libs' . DS . 'Smarty' . DS . 'Smarty.class.php';
$tpl = new Smarty();
$tpl->compile_dir = $root . 'tmp';
$tpl->template_dir = $root . 'templates';
$tpl->registerPlugin('function', 'mkurl', 'mkurl_smarty');
$tpl->registerPlugin('block', 'acl', 'acl_smarty');

if (!is_dir($tpl->compile_dir))
    @mkdir($tpl->compile_dir, 0777);


// Etape 1, on charge la configuration sur l'environnement présent.
$conf = $pdo->prepare("SELECT * FROM config WHERE env is NULL OR env = ?");
$conf->bindValue(1, $env);
$conf->execute();
while ($dat = $conf->fetch()) {
    $$dat['name'] = $dat['value'];
}

// Etape 2, calcul du chemin d'execution
if (!isset($_REQUEST['action']))
    redirect('index');

$action = null;
if (isset($_GET['action']))
    $action = $_GET['action'];
$action = basename($action);

$page = 'index';
if (isset($_GET['page']))
    $page = $_GET['page'];
$page = basename($page);

if (!file_exists($root . 'action' . DS . $action . '.php')) {
    $action = 'syscore';
    $page = 'nomod';
}

// Etape 3, vérification des droits d'accès
if (!isset($_SESSION['user']))
    $_SESSION['user'] = false;
$tpl->assign('_user', $_SESSION['user']);
if ($_SESSION['user']) {
    $sections = $pdo->prepare('SELECT * FROM user_sections LEFT JOIN sections ON us_section = section_id WHERE us_user = ?');
    $sections->bindValue(1, $_SESSION['user']['user_id']);
    $sections->execute();
    $_SESSION['user']['sections'] = array();
    while ($line = $sections->fetch())
        $_SESSION['user']['sections'][$line['section_id']] = $line;
}

modsecu($action, $page);
needAcl(getAclLevel($action, $page));

// Etape 4 lancement du module
modexec($action, $page);
modexec('syscore', 'moderror');
quit();
?>
