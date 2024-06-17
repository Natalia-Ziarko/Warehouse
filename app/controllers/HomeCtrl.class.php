<?php

namespace app\controllers;

use core\App;
use core\Utils;
use core\ParamUtils;

class HomeCtrl {
    
    public function action_home() {
        
        App::getSmarty()->assign('show_header', true);

        $status = (null !== ParamUtils::getFromGet('status')) ? ParamUtils::getFromGet('status') : null;

        if ($status === 'logged_out') {
            Utils::addInfoMessage('Poprawnie wylogowano!');
        } elseif ($status === 'logout_failed') {
            Utils::addErrorMessage('Wylogowanie nie powiodło się.');
        }
        
        App::getSmarty()->display("HomeView.tpl");
        
    }
    
}