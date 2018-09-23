<?php

function _rgpd_remove($userid) {
    $mdl = new Modele('users');
    $mdl->fetch($userid);
    
    $mdl->modFrom(array(
        'user_firstname' => '',
        'user_lastname' => '',
        'user_email' => '',
        'user_login' => NULL,
        'user_phone' => NULL,
        'user_address' => NULL,
        'user_cp' => NULL,
        'user_town' => NULL,
        'user_born' => NULL,
        'user_photo' => NULL,
        'user_role' => 'GUEST',
    ), true);
}

function rgpd_execute() {
    global $pdo, $config, $tpl;
    
    $users = $pdo->prepare("SELECT user_id, user_name, la_date"
            . " FROM users"
            . " LEFT JOIN logaudit"
            . " ON la_id = (SELECT la_id FROM logaudit WHERE la_user = user_id ORDER BY la_date DESC LIMIT 1)");
    $users->execute();
    
    $maxDate = new DateTime();
    $maxDate->sub(new DateInterval('P' . $config['cms']['rgpd_days'] . 'D'));
    
    while($user = $users->fetch(PDO::FETCH_ASSOC)) {
        if ($user['la_date'] == null) {
            $user['la_date'] = '2018-07-20 18:00:00'; //Date de mise en place RGPD
        }
        $user['la_date'] = new DateTime($user['la_date']);
        $user['remove'] = $maxDate > $user['la_date'];
        echo "$user[user_id], $user[user_name] = " . $user['la_date']->format('Y-m-d H:i:s') . " (" . ($user['remove']?'del':'skip') . ")<br />";
        if ($user['remove']) {
            _rgpd_remove($user['user_id']);
        }
    }
    quit();
}

function rgpd_index() {
    global $config, $tpl;
    
    $tpl->assign('nbjours', $config['cms']['rgpd_days']);
    display();
}