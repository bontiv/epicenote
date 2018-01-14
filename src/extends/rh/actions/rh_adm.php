<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function rh_adm_index () {
    $mdl = new Modele('rh_event');
    $mdl->find(array('re_event' => $_GET['event']));
    $mdl->assignTemplate('conf');
    
    display();
}

function rh_adm_activate() {
    $mdl = new Modele('rh_event');
    $mdl->addFrom(array(
        're_event' => $_GET['event'],
    ));
    
    redirect('rh_adm', 'index', array('event' => $_GET['event']));
}