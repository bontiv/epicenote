<?php

/**
 * Module de gestion des utilisateurs
 * Ce module permet de gérer son compte d'utilisateur ainsi que les utilisateurs
 * du site.
 * @package Epicenote
 */
/* * ******************************* */

/*
 * Experimental : Google Sync
 */

function user_sync() {
    global $config;

    $url = "https://accounts.google.com/o/oauth2/auth";

    $params = array(
        "response_type" => "code",
        "client_id" => $config['GoogleApps']['clientID'],
        "redirect_uri" => $config['GoogleApps']['returnUrl'],
        "scope" => "https://www.googleapis.com/auth/admin.directory.group"
    );

    $request_to = $url . '?' . http_build_query($params);

    header("Location: " . $request_to);
    quit();
}

function user_sync2() {
    global $srcdir, $tpl, $config;

    require_once $srcdir . '/libs/GoogleApi.php';

    $api = new GoogleApi();
    $Gregistred = array();
    $Sregistred = array();
    $pageToken = null;

    do {
        $members = $api->getGroupMembers($config['GoogleApps']['members_ml'], $pageToken);
        foreach ($members->members as $member) {
            if (isset($member->email)) {
                $Gregistred[] = strtolower($member->email);
            }
        }
        $pageToken = isset($members->nextPageToken) ? $members->nextPageToken : null;
    } while ($pageToken !== null);

    $mdl = new Modele('users');
    $mdl->find('user_role = "USER" OR user_role = "ADMINISTRATOR"');
    while ($mdl->next()) {
        $Sregistred[] = strtolower($mdl->user_email);
    }

    $tpl->assign('add', array_diff($Sregistred, $Gregistred));
    $tpl->assign('del', array_diff($Gregistred, $Sregistred));
    $tpl->display('user_syncProc.tpl');
    quit();
}

function user_execSync() {
    global $srcdir, $config;

    require_once $srcdir . '/libs/GoogleApi.php';

    $api = new GoogleApi();
    $Gregistred = array();
    $Sregistred = array();
    $pageToken = null;

    do {
        $members = $api->getGroupMembers($config['GoogleApps']['members_ml'], $pageToken);
        foreach ($members->members as $member) {
            if (isset($member->email)) {
                $Gregistred[] = strtolower($member->email);
            }
        }
        $pageToken = isset($members->nextPageToken) ? $members->nextPageToken : null;
    } while ($pageToken !== null);


    $mdl = new Modele('users');
    $mdl->find('user_role = "USER" OR user_role = "ADMINISTRATOR"');
    while ($mdl->next()) {
        $Sregistred[] = strtolower($mdl->user_email);
    }

    // Add
    foreach (array_diff($Sregistred, $Gregistred) as $mail) {
        $api->addGroupMember($config['GoogleApps']['members_ml'], $mail);
    }

    // Del
    foreach (array_diff($Gregistred, $Sregistred) as $mail) {
        $api->delGroupMember($config['GoogleApps']['members_ml'], $mail);
    }

    redirect("user", "sync2");
}

function user_syncReturn() {
    global $config;

    if (isset($_GET['code'])) {
        // try to get an access token
        $code = $_GET['code'];
        $url = 'https://accounts.google.com/o/oauth2/token';
        $params = array(
            "code" => $code,
            "client_id" => $config['GoogleApps']['clientID'],
            "client_secret" => $config['GoogleApps']['secret'],
            "redirect_uri" => $config['GoogleApps']['returnUrl'],
            "grant_type" => "authorization_code"
        );

        $request = curl_init($url);
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_POSTFIELDS, $params);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        $body = curl_exec($request);
        $responseObj = json_decode($body);

        $_SESSION['user']['GoogleToken'] = $responseObj->access_token;
        redirect('user', 'syncProc');
    }
}

function user_syncProc() {
    global $tpl;

    $ch = curl_init('https://www.googleapis.com/admin/directory/v1/groups/membres@epitanime.com/members?maxResults=1000&oauth_token=' . $_SESSION['user']['GoogleToken']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $members = json_decode(curl_exec($ch));
    $Gregistred = array();
    $Sregistred = array();

    foreach ($members->members as $member) {
        if (isset($member->email)) {
            $Gregistred[] = strtolower($member->email);
        }
    }

    $mdl = new Modele('users');
    $mdl->find();
    while ($mdl->next()) {
        $Sregistred[] = strtolower($mdl->user_email);
    }

    $tpl->assign('add', array_diff($Sregistred, $Gregistred));
    $tpl->assign('del', array_diff($Gregistred, $Sregistred));
    display();
}

/* * ******************************* */

/**
 * Permet d'afficher la liste des utilisateurs
 * @global type $pdo
 * @global type $tpl
 */
function user_index() {
    global $pdo, $tpl;

    if (isset($_POST['search'])) {
        redirect('user', 'index', array('search' => $_POST['search'], 'role' => $_POST['role']));
    }

    $mdt = new Modele('mandate');
    $mdt->find(false, 'mandate_end DESC');
    $mdt->appendTemplate('mandates');


    $where = 'WHERE user_status != "DELETE"';
    $mandate = false;

    if (isset($_GET['search'])) {
        $where .= ' AND (user_name LIKE :search '
                . 'OR user_lastname LIKE :search '
                . 'OR user_firstname LIKE :search '
                . 'OR user_email LIKE :search )';

        if (strlen($_GET['role']) > 0) {
            $where .= ' AND (';
            $first = true;
            foreach (explode(',', $_GET['role']) as $filter) {
                if ($first) {
                    $first = false;
                } else {
                    $where .= ' OR';
                }
                if (in_array($filter, ['GUEST', 'CPLUSER', 'USER', 'ADVUSER', 'SUPERUSER', 'ADMINISTRATOR', 'SYSADMIN'])) {
                    $where .= " user_role = '$filter'";
                } elseif ($filter == 'SUBSCRIPTION') {
                    $where .= " 1 = (SELECT COUNT(*) FROM user_mandate WHERE um_user = user_id AND um_mandate = :mandate)";
                    $mandate = true;
                }
            }
            $where .= ')';
        }
    }


    $pager = new SimplePager('users', $where . 'ORDER BY user_name ASC', 'p', 20);

    if (isset($_GET['search'])) {
        $pager->bindValue('search', "%${_GET['search']}%");
    }
    if ($mandate) {
        $mandate = new Modele('mandate');
        $mandate->find(['mandate_state' => 'ACTIVE'], 'mandate_start DESC');
        if ($mandate->next()) {
            $pager->bindValue('mandate', $mandate->getKey());
        } else {
            $pager->bindValue('mandate', 0);
        }
    }

    $pager->run($tpl);
    $tpl->display('user_index.tpl');
    quit();
}

/**
 * Ajoute un utilisateur
 * Des fois c'est bien de pouvoir rajouter un utilisateur depuis le panneau d'admin pour l'ajout des nouveaux adhérents.
 */
function user_add() {
    global $pdo, $tpl;

    $tpl->assign('error', false);
    $tpl->assign('succes', false);

    if (isset($_POST['user_name'])) {
        if (autoInsert('users', 'user_')) {
            $tpl->assign('succes', true);
        } else
            $tpl->assign('error', true);
    }

    $sql = $pdo->prepare('SELECT * FROM user_types');
    $sql->execute();
    while ($type = $sql->fetch()) {
        $tpl->append('types', $type);
    }

    $tpl->display('user_add.tpl');
    quit();
}

/**
 * Suppresion d'un utilisateur
 */
function user_delete() {
    $mdl = new Modele('users');
    $mdl->fetch($_GET['user']);
    $mdl->user_status = 'DELETE';
    redirect('user', 'index', array('hsuccess' => 1));
}

/**
 * Détails d'un utilisateur
 * Et optionnellement sa vie.
 */
function user_view() {
    global $pdo, $tpl, $srcdir;

    $utime = microtime(true);
    $sql = $pdo->prepare('SELECT * FROM users LEFT JOIN user_types ON ut_id = user_type WHERE user_id = ?');
    $sql->bindValue(1, $_REQUEST['user']);
    $sql->execute();
    $user = $sql->fetch();
    $tpl->assign('user', $user);

    $sql = $pdo->prepare('SELECT * FROM user_sections LEFT JOIN sections ON section_id = us_section WHERE us_user = ?');
    $sql->bindValue(1, $user['user_id']);
    $sql->execute();
    $sections = array();
    while ($line = $sql->fetch()) {
        $sections[] = $line['section_id'];
        $tpl->append('sections', $line);
    }

    //Last connection
    $lstConnect = $pdo->prepare('SELECT la_date, la_ip FROM logaudit WHERE la_user = ? AND la_type = "ACCEPT" ORDER BY la_date DESC LIMIT 1');
    $lstConnect->bindValue(1, $user['user_id']);
    $lstConnect->execute();
    $audit = $lstConnect->fetch(PDO::FETCH_ASSOC);
    if ($audit) {
        $tpl->assign('audit', $audit);
    }

    //Member paper form print
    $mdt = new Modele('mandate');
    if ($mdt->find('`mandate_start` < now() and `mandate_end` > now() and mandate_state = "ACTIVE"', 'mandate_ago DESC')) {
        if ($mdt->next()) {
            $mdt->assignTemplate('mandate');
            $sub = new Modele('subscription');
            $sub->find(array('subscription_mandate' => $mdt->getKey()));
            $sub->appendTemplate('subs');
        }
    }
    // <end> Member paper form print


    //List events
    $sql = $pdo->prepare('SELECT * FROM event_staff'
            . ' LEFT JOIN events ON event_id = est_event'
            . ' LEFT JOIN sections ON section_id = est_section'
            . ' WHERE est_user = ?'
            . ' ORDER BY event_start DESC');
    $sql->bindValue(1, $user['user_id']);
    $sql->execute();
    while ($event = $sql->fetch(PDO::FETCH_ASSOC)) {
        $tpl->append('events', $event);
    }

    $sql = $pdo->prepare('SELECT * FROM sections WHERE section_type = "primary"');
    $sql->execute();
    while ($line = $sql->fetch())
        if (!in_array($line['section_id'], $sections))
            $tpl->append('section_list', $line);

    $mdt = new Modele('user_mandate');
    $mdt->find(array('um_user' => $_REQUEST['user']));
    while ($mdt->next()) {
        $tpl->append('mandates', $mdt->um_mandate);
    }

    $mdl = new Modele('card');
    $mdl->find(array('card_user' => $_REQUEST['user']));
    while ($l = $mdl->next()) {
        $o = new Modele('card');
        $o->fetch($mdl->card_id);
        $tpl->append('cards', $o);
    }

    require_once $srcdir . '/libs/GoogleApi.php';
    $api = new GoogleApi();
    $mls = $api->findUserGroups($user['user_email']);
    $groups = array();
    if (isset($mls->groups)) {
        $tpl->assign('groups', $mls->groups);
        foreach ($mls->groups as $group) {
            $groups[] = $group->email;
        }
    }

    $allGroups = $api->getGroupsList();
    foreach ($allGroups->groups as $group) {
        if (!in_array($group->email, $groups)) {
            $tpl->append('otherGroups', $group);
        }
    }

    //Get Bocal data
    if ($user['user_login']) {
        include_once $srcdir . '/libs/bocal.php';
        $bocal = new Bocal();
        $bdata = $bocal->getUser($user['user_login']);
        $tpl->assign('bocal', $bdata);

        if ($bdata !== false) {
            include_once $srcdir . '/libs/intra.php';
            $intra = new EIntranet();
            $tpl->assign('intra', $intra->getUserInfos($user['user_login']));
        }
    }

    //Get activities
    $sql = $pdo->prepare('SELECT * FROM marks '
            . 'LEFT JOIN participations ON part_id = mark_participation '
            . 'LEFT JOIN sections ON part_section = section_id '
            . 'LEFT JOIN events ON part_event = event_id '
            . 'WHERE mark_user = ? '
            . 'ORDER BY part_attribution_date DESC');
    $sql->bindValue(1, $user['user_id']);
    $sql->execute();
    while ($line = $sql->fetch()) {
        $tpl->append('activities', $line);
    }

    //Compta
    $mdl = new Modele('user_accounts');
    $mdl->find(array('ua_user' => $user['user_id']));

    $accounts = array(array(
            'ua_id' => 0,
            'ua_identifier' => 'Chèque',
            'ua_type' => 'cheq',
            'ua_number' => '',
    ));
    while ($mdl->next()) {
        $accounts[] = $mdl->toArray();
    }

    $tpl->assign('accounts', $accounts);
    //Fin compta

    $tpl->assign('time', microtime(true) - $utime);
    $tpl->display('user_details.tpl');
    quit();
}

/**
 * Ajoute un utilisateur comme staff d'une section
 * Cette fonctionnalité permet de gérer les sections d'un utilisateur directement depuis son compte :p
 */
function user_invit_section() {
    global $pdo;

    $sql = $pdo->prepare('INSERT INTO user_sections (us_user, us_section, us_type) VALUES (?, ?, "user")');
    $sql->bindValue(1, $_GET['user']);
    $sql->bindValue(2, $_POST['us_section']);
    $sql->execute();
    redirect('user', 'view', array('user' => $_GET['user']));
}

/**
 * Permet de quitter une section
 */
function user_quit() {
    global $pdo;

    $sql = $pdo->prepare('DELETE FROM user_sections WHERE us_user = ? AND us_section = ?');
    $sql->bindValue(1, $_GET['user']);
    $sql->bindValue(2, $_GET['section']);
    $sql->execute();
    redirect('user', 'view', array('user' => $_GET['user']));
}

function user_add_mandate($user, $mandate) {
    $usr = new Modele('users');
    $mdt = new Modele('mandate');
    $lnk = new Modele('user_mandate');
    $sub = new Modele('subscription');
    $cotis = null;

    if (preg_match('/^9([0-9]{4})([0-9]{7})[0-9]$/', $user, $matchs)) {
        $user = $matchs[2];
        $mandate = $matchs[1];
    }
    elseif (preg_match('/^([0-9]+).([0-9]+)$/', $user, $matchs)) {
        $user = $matchs[1];
        $cotis = $matchs[2];
        $sub->fetch($cotis);
        $mandate = $sub->raw_subscription_mandate;
    } else {
        return false;
    }

    $usr->fetch($user);
    $mdt->fetch($mandate);

    if ($lnk->find(array(
                'um_user' => $usr->getKey(),
                'um_mandate' => $mdt->getKey(),
            )) && $lnk->count() > 0) {
        return true;
    }
    
    $subAdd = array(
        'um_user' => $usr->getKey(),
        'um_mandate' => $mdt->getKey(),
    );
    
    if ($cotis != null) {
        $subAdd['um_subscrib'] = $cotis;
    }

    $succ = $lnk->addFrom($subAdd);

    if ($succ && (aclFromText($usr->raw_user_role) < ACL_USER)) {
        $usr->user_role = ACL_USER;
    }

    return $succ;
}

function user_check() {
    global $tpl;

    $mdt = new Modele('mandate');
    $mdt->find(false, 'mandate_end DESC');

    $tpl->assign('mandates', array());

    if (isset($_POST['idfiche'])) {
        $tpl->assign('hsuccess', user_add_mandate($_POST['idfiche'], $_POST['mandate']));
    }

    while ($l = $mdt->next()) {
        $tpl->append('mandates', $l);
    }

    display();
}

function user_editpassword() {
    global $tpl, $config;

    if ($config['cms']['saml']) {
        throw new Exception('SAML is activated. Legacy system not available.');
    }

    $pass = $_POST['password'];
    $confirm = $_POST['password2'];
    $user = $_GET['user'];

    if ($pass != $confirm) {
        $tpl->assign('hsuccess', false);
    } else {
        $mdl = new Modele('users');
        $mdl->fetch($user);

        $rslt = $mdl->modFrom(array(
            'user_pass' => md5($mdl->user_name . ':' . $pass),
                ), false);

        $tpl->assign('hsuccess', $rslt);

        modexec('user', 'view');
    }
}

function user_viewphoto() {
    $usr = new Modele('users');
    $usr->fetch($_GET['user']);

    header('Content-Type: image/png');
    readfile($usr->user_photo);
    quit();
}

function user_removeGroup() {
    global $srcdir;

    $usr = new Modele('users');
    $usr->fetch($_GET['user']);

    require_once $srcdir . '/libs/GoogleApi.php';
    $api = new GoogleApi();
    $ret = $api->delGroupMember($_GET['group'], $usr->user_email);
    redirect("user", "view", array('user' => $usr->user_id, 'hsuccess' => isset($ret->error) ? 0 : 1));
}

function user_addGroup() {
    global $srcdir;

    $usr = new Modele('users');
    $usr->fetch($_GET['user']);

    require_once $srcdir . '/libs/GoogleApi.php';
    $api = new GoogleApi();
    $ret = $api->addGroupMember($_POST['group'], $usr->user_email);
    redirect("user", "view", array('user' => $usr->user_id, 'hsuccess' => isset($ret->error) ? 0 : 1));
}

function user_setcompta() {
    $usr = new Modele('users');
    $usr->fetch($_GET['user']);

    if ($_GET['account'] == 0) {
        $usr->user_compta = 0;
        redirect("user", "view", array('hsuccess' => 1, 'user' => $usr->getKey()));
    }

    $mdlAcc = new Modele('user_accounts');
    $mdlAcc->fetch($_GET['account']);

    if ($mdlAcc->raw_ua_user == $usr->getKey()) {
        $usr->user_compta = $mdlAcc->getKey();
        redirect("user", "view", array('hsuccess' => 1, 'user' => $usr->getKey()));
    }

    redirect("user", "view", array('hsuccess' => 0, 'user' => $usr->getKey()));
}

function user_remove_old() {
    global $pdo;

    $subsql = 'SELECT count(*) '
            . 'FROM user_mandate '
            . 'LEFT JOIN mandate ON um_mandate = mandate_id '
            . 'WHERE um_user = user_id AND mandate_end > now()';

    $sql = 'UPDATE `users` '
            . 'SET user_role = "GUEST" '
            . 'WHERE user_role = "USER" AND (' . $subsql . ') = 0';

    $result = $pdo->exec($sql);
    redirect('user', 'index', array('hsuccess' => $result !== false));
}

function user_pdfex() {
    if (!isset($_REQUEST['mandate'])) {
        redirect('user', 'index', array('hsuccess' => 0));
    }

    global $srcdir, $pdo;

    $mdt = new Modele('mandate');
    $mdt->fetch($_REQUEST['mandate']);

    include_once $srcdir . DS . 'libs' . DS . 'fpdf' . DS . 'fpdf.php';
    include_once $srcdir . DS . 'libs' . DS . 'fpdf' . DS . 'makefont' . DS . 'makefont.php';
    include_once $srcdir . DS . 'libs' . DS . 'userPdfEx.php';

    $users = $pdo->prepare('SELECT `users`.* FROM user_mandate LEFT JOIN users ON user_id = um_user WHERE user_status != "DELETE" AND um_mandate = ? ORDER BY user_lastname, user_firstname');
    $users->bindValue(1, $mdt->getKey());
    $users->execute();

    $pdf = new UserPdfEx($mdt->mandate_label, $_SESSION['user']['user_name'], $users->fetchAll(PDO::FETCH_ASSOC));
    $pdf->mktable();
    $pdf->out();

    quit();
}

function user_print() {
    $acl = new Modele('logaudit');
    $acl->addFrom([
        'la_user' => $_GET['user'],
        'la_date' => date('Y-m-d H:i:s'),
        'la_type' => 'ACCEPT',
        'la_ip' => '0.0.0.0',
    ]);
    modexec('index', 'print', $_GET['user'], $_POST['subscription']);
}

function user_edit() {
    global $tpl;

    $usr = new Modele('users');
    $usr->fetch($_GET['user']);
    $usr->assignTemplate('user');

    $fieldset = [
        'user_name',
        'user_firstname',
        'user_lastname',
        'user_type',
        'user_login',
        'user_promo',
        'user_email',
        'user_phone',
        'user_address',
        'user_cp',
        'user_town',
        'user_sexe',
        'user_born',
        'user_role',
    ];

    if (isset($_POST['user_name'])) {
        redirect('user', 'view', ['user' => $usr->getKey(), 'hsuccess' => $usr->modFrom($_POST, $fieldset) ? '1' : '0']);
    }

    $tpl->assign('fieldset', $fieldset);
    display();
}