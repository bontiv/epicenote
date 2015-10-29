<?php

/**
 * Fichier de chargement type. Il permet de charger un projet
 * en êtant le plus flexible possible.
 *
 * Pour éviter d'avoir des problèmes lors des déploiement à partir du GIT,
 * toutes les variables ici-présente peuvent être redéfinies dans un fichier
 * boostrap.php dans ce même dossier.
 *
 * @package FrameTool
 */
/**
 * Annonce le démarrage du framework
 */
define('NPE_INDEX', true);

/**
 * Liste des paramètres
 */
// Chaine de connexion SQL
$dsn = 'mysql:host=localhost;dbname=epicenote';

// Utilisateur SQL
$db_user = 'epicenote';

// Mot de passe SQL
$db_pass = 'epicenote';

// Environnement
$env = 'def';

// Base des URLs
$urlbase = 'index.php?';

// Options de base des URLs
$urlops = array();

// Dossier des sources
$srcdir = '..' . DIRECTORY_SEPARATOR . 'src';

// Dossier des fichiers temporaires
$tmpdir = '..' . DIRECTORY_SEPARATOR . 'tmp';


/*
 *  S'il existe un fichier bootstrap, on le charge pour réécrire les valeures
 * par défaut
 */

if (file_exists('bootstrap.php'))
    include_once('bootstrap.php');

/*
 * Ensuite on lance le tout !
 */
require_once $srcdir . DIRECTORY_SEPARATOR . 'loader.php';

// Etape 2, calcul du chemin d'execution
$action = 'index';
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

// Redirection si pas d'action défini
if (!isset($_REQUEST['action']))
    redirect('index');


securityTime();
securityCSRF();

run();
