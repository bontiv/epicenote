<?php

/**
 * Controleur sur la gestion des sections
 * Ce controleur contient toutes les pages utilisés pour gérer les sections.
 * @package Epicenote
 */

/**
 * Défini le mode superuser
 */
function section_security($page, $params) {
    $mdl = new Modele('user_sections');

    if (!$_SESSION['user'] || !isset($params['section']))
        return false;

    $mdl->find(array(
        'us_user' => $_SESSION['user']['user_id'],
        'us_section' => $params['section'],
        'us_type' => 'manager'
    ));
    if ($mdl->count()) {
        return ACL_SUPERUSER;
    }
    
    $sec = new Modele('sections');
    $sec->fetch($params['section']);
    if ($sec->raw_section_type == 'secondary') {
        $params['section'] = $sec->raw_section_parent;
        return section_security($page, $params);
    }
    
    return false;
}

/**
 * Permet de créer un événement
 * Ce controleur permet de créer un événement à partir d'une section. On y accède à partir de la fiche de la section qui va gérer le déroulement de l'événement.
 */
function section_mkevent() {
    global $pdo, $tpl;

    $tpl->assign('error', false);
    $tpl->assign('succes', false);
    $tpl->assign('section', $_GET['section']);

    if (isset($_POST['event_name'])) {
        $dateStart = new DateTime($_POST['event_start']);
        $dateStart->setTime($_POST['event_start_hours'], $_POST['event_start_mins'], 0);
        $dateEnd = new DateTime($_POST['event_end']);
        $dateEnd->setTime($_POST['event_end_hours'], $_POST['event_end_mins'], 0);
        $sevenDays = new DateInterval('P7D');
        $dateLock = new DateTime($dateStart->format('Y-m-d H:i:s'));
        $dateLock->sub($sevenDays);
        $dateNote1 = new DateTime($dateEnd->format('Y-m-d H:i:s'));
        $dateNote1->add($sevenDays);
        $dateNote2 = new DateTime($dateNote1->format('Y-m-d H:i:s'));
        $dateNote2->add($sevenDays);

        unset($_REQUEST['event_start_hours'], $_REQUEST['event_start_mins'], $_REQUEST['event_end_hours'], $_REQUEST['event_end_mins']);

        $extra = array(
            'event_start' => $dateStart->format('Y-m-d H:i:s'),
            'event_end' => $dateEnd->format('Y-m-d H:i:s'),
            'event_lock' => $dateLock->format('Y-m-d H:i:s'),
            'event_note1' => $dateNote1->format('Y-m-d H:i:s'),
            'event_note2' => $dateNote2->format('Y-m-d H:i:s'),
            'event_coef' => 1,
            'event_section' => $_GET['section'],
            'event_owner' => $_SESSION['user']['user_id'],
        );

        if (autoInsert('events', 'event_', $extra))
            $tpl->assign('succes', true);
        else
            $tpl->assign('error', true);
    }

    $tpl->display('section_mkevent.tpl');
    quit();
}

/**
 * Liste toutes les sections
 */
function section_index() {
    global $pdo, $tpl;

    $sql = $pdo->prepare('SELECT * FROM sections LEFT JOIN users ON user_id = section_owner WHERE section_type = "primary" ORDER BY section_name');
    $sql->execute();
    while ($line = $sql->fetch()) {

        $line['inType'] = isset($_SESSION['user']['sections'][$line['section_id']]) ? $_SESSION['user']['sections'][$line['section_id']]['us_type'] : false;
        $subsql = $pdo->prepare('SELECT * FROM user_sections NATURAL JOIN users WHERE section_id = ? AND us_type = \'manager\'');
        $subsql->bindValue(1, $line['section_id']);
        $subsql->execute();
        $managers = array();
        while ($subline = $subsql->fetch())
            $managers[] = $subline;
        $line['managers'] = $managers;
        $subgrp = $pdo->prepare("SELECT * FROM sections WHERE section_parent = ?");
        $subgrp->bindValue(1, $line['section_id']);
        $subgrp->execute();
        $line['subgrps'] = array();
        while ($linegrp = $subgrp->fetch()) {
            $line['subgrps'][] = $linegrp;
        }
        $tpl->append('sections', $line);
    }

    $tpl->display('section_index.tpl');
    quit();
}

/**
 * Ajoute une section
 */
function section_add() {
    global $pdo, $tpl;

    $tpl->assign('error', false);
    $tpl->assign('succes', false);

    if (isset($_POST['section_name'])) {
        if (autoInsert('sections', 'section_', array(
                    'section_owner' => $_SESSION['user']['user_id'],
                ))) {
            $tpl->assign('succes', true);
        } else
            $tpl->assign('error', $pdo->errorInfo());
    }


    $tpl->display('section_add.tpl');
    quit();
}

/**
 * Supprime une section
 */
function section_delete() {
    global $pdo;

    $sql = $pdo->prepare('DELETE FROM sections WHERE section_id = ?');
    $sql->bindValue(1, $_GET['section']);
    if ($sql->execute())
        redirect('section');
    else
        modexec('syscore', 'sqlerror');
}

/**
 * Affiche les détails d'une section
 * Les détails d'une section c'est aussi la liste des membres de la section avec la gestion des membres.
 * NB: C'est aussi d'ici qu'on créer un événement.
 */
function section_details() {
    global $pdo, $tpl;

    $tpl->assign('managers', array());
    $tpl->assign('users', array());
    $tpl->assign('guests', array());

    $section = new Modele('sections');
    $section->fetch($_REQUEST['section']);
    $tpl->assign('section', $section);


    $sql = $pdo->prepare('SELECT * FROM user_sections LEFT JOIN users ON user_id = us_user WHERE us_section = ? AND us_type="manager" AND user_status != "DELETE"');
    $sql->bindValue(1, $section->section_id);
    $sql->execute();
    while ($line = $sql->fetch())
        $tpl->append('managers', $line);

    $sql = $pdo->prepare('SELECT * FROM user_sections LEFT JOIN users ON user_id = us_user WHERE us_section = ? AND us_type="user" AND user_status != "DELETE"');
    $sql->bindValue(1, $section->section_id);
    $sql->execute();
    while ($line = $sql->fetch())
        $tpl->append('users', $line);

    $sql = $pdo->prepare('SELECT * FROM user_sections LEFT JOIN users ON user_id = us_user WHERE us_section = ? AND us_type="guest"');
    $sql->bindValue(1, $section->section_id);
    $sql->execute();
    while ($line = $sql->fetch())
        $tpl->append('guests', $line);

    $tpl->display('section_details.tpl');
    quit();
}

/**
 * Je veux rentrer dans la section
 * Ce controleur permet à l'utilisateur actuellement connecter de faire une demande d'adhésion à une section.
 */
function section_goin() {
    global $pdo;

    $sql = $pdo->prepare('INSERT INTO user_sections (us_user, us_section, us_type) VALUES (?, ?, "guest")');
    $sql->bindValue(1, $_SESSION['user']['user_id']);
    $sql->bindValue(2, $_GET['section']);
    $sql->execute();
    redirect('section');
}

/**
 * Je ne veux plus de cette section
 * Cette fonction permet à l'utilisateur connecté de quitter la section.
 */
function section_goout() {
    global $pdo;

    $sql = $pdo->prepare('DELETE FROM user_sections WHERE us_user = ? AND us_section = ?');
    $sql->bindValue(1, $_SESSION['user']['user_id']);
    $sql->bindValue(2, $_GET['section']);
    $sql->execute();
    redirect('section');
}

/**
 * Ho Oui ! Un staff
 * Permet d'accepter un membre dans sa section.
 * @global type $pdo
 */
function section_accept() {
    global $pdo;

    $sql = $pdo->prepare('UPDATE user_sections SET us_type = "user" WHERE us_user = ? AND us_section = ?');
    $sql->bindValue(1, $_GET['user']);
    $sql->bindValue(2, $_GET['section']);
    $sql->execute();
    redirect('section', 'details', array('section' => $_GET['section']));
}

/**
 * Bye le membre ...
 * Permet de retirer un membre d'une section.
 * @global type $pdo
 */
function section_reject() {
    global $pdo;

    $sql = $pdo->prepare('UPDATE user_sections SET us_type = "rejected" WHERE us_user = ? AND us_section = ?');
    $sql->bindValue(1, $_GET['user']);
    $sql->bindValue(2, $_GET['section']);
    $sql->execute();
    redirect('section', 'details', array('section' => $_GET['section']));
}

/**
 * Promotion manager
 * Et hop ! Un staff devient responsable de la section.
 * @global type $pdo
 */
function section_manager() {
    global $pdo;

    $sql = $pdo->prepare('UPDATE user_sections SET us_type = "manager" WHERE us_user = ? AND us_section = ?');
    $sql->bindValue(1, $_GET['user']);
    $sql->bindValue(2, $_GET['section']);
    $sql->execute();
    redirect('section', 'details', array('section' => $_GET['section']));
}

function section_edit() {
    global $tpl;

    $mdl = new Modele('sections');
    $mdl->fetch($_GET['section']);
    if (isset($_POST['postOK'])) {
        $tpl->assign('hsuccess', $mdl->modFrom($_POST));
    }
    $tpl->assign('section', $mdl);

    display();
}

function section_addpoints() {
    global $tpl, $pdo;

    $section = new Modele('sections');
    $section->fetch($_REQUEST['section']);
    $tpl->assign('section', $section);

    $queryFields = array(
        'part_duration',
        'part_title',
        'part_justification'
    );

    $mdl = new Modele('participations');
    $tpl->assign('form', $mdl->edit($queryFields));

    if (isset($_POST['edit'])) {
        $data = array(
            'part_section' => $section->section_id,
            'part_attribution_date' => date('Y-m-d'),
            'part_status' => 'SUBMITTED',
        );

        foreach ($queryFields as $field) {
            $data[$field] = $_POST[$field];
        }

        if (!$mdl->addFrom($data))
            redirect('section', 'details', array('section' => $section->section_id, 'hsuccess' => '0'));
        $sql = $pdo->prepare('SELECT * FROM user_sections LEFT JOIN users ON user_id = us_user WHERE us_section = ? ORDER BY user_name');
        $sql->bindValue(1, $section->section_id);
        $sql->execute();

        $mdlMark = new Modele('marks');
        $dataMark = array(
            'mark_participation' => $mdl->getKey(),
        );

        while ($user = $sql->fetch()) {
            if (in_array($user['user_id'], $_POST['staffs'])) {
                $dataMark['mark_user'] = $user['user_id'];
                $mdlMark->addFrom($dataMark);
            }
        }
        redirect('section', 'details', array('section' => $section->section_id, 'hsuccess' => '1'));
    }

    $sql = $pdo->prepare('SELECT * FROM user_sections LEFT JOIN users ON user_id = us_user WHERE us_section = ? ORDER BY user_name');
    $sql->bindValue(1, $section->section_id);
    $sql->execute();

    while ($user = $sql->fetch()) {
        $tpl->append('staffs', $user);
    }

    display();
}

function section_activities() {
    global $tpl;

    $section = new Modele('sections');
    $section->fetch($_REQUEST['section']);
    $tpl->assign('section', $section);

    $activites = new Modele('participations');
    $activites->find(array('part_section' => $section->section_id));
    while ($activites->next()) {
        $tpl->append('activities', new Modele($activites));
    }

    display();
}

function section_events() {
    global $tpl;

    $section = new Modele('sections');
    $section->fetch($_REQUEST['section']);
    $tpl->assign('section', $section);

    $events = new Modele('events');
    $events->find(array('event_section' => $section->section_id));
    while ($events->next()) {
        $tpl->append('events', new Modele($events));
    }

    display();
}

function section_viewactivity() {
    $section = new Modele('sections');
    $section->fetch($_REQUEST['section']);
    $section->assignTemplate('section');

    $mdl = new Modele('participations');
    $mdl->fetch($_REQUEST['activity']);
    if ($mdl->raw_part_section == $_REQUEST['section']) {
        $mdl->assignTemplate('part');
    } else {
        redirect('index');
    }
    display();
}

function section_mls() {
    global $tpl, $srcdir;

    include $srcdir . '/libs/GoogleApi.php';

    $api = new GoogleApi();

    $mdl = new Modele('sections');
    $mdl->fetch($_REQUEST['section']);
    $mdl->assignTemplate('section');

    if ($mdl->section_ml) {
        $grp = $api->getGroupsDetails($mdl->section_ml);
        $tpl->append('groups', array(
            'obj' => $grp,
            'isSection' => true,
        ));
    }

    $lnk = new Modele('section_ml');
    $lnk->find(array('sm_section' => $mdl->section_id));
    while ($lnk->next()) {
        $grp = $api->getGroupsDetails($lnk->sm_ml);
        $tpl->append('groups', array(
            'obj' => $grp,
            'isSection' => false,
        ));
    }

    display();
}

function section_admin_ml() {
    global $tpl, $srcdir, $pdo;

    include $srcdir . '/libs/GoogleApi.php';

    $api = new GoogleApi();

    $mdl = new Modele('sections');
    $mdl->fetch($_REQUEST['section']);
    $mdl->assignTemplate('section');

    $lnk = new Modele('section_ml');
    $lnk->find(array(
        'sm_section' => $_REQUEST['section'],
        'sm_ml' => $_REQUEST['ml'],
    ));
    if (!$lnk->next()) {
        modexec('syscore', 'forbidden');
    }

    $grp = $api->getGroupsDetails($lnk->sm_ml);
    $tpl->assign('group', $grp);

    $members = $api->getGroupMembers($grp->id);
    $usql = $pdo->prepare('SELECT * FROM users WHERE user_email = ?');

    foreach ($members->members as $member) {
        $usql->bindValue(1, $member->email);
        $usql->execute();
        $user = $usql->fetch();

        $tpl->append('members', array(
            'isSave' => strpos($member->email, 'save_') === 0,
            'user' => $user,
            'obj' => $member,
        ));
    }

    display();
}

function section_admin_ml_add() {
    global $tpl, $srcdir, $pdo;

    include $srcdir . '/libs/GoogleApi.php';

    $api = new GoogleApi();

    $mdl = new Modele('sections');
    $mdl->fetch($_REQUEST['section']);
    $mdl->assignTemplate('section');

    $lnk = new Modele('section_ml');
    $lnk->find(array(
        'sm_section' => $_REQUEST['section'],
        'sm_ml' => $_REQUEST['ml'],
    ));
    if (!$lnk->next()) {
        modexec('syscore', 'forbidden');
    }

    $api->addGroupMember($lnk->sm_ml, $_REQUEST['email']);

    redirect("section", "admin_ml", array(
        "hsuccess" => 1,
        "section" => $_REQUEST['section'],
        "ml" => $lnk->sm_ml,
    ));
}

function section_admin_ml_del() {
    global $tpl, $srcdir, $pdo;

    include $srcdir . '/libs/GoogleApi.php';

    $api = new GoogleApi();

    $mdl = new Modele('sections');
    $mdl->fetch($_REQUEST['section']);
    $mdl->assignTemplate('section');

    $lnk = new Modele('section_ml');
    $lnk->find(array(
        'sm_section' => $_REQUEST['section'],
        'sm_ml' => $_REQUEST['ml'],
    ));
    $mbr = $api->getGroupMemberDetails($_REQUEST['ml'], $_REQUEST['member']);
    if (!$lnk->next() || strpos($_REQUEST['member'], 'save_') === 0 || $mbr->type == "GROUP") {
        modexec('syscore', 'forbidden');
    }

    $api->delGroupMember($lnk->sm_ml, $_REQUEST['member']);

    redirect("section", "admin_ml", array(
        "hsuccess" => 1,
        "section" => $_REQUEST['section'],
        "ml" => $lnk->sm_ml,
    ));
}

function section_admin_ml_setadmin() {
    global $tpl, $srcdir, $pdo;

    include $srcdir . '/libs/GoogleApi.php';

    $api = new GoogleApi();

    $mdl = new Modele('sections');
    $mdl->fetch($_REQUEST['section']);
    $mdl->assignTemplate('section');

    $lnk = new Modele('section_ml');
    $lnk->find(array(
        'sm_section' => $_REQUEST['section'],
        'sm_ml' => $_REQUEST['ml'],
    ));
    $mbr = $api->getGroupMemberDetails($_REQUEST['ml'], $_REQUEST['member']);
    if (!$lnk->next() || strpos($_REQUEST['member'], 'save_') === 0 || $mbr->type == "GROUP") {
        modexec('syscore', 'forbidden');
    }

    $ret = $api->setGroupMemberLevel($lnk->sm_ml, $_REQUEST['member'], 'OWNER');

    redirect("section", "admin_ml", array(
        "hsuccess" => 1,
        "section" => $_REQUEST['section'],
        "ml" => $lnk->sm_ml,
    ));
}

function section_admin_ml_noadmin() {
    global $tpl, $srcdir, $pdo;

    include $srcdir . '/libs/GoogleApi.php';

    $api = new GoogleApi();

    $mdl = new Modele('sections');
    $mdl->fetch($_REQUEST['section']);
    $mdl->assignTemplate('section');

    $lnk = new Modele('section_ml');
    $lnk->find(array(
        'sm_section' => $_REQUEST['section'],
        'sm_ml' => $_REQUEST['ml'],
    ));
    $mbr = $api->getGroupMemberDetails($_REQUEST['ml'], $_REQUEST['member']);
    if (!$lnk->next() || strpos($_REQUEST['member'], 'save_') === 0 || $mbr->type == "GROUP") {
        modexec('syscore', 'forbidden');
    }

    $api->setGroupMemberLevel($lnk->sm_ml, $_REQUEST['member'], 'MEMBER');

    redirect("section", "admin_ml", array(
        "hsuccess" => 1,
        "section" => $_REQUEST['section'],
        "ml" => $lnk->sm_ml,
    ));
}

function section_send() {
    global $srcdir, $tpl;

    $mdl = new Modele('sections');
    $mdl->fetch($_REQUEST['section']);
    $mdl->assignTemplate('section');

    include $srcdir . '/libs/GoogleApi.php';

    $ml = new Modele('section_ml');
    $ml->find(array(
        'sm_section' => $_REQUEST['section'],
        'sm_ml' => $_REQUEST['from'],
    ));

    if ($ml->next()) {
        $mlid = $ml->sm_ml;
    } elseif ($mdl->section_ml != '') {
        $mlid = $mdl->section_ml;
    } else {
        modexec('syscore', 'no_object');
    }

    $api = new GoogleApi();
    $grp = $api->getGroupsDetails($mlid);
    $tpl->assign('mail', $grp);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $mailer = getMailer();
        $mailer->AddReplyTo($grp->email, $grp->name);
        $mailer->FromName = $grp->name;

        foreach (explode(';', $_POST['recipients']) as $receip) {
            $receip = trim($receip);
            if ($receip) {
                $mailer->AddAddress(trim($receip));
            }
        }

        $mailer->Subject = "[EPITANIME] $_POST[subject]";
        $mailer->Body = $_POST['ebody'];
        $tpl->assign('hsuccess', $mailer->Send());
    }

    display();
}

function section_trombi() {


    $mdl = new Modele('sections');
    $mdl->fetch($_REQUEST['section']);
    $mdl->assignTemplate('section');

    $usr = new Modele('user_sections');

    // Managers
    $usr->find(array(
        'us_section' => $mdl->section_id,
        'us_type' => 'manager',
        'user_status != "DELETE"',
    ));
    $usr->appendTemplate('managers');

    //Staffs
    $usr->find(array(
        'us_section' => $mdl->section_id,
        'us_type' => 'user',
    ));
    $usr->appendTemplate('users');

    display();
}

function section_teams() {
    global $pdo, $tpl;
    
    $mdl = new Modele('sections');
    $mdl->fetch($_REQUEST['section']);
    $mdl->assignTemplate('section');


    $sql = $pdo->prepare('SELECT * FROM sections LEFT JOIN users ON user_id = section_owner WHERE section_parent = ? ORDER BY section_name');
    $sql->bindValue(1, $_GET['section']);
    $sql->execute();
    while ($line = $sql->fetch()) {

        $line['inType'] = isset($_SESSION['user']['sections'][$line['section_id']]) ? $_SESSION['user']['sections'][$line['section_id']]['us_type'] : false;
        $subsql = $pdo->prepare('SELECT * FROM user_sections NATURAL JOIN users WHERE section_id = ? AND us_type = \'manager\'');
        $subsql->bindValue(1, $line['section_id']);
        $subsql->execute();
        $managers = array();
        while ($subline = $subsql->fetch())
            $managers[] = $subline;
        $line['managers'] = $managers;
        $tpl->append('teams', $line);
    }
    
    display();
}

function section_team_add() {
    $mdl = new Modele('sections');
    $mdl->fetch($_REQUEST['section']);
    $mdl->assignTemplate('section');

    $teams = new Modele('sections');
    $teams->setFields(array(
        'section_name'
    ));
    if (isset($_POST['section_name'])) {
        $team = $teams->addFrom(array(
            'section_name' => $_POST['section_name'],
            'section_owner' => $_SESSION['user']['user_id'],
            'section_parent' => $_GET['section'],
            'section_type' => 'secondary',
        ), true);
        if ($team) {
            redirect('section', 'teams', array('section' => $_GET['section'], 'hsuccess' => 1));
        }
    }
    $teams->assignTemplate('team');
    
    display();
}

function section_acl() {
    global $pdo, $tpl;
    
    $mdl = new Modele('sections');
    $mdl->fetch($_REQUEST['section']);
    $mdl->assignTemplate('section');

    $acl = $pdo->prepare(
            'SELECT * FROM acces '
            . 'WHERE acl_action != "index"'
            . 'AND acl_action != "admin" '
            . 'AND acl_acces = "SUPERUSER" '
            . 'OR (SELECT COUNT(*) FROM access_groups WHERE ag_access = acl_id AND ag_group = :group) > 1 '
            . 'ORDER BY acl_action ASC, acl_page ASC');
    $acl->bindValue('group', $mdl->getKey());
    $acl->execute();
    
    $agrp = $pdo->prepare(
            'SELECT ag_access FROM access_groups WHERE ag_group = :group');
    $agrp->bindValue('group', $mdl->getKey());
    $agrp->execute();
    $grps = $agrp->fetchAll(PDO::FETCH_COLUMN, 0);
    
    $acls = array();
    while($line = $acl->fetch()) {
        $action = $line['acl_action'];
        $aclid = $line['acl_id'];
        
        if (!isset($acls[$action])) {
            $acls[$action] = array();
        }
        $acls[$action][$aclid] = $line;
        $acls[$action][$aclid]['acl_enable'] = in_array($aclid, $grps);
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $newAcl = isset($_POST['acl-' . $aclid]) && $_POST['acl-' . $aclid];
            if ($newAcl != $acls[$action][$aclid]['acl_enable']) {
                if ($newAcl) {
                    $upd = $pdo->prepare('INSERT INTO access_groups (ag_access, ag_group) VALUES (:access, :group)');
                } else {
                    $upd = $pdo->prepare('DELETE FROM access_groups WHERE ag_access = :access AND ag_group = :group');
                }
                $upd->bindValue('access', $aclid);
                $upd->bindValue('group', $mdl->getKey());
                $upd->execute();
                $acls[$action][$aclid]['acl_enable'] = $newAcl;
            }
            $tpl->assign('hsuccess', true);
        }
    }
    $tpl->assign('acls', $acls);
    display();
}

function section_staff_add() {
    global $pdo;

    // Autocomplete
    if (isset($_GET['format']) && $_GET['format'] == 'json') {
        $sql = $pdo->prepare("SELECT user_name, user_firstname, user_lastname FROM users WHERE user_name LIKE :term OR user_firstname LIKE :term OR user_lastname LIKE :term ORDER BY user_name ASC LIMIT 10");
        $sql->bindValue('term', "%$_GET[term]%");
        $sql->execute();

        echo json_encode($sql->fetchAll(PDO::FETCH_ASSOC));
        quit();
    }
    
    if (isset($_POST['login'])) {
        $mdl = new Modele('user_sections');
        $usr = $pdo->prepare('SELECT user_id FROM users WHERE user_name = ?');
        foreach (explode(',', $_POST['login']) as $login) {
            $usr->bindValue(1, trim($login));
            $usr->execute();
            $usrDetails = $usr->fetch();
            if ($usrDetails !== false) {
                $mdl->find(array(
                    'us_user' => $usrDetails['user_id'],
                    'us_section' => $_REQUEST['section'],
                ));
                if ($mdl->next()) {
                    $mdl->us_type = 'user';
                } else {
                    $mdl->addFrom(array(
                        'us_user' => $usrDetails['user_id'],
                        'us_section' => $_REQUEST['section'],
                        'us_type' => 'manager',
                    ));
                }
            }
        }
        redirect('section', 'details', array('section' => $_REQUEST['section'], 'hsuccess' => 1));
    }
}