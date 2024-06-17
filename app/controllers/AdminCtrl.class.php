<?php

namespace app\controllers;

use core\App;
use core\Message;
use core\Utils;
use core\RoleUtils;
use core\SessionUtils;

class AdminCtrl {
    
    private $user_list;
    
    private function load_user_data() {
        return [
            'logged_user'    => SessionUtils::load('logged_user', true),
            'logged_user_id' => SessionUtils::load('logged_user_id', true)
        ];
    }

    private function assign_roles() {
        App::getSmarty()->assign('roleAdmin',    RoleUtils::inRole('admin'));
        App::getSmarty()->assign('roleClient',   RoleUtils::inRole('client'));
        App::getSmarty()->assign('roleEmployee', RoleUtils::inRole('employee'));
        App::getSmarty()->assign('roleManager',  RoleUtils::inRole('manager'));
    }    
    
    private function handle_error(PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        Utils::addErrorMessage('Wystąpił błąd podczas pobierania rekordów');
    }
    
    public function action_homeAdmin() {
        
        Utils::addErrorMessage('Brak formatki do dodawania użytkowników - administrator musi dodać ręcznie użytkownika do tabel users oraz user_privileges');
        
        $user_data = $this->load_user_data();
        
        /* main data */
        
        try {
            $this->user_list = App::getDB()->select(
                'users',
            [
                '[>]user_privileges' => ['users.user_id' => 'priv_user_id'],
                '[>]roles' => ['user_privileges.priv_rol_id' => 'rol_id']
            ], [
                'users.user_id',
                'users.user_login',
                'users.user_name_surname',
                'users.user_first_name',
                'users.user_phone',
                'roles.rol_name'
            ]);
        } catch (PDOException $e){
            $this->handle_error($e);
        }
        
        /* launching the site with variables */

        $data = array_merge($user_data, [
            'user_list'   => $this->user_list,
            'show_header' => true
        ]);
        
        App::getSmarty()->assign($data);
        
        $this->assign_roles();
        
        App::getSmarty()->display("HomeAdminView.tpl");
    }
    
}
