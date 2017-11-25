<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function admin_note_refuseactivity() {
    $part = new Modele('participations');
    $part->fetch($_GET['activity']);
    $part->part_status = 'REFUSED';
    $part->part_validation_date = date('Y-m-d');

    redirect('admin_note', 'index', array('hsuccess' => 1));
}

function admin_note_validactivity() {
    $part = new Modele('participations');
    $part->fetch($_GET['activity']);
    $part->part_status = 'ACCEPTED';
    $part->part_validation_date = date('Y-m-d');

    redirect('admin_note', 'index', array('hsuccess' => 1));
}

function admin_note_delactivity() {
    $part = new Modele('participations');
    $part->fetch($_GET['activity']);

    $staffs = new Modele('marks');
    $staffs->find(array('mark_participation' => $part->getKey()));
    while ($staffs->next()) {
        $staffs->delete();
    }
    $part->delete();

    redirect('admin_note', 'index', array('hsuccess' => 1));
}

function admin_note_index() {
    $mdl = new Modele('participations');
    $mdl->find(array('part_status' => 'SUBMITTED'));
    $mdl->appendTemplate('parts');

    display();
}

function admin_note_viewactivity() {
    global $tpl;

    $mdl = new Modele('participations');
    $mdl->fetch($_GET['activity']);

    $tpl->assign('activity', $mdl);

    $staffs = new Modele('marks');
    $staffs->find(array('mark_participation' => $mdl->getKey()));
    $staffs->appendTemplate('staffs');

    display();
}

function admin_note_mandate() {
    $mdl = new Modele('mandate');
    $mdl->find();
    $mdl->appendTemplate('mandates');
    display();
}

function admin_note_addmandate() {
    global $tpl;

    $mdl = new Modele('mandate');
    $mdl->assignTemplate('mandat');
    if (isset($_POST['edit'])) {
        if ($mdl->addFrom($_POST)) {
            redirect("admin_note", "mandate", array('hsuccess' => 1));
        }
        $tpl->assign('hsuccess', false);
    }
    display();
}

function admin_note_delmandate() {
    $mdl = new Modele('mandate');
    $mdl->fetch($_REQUEST['mandate']);
    if ($mdl->delete()) {
        redirect("admin_note", "mandate", array('hsuccess' => 1));
    } else {
        redirect("admin_note", "mandate", array('hsuccess' => 1));
    }
}

function admin_note_modmandate() {
    global $tpl;
    
    $mdl = new Modele('mandate');
    $mdl->fetch($_REQUEST['mandate']);

    if (isset($_POST['mandate_label'])) {
        if ($mdl->modFrom($_POST)) {
            redirect('admin_note', 'mandate', array('hsuccess' => 1));
        } else {
            $tpl->assign('hsuccess', false);
        }
    }
        $mdl->assignTemplate('mandate');
        display();
}

function admin_note_periods() {
    $mdl = new Modele('periods');
    $mdl->find(array("period_state" => "ACTIVE"));
    $mdl->appendTemplate('periods');
    display();
}

function admin_note_delperiod() {
    $prd = new Modele('periods', $_REQUEST['id']);
    $marks = new Modele('marks');
    $marks->find(array('mark_period' => $prd->getKey()));
    while ($marks->next()) {
        $marks->delete();
    }
    $prd->delete();
    redirect('admin_note', 'periods', array('hsuccess' => 1));
}

function admin_note_bulletin() {
    $mdl = new Modele('periods');
    $mdl->find("period_state = 'DRAFT' OR period_state = 'VALID' OR period_state = 'SENT'");
    $mdl->appendTemplate('periods');
    display();
}

function admin_note_addbulletin() {
    global $pdo, $root, $tpl;

    if (isset($_REQUEST['period'])) {
        $mdl = new Modele("periods");
        $mdl->fetch($_REQUEST['period']);
        $_REQUEST['generator'] = basename($_REQUEST['generator']);

        require $root . 'libs' . DS . 'bulletins' . DS . $_REQUEST['generator'] . DS . 'bulletin.php';
        bulletin_add($mdl->period_id);

        $mdl->period_state = 'DRAFT';
        $mdl->period_generator = $_REQUEST['generator'];
        redirect('admin_note', 'bulletin', array('hsuccess' => 1));
    }

    $mdl = new Modele("periods");
    $mdl->find(array("period_state" => "ACTIVE"));
    $mdl->appendTemplate('periods');

    foreach (scandir($root . 'libs' . DS . 'bulletins') as $generator) {
        if (is_dir($root . 'libs' . DS . 'bulletins' . DS . $generator) && $generator[0] != '.') {
            $tpl->append('generators', $generator);
        }
    }

    display();
}

function admin_note_viewbulletin() {
    global $pdo, $root;

    $mdl = new Modele("periods");
    $mdl->fetch($_GET['id']);
    $mdl->assignTemplate('bulletin');

    require $root . 'libs' . DS . 'bulletins' . DS . $mdl->period_generator . DS . 'bulletin.php';

    bulletin_view($_GET['id']);
    quit();
}

function admin_note_editbulletin() {
    global $pdo, $srcdir;

    $mdl = new Modele("periods");
    $mdl->fetch($_GET['id']);
    $mdl->assignTemplate('bulletin');

    require $srcdir . '/libs' . DS . 'bulletins' . DS . $mdl->period_generator . DS . 'bulletin.php';

    bulletin_edit($_GET['id']);
    quit();
}

function admin_note_validbulletin() {
    global $srcdir;

    $mdl = new Modele("periods");
    $mdl->fetch($_GET['id']);

    require $srcdir . '/libs' . DS . 'bulletins' . DS . $mdl->period_generator . DS . 'bulletin.php';

    bulletin_valid($mdl);

    //redirect("admin_note", "bulletin", array("hsuccess" => 1));
}

function admin_note_downbulletin() {
    global $pdo, $root;

    $mdl = new Modele("periods");
    $mdl->fetch($_GET['id']);
    $mdl->assignTemplate('bulletin');

    require $root . 'libs' . DS . 'bulletins' . DS . $mdl->period_generator . DS . 'bulletin.php';

    bulletin_download($_GET['id']);
    quit();
}

function admin_note_cotis() {
    $mdt = new Modele('mandate');
    $mdt->fetch($_GET['mandate']);
    
    $cotis = new Modele('subscription');
    $cotis->find(array(
        'subscription_mandate' => $mdt->getKey(),
    ));
    
    $cotis->appendTemplate('cotis');
    $mdt->assignTemplate('mandate');
    display();
}

function admin_note_addcotis() {
    global $tpl;
    
    $mdt = new Modele('mandate');
    $mdt->fetch($_GET['mandate']);
    $cotis = new Modele('subscription');
    $cotis->setFields(array(
        'subscription_label',
        'subscription_price'
    ));
    
    if (isset($_POST['subscription_label'])) {
        $data = array(
            'subscription_label' => $_POST['subscription_label'],
            'subscription_price' => $_POST['subscription_price'],
            'subscription_mandate' => $mdt->getKey(),
        );
        if ($cotis->addFrom($data, true)) {
            redirect('admin_note', 'cotis', array('hsuccess' => 1, 'mandate' => $mdt->getKey()));
        } else {
            $tpl->assign('hsuccess', false);
        }
    }

    $cotis->assignTemplate('cotis');
    
    $mdt->assignTemplate('mandate');
    display();    
}

function admin_note_delcotis() {
    $cot = new Modele('subscription');
    $cot->fetch($_GET['cotis']);
    
    $mdl = $cot->raw_subscription_mandate;
    $cot->delete();
    redirect('admin_note', 'cotis', array('mandate' => $mdl, 'hsuccess' => 1));
}