<?php

namespace app\controllers;

use core\App;
use core\Utils;
use core\SessionUtils;
use core\RoleUtils;
use core\ParamUtils;
use app\forms\LoginForm;

class LoginCtrl {
    
    private $form;

    public function __construct() { // INFO: required objects
        $this->form = new LoginForm();
    }

    public function action_logout() {
        $logout_success = session_destroy();
        
        $status = $logout_success ? 'logged_out' : 'logout_failed';
        
        App::getRouter()->redirectTo("home?status={$status}");
    }
        
    public function validate_pass() {
        // DEBUG: Utils::addInfoMessage('3.1. in validate_pass()');
        $user_pass = App::getDB()->select("users", ["user_password"], ["user_login" => $this->form->login]);
        // DEBUG: Utils::addInfoMessage('3.2. Password from the DB: '.$user_pass[0]['user_password']);

        if (isset($user_pass[0]['user_password'])) {
            $user_pass_hash = password_hash($user_pass[0]['user_password'], PASSWORD_DEFAULT);
            
            return password_verify($this->form->pass, $user_pass_hash);
        }
        
        return false;
    }

    public function validate() {
        // DEBUG: Utils::addInfoMessage('2.1. in validate()');
        
        $this->form->login = ParamUtils::getFromRequest('login');
        $this->form->pass = ParamUtils::getFromRequest('pass');
        
        if (!isset($this->form->login)) {
            return false;
        }

        if (empty($this->form->login)) {
            Utils::addErrorMessage('Nie podano loginu');
        }
        if (empty($this->form->pass)) {
            Utils::addErrorMessage('Nie podano hasła');
        }
        
        if (App::getMessages()->isError()) {
            return false;
        }

        /* password validation */
        
        if (!$this->validate_pass()) {
            Utils::addErrorMessage('Niepoprawny login lub hasło');
            // DEBUG: Utils::addErrorMessage('Used login: '.$this->form->login);
            // DEBUG: Utils::addErrorMessage('Used password: '.$this->form->pass);
        } else {
            // DEBUG: Utils::addInfoMessage('2.2. validate() passed');
            return true;
        }
        
        //Utils::addErrorMessage('2.3. validation failed');
        return !App::getMessages()->isError();
    }

    private function setUserDataAndRedirect() {
        
        /* user name */
        
        $user = App::getDB()->select("users", ["user_first_name"], ["user_login" => $this->form->login]);
        if ($user !== null && isset($user[0])) {
            $logged_user = $user[0]['user_first_name'];
        } else {
            $logged_user = 'X';
        }
        SessionUtils::store('logged_user', $logged_user);
        
        /* user id */
        
        $user_id = App::getDB()->select("users", ["user_id"], ["user_login" => $this->form->login]);
        if ($user_id !== null && isset($user_id[0])) {
            $logged_user_id = $user_id[0]['user_id'];
        } else {
            $logged_user_id = -1;
        }
        SessionUtils::store('logged_user_id', $logged_user_id);
        
        /* user roles */
        
        $user_roles = App::getDB()->select(
            "roles",
            ["[>]user_privileges" => ["rol_id" => "priv_rol_id"]],
            ["rol_name"],
            ["priv_user_id" => $logged_user_id]
        );
        foreach ($user_roles as $role) {
            RoleUtils::addRole($role['rol_name']);
        }
        
        /* routing */
        
        if ($user_roles !== null) {
            if ($user_roles[0]['rol_name'] == 'admin') {
                App::getRouter()->redirectTo("homeAdmin");
            } else if ($user_roles[0]['rol_name'] == 'client') {
                App::getRouter()->redirectTo("homeClient");
            } else if ($user_roles[0]['rol_name'] == 'employee') {
                App::getRouter()->redirectTo("homeEmployee");
            } else if ($user_roles[0]['rol_name'] == 'manager') {
                App::getRouter()->redirectTo("homeManager");
            } else {
                App::getRouter()->redirectTo("home");
            }
        } 
       
    }

    public function action_loginShow() {
        $this->generateView();
    }

    public function action_login() {
        // DEBUG: Utils::addInfoMessage('1.1. in action_login()');
        
        if ($this->validate()) { # action if correctly logged
            //DEBUG: Utils::addInfoMessage('1.2. if -> after validate, before checkUserType');
            $this->setUserDataAndRedirect();
        } else {
            //DEBUG: Utils::addErrorMessage('1.3. validation failed');
            $this->generateView();
        }
    }

    public function generateView() {
        App::getSmarty()->assign('form', $this->form); // INFO: form data
        
        App::getSmarty()->assign('show_header', true);
        
        App::getSmarty()->display('LoginView.tpl');
    }
}