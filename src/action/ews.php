<?php

/*
 * Event Web Site
 *
 * Administration des sous-sites pour les events.
 */

function ews_adm_list() {
    $mdl = new Modele('ews');
    $mdl->find();
    $mdl->appendTemplate('sites');
    display();
}

function ews_adm_del() {
    $mdl = new Modele('ews');
    $mdl->fetch($_REQUEST['id']);
    redirect("ews", "adm_list", array('hsuccess' => $mdl->delete() ? 1 : 0));
}

function ews_adm_add() {
    global $tpl;

    $mdl = new Modele('ews');

    $fields = array(
        'ua_identifier',
        'ua_number',
    );

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($mdl->addFrom($_POST)) {
            redirect("ews", "adm_list", array('hsuccess' => 1));
        } else {
            $tpl->assign('hsuccess', false);
        }
    }

    $tpl->assign('form', $mdl->edit());
    display();
}
