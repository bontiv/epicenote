<?php

/**
 * Controleurs du module administration
 * Ce module permet la gestion des droits d'accès. Son droit d'accès est forcé à "Admin" par le framework, il ne peut pas être édité lui même.
 * @package Epicenote
 */

/**
 * Controleur page d'index admin
 * Permet l'affichage de la page d'administration des droits d'accès.
 */
function admin_index() {
    global $pdo, $tpl;

    $sql = $pdo->prepare('SELECT * FROM acces WHERE acl_action != "index" AND acl_action != "admin" ORDER BY acl_action ASC, acl_page ASC');
    $sql->execute();
    $conf = array();
    while ($line = $sql->fetch()) {
        if (!isset($conf[$line['acl_action']]))
            $conf[$line['acl_action']] = array();
        $conf[$line['acl_action']][] = $line;
    }

    $groups = new Modele('sections');
    $groups->find();
    while ($groups->next()) {
        $tpl->append('grps', $groups->toArray());
    }

    $aclGrps = new Modele('access_groups');
    $aclGrps->find();
    $aclGrpsRslt = array();
    while ($aclGrps->next()) {
        if (!isset($aclGrpsRslt[$aclGrps->raw_ag_access])) {
            $aclGrpsRslt[$aclGrps->raw_ag_access] = array();
        }
        $aclGrpsRslt[$aclGrps->raw_ag_access][] = $aclGrps->raw_ag_group;
    }

    $tpl->assign('aclGrps', $aclGrpsRslt);
    $tpl->assign('acls', $conf);
    $tpl->display('admin_index.tpl');
    quit();
}

/**
 * Controleur mise à jour des droits
 * Ce controleur est appelé quand on valide la page avec les droits d'accès. Il permet d'enregistrer les nouveaux droits d'accès.
 */
function admin_update() {
    global $pdo;

    $sql = $pdo->prepare('SELECT * FROM acces');
    $sql->execute();
    while ($line = $sql->fetch()) {
        if (isset($_POST['acl' . $line['acl_id']])) {
            $update = $pdo->prepare('UPDATE acces SET acl_acces = ? WHERE acl_id = ?');
            $update->bindValue(1, $_POST['acl' . $line['acl_id']]);
            $update->bindValue(2, $line['acl_id']);
            $update->execute();
        }

        $grpDel = $pdo->prepare('DELETE FROM access_groups WHERE ag_acces = ?');
        $grpDel->bindValue(1, $line['acl_id']);
        $grpDel->execute();

        if (isset($_POST['groups' . $line['acl_id']])) {

            $nbRow = count($_POST['groups' . $line['acl_id']]);
            $sqlGrpQuery = 'INSERT INTO access_groups (ag_access, ag_group) VALUES ';

            $parts = array();
            for ($i = 0; $i < $nbRow; $i++) {
                $parts[] = '(?, ?)';
            }

            $sqlGrpQuery .= implode(', ', $parts);
            $sqlGrp = $pdo->prepare($sqlGrpQuery);
            $i = 1;

            foreach ($_POST['groups' . $line['acl_id']] as $grp) {
                $sqlGrp->bindValue($i++, $line['acl_id']);
                $sqlGrp->bindValue($i++, $grp);
            }
            $sqlGrp->execute();
        }
    }

    redirect('admin');
}

function admin_list() {
    $list = new Modele('users');
    $list->find(array(
        'user_role' => 'ADMINISTRATOR',
    ));
    $list->appendTemplate('admins');
    display();
}

function admin_autocomplete() {
    global $pdo;

    $sql = $pdo->prepare("SELECT * FROM users WHERE user_role != 'ADMINISTRATOR'"
            . " AND (user_firstname LIKE :term OR user_lastname LIKE :term OR user_name LIKE :term) ORDER BY user_name ASC");
    $sql->bindValue(':term', '%' . $_REQUEST['term'] . '%');
    $sql->execute();
    echo '[';
    $first = true;
    while ($line = $sql->fetch()) {
        if ($first) {
            $first = false;
        } else {
            echo ',';
        }
        echo json_encode(array(
            'label' => "$line[user_firstname] $line[user_lastname] ($line[user_name])",
            'value' => $line['user_name']
        ));
    }
    echo ']';
    quit();
}

function admin_add() {
    $usr = new Modele('users');
    $usr->find(array('user_name' => $_REQUEST['addadmin']));
    if ($usr->next()) {
        $usr->modFrom(array(
            'user_role' => 'ADMINISTRATOR',
        ));
        redirect('admin', 'list', array('hsuccess' => 1));
    } else {
        redirect('admin', 'list', array('hsuccess' => 0));
    }
}

function admin_remove() {
    global $pdo;
    
    $usr = new Modele('users');
    $usr->fetch($_REQUEST['user']);
    
    $sql = $pdo->prepare('SELECT count(*) FROM user_mandate LEFT JOIN mandate ON um_mandate = mandate_id WHERE mandate_end > NOW() AND um_user = :user');
    $sql->bindValue(':user', $usr->getKey());
    $sql->execute();
    $nbMdt = $sql->fetch();
    
    $usr->modFrom(array(
        'user_role' => $nbMdt > 0 ? 'USER' : 'CPLUSER',
    ));
    
    redirect('admin', 'list', array('hsuccess' => 1));
}