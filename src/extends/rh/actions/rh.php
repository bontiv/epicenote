<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function rh_autoload() {
    global $pdo, $tpl;
    
    $sql = $pdo->prepare('SELECT * FROM events LEFT JOIN users ON event_owner = user_id LEFT JOIN sections ON section_id = event_section WHERE event_id = ?');
    $sql->bindValue(1, $_GET['event']);
    $sql->execute();

    $event = $sql->fetch();
    if (!$event)
        modexec('syscore', 'notfound');
    $tpl->assign('event', $event);
}

function rh_index() {
    display();
}