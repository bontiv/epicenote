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

if (isset($_SESSION) && isset($_SESSION['AuthNRequestID'])) {
    $requestID = $_SESSION['AuthNRequestID'];
} else {
    $requestID = null;
}

$auth->processResponse($requestID);
unset($_SESSION['AuthNRequestID']);

$errors = $auth->getErrors();

if (!empty($errors)) {
    echo '<p>', implode(', ', $errors), '</p>';
    exit();
}

if (!$auth->isAuthenticated()) {
    echo "<p>Not authenticated</p>";
    exit();
}

$_SESSION['samlUserdata'] = $auth->getAttributes();
$_SESSION['samlNameId'] = $auth->getNameId();
$_SESSION['samlNameIdFormat'] = $auth->getNameIdFormat();
$_SESSION['samlSessionIndex'] = $auth->getSessionIndex();
$attributes = $_SESSION['samlUserdata'];
$nameId = $_SESSION['samlNameId'];

global $pdo;
$sql = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
$sql->bindValue(1, $attributes['uid'][0]);
$sql->execute();
$user = $sql->fetch();
if (!$user) {
    echo 'Bad User';
    die;
}

$log = $pdo->prepare('INSERT INTO logaudit (la_user, la_ip, la_date, la_type) VALUES (:user, :ip, now(), :type)');
$log->bindValue(':user', null);
$log->bindValue(':ip', $_SERVER['REMOTE_ADDR']);

$last = $pdo->prepare('SELECT COUNT(*) FROM logaudit WHERE la_date > now() - time("01:00:00") AND la_type = \'DENY\' AND (la_user = :user OR la_ip = :ip)');
$last->bindValue(':user', $user);
$last->bindValue(':ip', $_SERVER['REMOTE_ADDR']);
$last->execute();

$_SESSION['user'] = $user;
$_SESSION['user']['role'] = aclFromText($user['user_role']);
unset($_SESSION['random']);
$_SESSION['urltok'] = substr(sha1(uniqid()), 0, 16);
$log->bindValue(':type', 'ACCEPT');
$log->execute();

//Update user private profile value
_upd_user($user['user_id']);

if (isset($_POST['RelayState']) && \OneLogin\Saml2\Utils::getSelfURL() != $_POST['RelayState']) {
    $auth->redirectTo($_POST['RelayState']);
}


echo '<h1>Identified user: ' . htmlentities($nameId) . '</h1>';

if (!empty($attributes)) {
    echo '<h2>' . _('User attributes:') . '</h2>';
    echo '<table><thead><th>' . _('Name') . '</th><th>' . _('Values') . '</th></thead><tbody>';
    foreach ($attributes as $attributeName => $attributeValues) {
        echo '<tr><td>' . htmlentities($attributeName) . '</td><td><ul>';
        foreach ($attributeValues as $attributeValue) {
            echo '<li>' . htmlentities($attributeValue) . '</li>';
        }
        echo '</ul></td></tr>';
    }
    echo '</tbody></table>';
} else {
    echo _('No attributes found.');
}