<?php

namespace app\controllers;

use core\App;
use core\Messages;
use core\Utils;
use core\RoleUtils;
use core\ParamUtils;
use core\SessionUtils;
use app\forms\ClientProdForm;
use PDOException;

class ClientCtrl {
    
    private $search_form;
    private $prod_list;
    
    private $releases_new_q;
    
    public function __construct(){
        $this->search_form = new ClientProdForm();
    }
    
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
    
    public function action_homeClient() {
        SessionUtils::loadMessages();
        
        $user_data = $this->load_user_data();
        $logged_user_id = $user_data['logged_user_id'];
        
        /* search engine */
              
        $filter = ParamUtils::getFromGet("product");
        
        /* main data */
        
        try{
            $tables = [
                '[>]oper_positions' => ['products.prod_id' => 'pos_prod_id'],
                '[>]stock' => ['oper_positions.pos_id' => 'stock_pos_id']             
            ];
            
            $columns = [
                'products.prod_id',
                'products.prod_name',
                'stock_count' => App::getDB()->raw('COUNT(stock.stock_pos_id)')
            ];       
            
            if (isset($filter) && strlen($filter) > 0) {
                $search_param = $filter.'%';
            
                $whereOrder = [
                    'AND' => [
                        'products.prod_cl_id' => $logged_user_id,
                        'products.prod_name[~]' => $search_param
                    ],
                    'GROUP' => ['products.prod_id']
                ];

                $this->prod_list = App::getDB()->select(
                    'products',
                    $tables,
                    $columns,
                    $whereOrder
                   
                );
            } else {
                $whereOrder = [
                    "prod_cl_id" => $logged_user_id,
                    "GROUP" => "products.prod_id"
                ];
                
                $this->prod_list = App::getDB()->select(
                    'products',
                    $tables,
                    $columns,
                    $whereOrder
                );
            }
            // DEBUG: Utils::addErrorMessage(App::getDB()->debug()->last());
        } catch (PDOException $e){
            $this->handle_error($e);
        }
        
        $data = array_merge($user_data, [
            'search_form'     => $this->search_form,
            'prod_list'       => $this->prod_list,
            'search_from_get' => $filter,
            'show_header'     => true
        ]);
        
        App::getSmarty()->assign($data);
        
        $this->assign_roles();        
        
        SessionUtils::storeMessages();
        
        App::getSmarty()->display("HomeClientView.tpl");
    }
    
    public function action_newReleaseForm() {
        SessionUtils::loadMessages();
        
        $user_data = $this->load_user_data();
        $logged_user_id = $user_data['logged_user_id'];
        
        /* main data */
        
        try {
            $this->sup_prod_amount = App::getDB()->count(
                    'products',
                [
                    'prod_cl_id' => $logged_user_id
                ]
            );
            
            $tables = [
                '[>]oper_positions' => ['products.prod_id' => 'pos_prod_id'],
                '[>]stock' => ['oper_positions.pos_id' => 'stock_pos_id']             
            ];
            
            $columns = [
                'products.prod_id',
                'products.prod_name',
                'stock_count' => App::getDB()->raw('COUNT(stock.stock_pos_id)')
            ];
            
            $whereOrder = [
                "prod_cl_id" => $logged_user_id,
                "GROUP" => "products.prod_id"
            ];
            
            $this->prod_list = App::getDB()->select(
                'products',
                $tables,
                $columns,
                $whereOrder
            );
            
            // DEBUG: Utils::addErrorMessage($this->sup_prod_amount);
            
            // SQL: SELECT COUNT(oper_id) FROM operations WHERE oper_type = 'R' AND oper_cl_id = $logged_user_id AND oper_emp_id IS NULL;
            $this->releases_new_q = App::getDB()->count(                  
                    'operations',
                [
                    'oper_id'
                ], [
                    'oper_type' => 'R',
                    'oper_cl_id' => $logged_user_id,
                    'oper_emp_id' => null
                ]
            );            
            
        } catch (PDOException $e){
            $this->handle_error($e);
        }
        
        /* launching the site with variables */
        
        $data = array_merge($user_data, [
            'prod_list'       => $this->prod_list,
            'sup_prod_amount' => $this->sup_prod_amount,
            'supplier_id'     => $logged_user_id
        ]);
        
        App::getSmarty()->assign($data);
        
        $this->assign_roles();
        
        // DEBUG: Utils::addErrorMessage('Zalogowany użytkownik: '.$logged_user_id);
        // DEBUG: Utils::addErrorMessage('Liczba oczekujących wydań: '.$this->rel_new_count);
        
        if ($this->releases_new_q == 0) {
            SessionUtils::storeMessages();
            
            App::getSmarty()->assign('show_header', false);
            
            App::getSmarty()->display("NewReleaseFormView.tpl");
        } else {
            $filter = null;
            App::getSmarty()->assign('search_from_get', $filter);
            
            Utils::addErrorMessage('Operacja nie może zostać zrealizowana - w systemie widnieje niezakończone wydanie!');
            
            App::getSmarty()->assign('show_header', true);
            
            App::getSmarty()->display("HomeClientView.tpl");
        }
        
    }
    
    public function action_doneReleaseForm() {
        SessionUtils::loadMessages();
        
        $user_data = $this->load_user_data();
        $logged_user_id = $user_data['logged_user_id'];
        
        /* form data */
        
        $release_date = ParamUtils::getFromGet('release_date');  
        
        /* main data */
        
        $current_date = date('Y-m-d');
        
        if ($release_date < $current_date) {
            Utils::addErrorMessage('Data odbioru nie może być wcześniejsza niż dzisiejsza');
            
            try {
                $this->sup_prod_amount = App::getDB()->count('products', ['prod_cl_id' => $logged_user_id]);

                $tables = [
                    '[>]oper_positions' => ['products.prod_id' => 'pos_prod_id'],
                    '[>]stock' => ['oper_positions.pos_id' => 'stock_pos_id']             
                ];

                $columns = [
                    'products.prod_id',
                    'products.prod_name',
                    'stock_count' => App::getDB()->raw('COUNT(stock.stock_pos_id)')
                ];

                $whereOrder = [
                    "prod_cl_id" => $logged_user_id,
                    "GROUP" => "products.prod_id"
                ];

                $this->prod_list = App::getDB()->select(
                    'products',
                    $tables,
                    $columns,
                    $whereOrder
                );

                // DEBUG: Utils::addErrorMessage($this->sup_prod_amount);

                // SQL: SELECT COUNT(oper_id) FROM operations WHERE oper_type = 'R' AND oper_cl_id = $logged_user_id AND oper_emp_id IS NULL;
                $this->releases_new_q = App::getDB()->count(                  
                    'operations',
                    [
                        'oper_id'
                    ], [
                        'oper_type' => 'R',
                        'oper_cl_id' => $logged_user_id,
                        'oper_emp_id' => null
                    ]
                );
            } catch (PDOException $e){
                $this->handle_error($e);
            }
            
            /* launching the site with variables */      

            $data = array_merge($user_data, [
                'prod_list'       => $this->prod_list,
                'sup_prod_amount' => $this->sup_prod_amount,
                'supplier_id'     => $logged_user_id,
                'show_header'     => false
            ]);

            App::getSmarty()->assign($data);

            $this->assign_roles();
            
            SessionUtils::storeMessages();            
            
            App::getSmarty()->display("NewReleaseFormView.tpl");
            
            return;
        }
        
        $products = ParamUtils::getFromGet('product_id');
        
        $quantities = ParamUtils::getFromGet('quantity');
        $quantity_sum = array_map('floatval', $quantities);
        $quantity_sum = array_sum($quantity_sum);
        // DEBUG: Utils::addInfoMessage('$quantity_sum: ' . $quantity_sum);
        
        $form_details = [];
        
        foreach ($products as $row => $product_id) {
            if ($quantities[$row] > 0) {
                $quantity = $quantities[$row];

                $form_details[] = [
                    "prod_id" => $product_id,
                    "quantity" => $quantity
                ];
            }
        }
        
        if ($quantity_sum >= 1) {
            App::getDB()->insert(
                'operations',
                [
                    'oper_type' => 'R',
                    'oper_receive_date' => App::getDB()->raw('SYSDATE()'),
                    'oper_release_date' => $release_date,
                    'oper_cl_id' => $logged_user_id
                ]
            );

            $last_oper_id = App::getDB()->id();
            if (!$last_oper_id) {
                Utils::addErrorMessage('Nie pobrano ID dostawy');
            } else {
                foreach ($form_details as $row) {
                    $prod_id = $row["prod_id"];
                    $quantity = $row["quantity"];

                    App::getDB()->insert(
                        'oper_positions',
                        [
                            'pos_prod_id' => $prod_id,
                            'pos_amount'  => $quantity,
                            'pos_oper_id' => $last_oper_id
                        ]
                    );
                }
            }

            Utils::addInfoMessage('Poprawnie przyjęto zlecenie przygotowania towarów do wydania');
            
            $filter = null;
            App::getSmarty()->assign('search_from_get', $filter);
        } else {
            Utils::addErrorMessage('Formularz niepoprawnie uzupełniony - nie podano ilości');
        }
        
        try{
            $tables = [
                '[>]oper_positions' => ['products.prod_id' => 'pos_prod_id'],
                '[>]stock' => ['oper_positions.pos_id' => 'stock_pos_id']             
            ];
            
            $columns = [
                'products.prod_id',
                'products.prod_name',
                'stock_count' => App::getDB()->raw('COUNT(stock.stock_pos_id)')
            ];   
            
            $whereOrder = [
                "prod_cl_id" => $logged_user_id,
                "GROUP" => "products.prod_id"
            ];

            $this->prod_list = App::getDB()->select(
                'products',
                $tables,
                $columns,
                $whereOrder
            );

            // DEBUG: Utils::addErrorMessage(App::getDB()->debug()->last());
        } catch (PDOException $e){
            $this->handle_error($e);
        }

        /* launching the site with variables */
     
        $data = array_merge($user_data, [
            'prod_list'   => $this->prod_list,
            'search_form' => $this->search_form,
            'show_header' => true
        ]);
        
        App::getSmarty()->assign($data);
        
        $this->assign_roles();        
        
        SessionUtils::storeMessages();
        
        App::getRouter()->redirectTo("homeClient");
    }
}