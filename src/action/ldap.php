<?php

use LdapRecord\Connection;
use LdapRecord\Container;
use LdapRecord\Models\OpenLDAP;

/**
 * Load LDAP connection
 */
function ldap_autoload() {
    global $config;
    $conf = $config['ldap'];

    $url = parse_url($conf['ldap_uri']);

    $connect = new Connection([
        'hosts' => [$url['host']],
        'base_dn' => trim($url['path'], '/ '),
        'username' => $conf['ldap_binddn'],
        'password' => $conf['ldap_password'],

        'port' => isset($url['port']) ? $url['port'] : ($url['scheme'] == 'ldaps' ? 636 : 389),
        'use_ssl' => $url['scheme'] == 'ldap' ? false : true,
    ]);

    try {
        $connect->connect();
        Container::addConnection($connect, 'Epitanime');
        Container::setDefaultConnection('Epitanime');
        echo 'Connection success';
    } catch (\LdapRecord\Auth\BindException $e) {
        var_dump($e, $connect, $url);
        quit();
    } catch (\LdapRecord\ConnectionException $e) {
        var_dump($e, $connect, $url);
        quit();
    }
}

function ldap_setuplevels(){
    global $config;

    $level1 = new LdapRecord\Models\OpenLDAP\Group();
    $level1->cn = 'Default';
    $level1->inside($config['ldap']['ldap_levelbase']);
    var_dump($level1);
    $level1->save();

    $groups = OpenLDAP\Group::query()->in($config['ldap']['ldap_levelbase'])->get();
    var_dump($groups);

}

function ldap_index() {
    echo "Coucou";
    quit();
}

function ldap_syncusers() {
    $users = new Modele('users');
    $users->find();

    while ($users->next()) {
        _ldap_adduser($users);
    }
    quit();
}

function ldap_syncuser() {
    $users = new Modele('users');
    $users->fetch($_GET['userid']);
    _ldap_adduser($users);
    quit();
}

function _ldap_addusergroup(OpenLDAP\User $user, $group) {
    global $config;

    $ldap_group = OpenLDAP\Group::query()->findBy('cn', $group);
    if ($ldap_group === null) {
        $ldap_group = new OpenLDAP\Group();
        $ldap_group->cn = $group;
        $ldap_group->inside($config['ldap']['ldap_groupbase']);
        $ldap_group->uniqueMember = $user->getDn();
    }

    $ldap_group->save();
}

function _ldap_delusergroup(OpenLDAP\User $user, LdapRecord\Models\Model $group) {
    $relation = $user->hasMany(OpenLDAP\Group::class, 'uniqueMember');

    if (!$relation->exists($group)) {
        return;
    }

    if (count($group->uniqueMember) == 1) {
        $group->delete();
    } else {
        $relation->detach($group);
    }
}

function _ldap_setgroups(OpenLDAP\User $user, $groups) {
    foreach ($groups as $group) {
        _ldap_addusergroup($user, $group);
    }

    $ldap_groups = $user->hasMany(OpenLDAP\Group::class, 'uniqueMember');
    foreach ($ldap_groups->get() as $group) {
        if (!in_array($group->cn[0], $groups)) {
            _ldap_delusergroup($user, $group);
        }
    }
}

function _ldap_adduser(Modele $user) {
    global $config;

    $ldap_user = OpenLDAP\User::query()->in($config['ldap']['ldap_userbase'])->findBy('cn', $user->user_id);
    if ($ldap_user === null) {
        $ldap_user = new OpenLDAP\User();
        $ldap_user->inside($config['ldap']['ldap_userbase']);
        $ldap_user->cn = $user->user_id;
        if (strpos($user->user_pass, '{CRYPT}') !== false) {
            $ldap_user->userPassword = $user->user_pass;
        }
    }

    $ldap_user->uid = $user->user_name;
    $ldap_user->displayName = $user->user_name;
    $ldap_user->sn = strlen($user->user_lastname) > 0 ? $user->user_lastname : $user->user_name;
    if (strlen($user->user_firstname) > 0)
        $ldap_user->givenName = $user->user_firstname;
    if (strlen($user->user_email) > 0)
        $ldap_user->mail = $user->user_email;

    $ldap_user->save();

    $groups = [$user->raw_user_role];
    $intra_group = $user->reverse('user_sections');
    while ($intra_group->next()) {
        $groups[] = $intra_group->us_section->section_name;
    }

    _ldap_setgroups($ldap_user, $groups);
}
