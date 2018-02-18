<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function rh_adm_autoload() {
    $mdl = new Modele('rh_event');
    $mdl->find(array('re_event' => $_GET['event']));
    if ($mdl->next()) {
        $mdl->assignTemplate('conf');
    }
    
}

function rh_adm_index () {
    display();
}

function rh_adm_activate() {
    $mdl = new Modele('rh_event');
    $mdl->addFrom(array(
        're_event' => $_GET['event'],
    ));
    
    redirect('rh_adm', 'index', array('event' => $_GET['event']));
}

function rh_adm_form() {
    display();
}

function rh_adm_addelmt() {
    redirect('rh_adm', 'form', array('event' => $_GET['event']));
}