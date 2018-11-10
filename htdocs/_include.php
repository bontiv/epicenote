<?php

if (!defined('NPE_INDEX')) die;


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

require_once 'bootstrap.php';
require_once $srcdir . DIRECTORY_SEPARATOR . 'loader.php';

//require_once(TOOLKIT_PATH . '_toolkit_loader.php');
