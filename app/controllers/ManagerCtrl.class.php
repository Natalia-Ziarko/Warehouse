<?php

namespace app\controllers;

use core\App;
use core\Utils;
use core\RoleUtils;
use core\SessionUtils;

class ManagerCtrl {
    
    const ROLE_EMPLOYEE = 3;
    
    private $stock_q; 
    private $locations_q;
    private $occupied_pct;
    
    private $emp_list;
    
    private $location_list;
    private $warehouse_layout;
    
    private $employees_q;
    private $deliveries_q;
    private $releases_new_q;
    private $releases_done_q;
    private $operations_done_q;
    
    private function load_user_data() {
        return [
            'logged_user'    => SessionUtils::load('logged_user', true),
            'logged_user_id' => SessionUtils::load('logged_user_id', true)
        ];
    }

    private function handle_error(PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        Utils::addErrorMessage('Wystąpił błąd podczas pobierania rekordów');
    }
 
    private function assign_roles() {
        App::getSmarty()->assign('roleAdmin',    RoleUtils::inRole('admin'));
        App::getSmarty()->assign('roleClient',   RoleUtils::inRole('client'));
        App::getSmarty()->assign('roleEmployee', RoleUtils::inRole('employee'));
        App::getSmarty()->assign('roleManager',  RoleUtils::inRole('manager'));
    }
    
    private function count_stock() {
        return App::getDB()->count('stock', 'stock_id');
    }

    private function count_loc() {
        return App::getDB()->count('locations', 'loc_id');
    }    
    
    public function action_homeManager() {
        SessionUtils::storeMessages();
        
        $user_data = $this->load_user_data();
        
        /* main data */
        
        try {
            $this->stock_q = $this->count_stock();
            $this->locations_q = $this->count_loc();
            
            $this->occupied_pct = $this->locations_q > 0 ? round(($this->stock_q / $this->locations_q) * 100, 2) : 0;
        } catch (PDOException $e){
            $this->handle_error($e);
        }
        
        /* launching the site with variables */

        $data = array_merge($user_data, [
            'stock_count'      => $this->stock_q,
            'loc_count'        => $this->locations_q,
            'occupied_percent' => $this->occupied_pct,
            'show_header'      => true
        ]);
        
        App::getSmarty()->assign($data);
        
        $this->assign_roles();
        
        App::getSmarty()->display("HomeManagerView.tpl");
    }
    
    public function action_employeeList() {
        SessionUtils::storeMessages();
        
        $user_data = $this->load_user_data();
        
        /* main data */
        
        try {
            $this->emp_list = App::getDB()->select(
                'users',
                [
                    '[>]user_privileges' => ['users.user_id' => 'priv_user_id'],
                    '[>]roles' => ['user_privileges.priv_rol_id' => 'rol_id']
                ], [
                    'users.user_id',
                    'users.user_login',
                    'users.user_name_surname',
                    'users.user_first_name',
                    'users.user_phone'
                ],
                    ['roles.rol_id' => self::ROLE_EMPLOYEE],
                    ['ORDER' => ['user_name_surname' => 'DESC']]
            );
        } catch (PDOException $e){
            $this->handle_error($e);
        }
        
        /* launching the site with variables */

        $data = array_merge($user_data, [
            'emp_list'    => $this->emp_list,
            'show_header' => false,
        ]);

        App::getSmarty()->assign($data); 
        
        $this->assign_roles();       
        
        App::getSmarty()->display("EmployeeListView.tpl");
    }
    
    public function action_warehouseLoc() {
        SessionUtils::storeMessages();
        
        $user_data = $this->load_user_data();
        
        /* main data */
        
        try {
            $this->location_list = App::getDB()->select('locations','*');
            
            $this->warehouse_layout = App::getDB()->select(
                "sizes",
                [
                    "[>]locations" => ["size_id" => "loc_size_id"]
                ],
                [
                    "sizes.size_id",
                    "sizes.size_dim1_max",
                    "sizes.size_dim2_max",
                    "sizes.size_dim3_max",
                    "total_places" =>  App::getDB()->raw("COUNT(locations.loc_size_id)")
                ],
                [
                    "GROUP" => "sizes.size_id"
                ]
            );
        } catch (PDOException $e){
            $this->handle_error($e);
        }
        
        /* launching the site with variables */

        $data = array_merge($user_data, [
            'location_list'    => $this->location_list,
            'warehouse_layout' => $this->warehouse_layout,
            'show_header'      => false,
        ]);

        App::getSmarty()->assign($data);
        
        $this->assign_roles();
        
        App::getSmarty()->display("WarehouseLocView.tpl");
    }
    
    public function action_warehouseStat() {
        SessionUtils::storeMessages();
        
        $user_data = $this->load_user_data();
        
        /* main data */

        try {
            $this->stock_q = $this->count_stock();
            $this->locations_q = $this->count_loc();
            
            $this->employees_q = App::getDB()->count(                  
                'users',
                [
                    '[>]user_privileges' => ['users.user_id' => 'priv_user_id'],
                    '[>]roles' => ['user_privileges.priv_rol_id' => 'rol_id']
                ], [
                    'users.user_id'
                ], [
                    'roles.rol_id' => self::ROLE_EMPLOYEE
                ]
            );
            
            $this->deliveries_q = App::getDB()->count('operations', ['oper_type' => 'D']);
            $this->releases_new_q = App::getDB()->count('operations', ['oper_type' => 'R', 'oper_emp_id' => null]);
            $this->releases_done_q = App::getDB()->count('operations', ['oper_type' => 'R', 'oper_emp_id[!]' => null]);
            
            $this->occupied_pct = $this->locations_q > 0 ? round(($this->stock_q / $this->locations_q) * 100, 2) : 0;
            
            $this->operations_done_q = $this->deliveries_q + $this->releases_done_q;
            
        } catch (PDOException $e){
            $this->handle_error($e);
        }
        
        /* launching the site with variables */

        $data = array_merge($user_data, [
            'stock_count'      => $this->stock_q,
            'loc_count'        => $this->locations_q,
            'emp_count'        => $this->employees_q,
            'occupied_percent' => $this->occupied_pct,
            'del_count'        => $this->deliveries_q,
            'rel_new_count'    => $this->releases_new_q,
            'rel_done_count'   => $this->releases_done_q,
            'done_oper_count'  => $this->operations_done_q,
            'show_header'      => false
        ]);

        App::getSmarty()->assign($data);
        
        $this->assign_roles();
        
        App::getSmarty()->display("WarehouseStatView.tpl");
    }

}