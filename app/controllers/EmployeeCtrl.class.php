<?php

namespace app\controllers;

use core\App;
use core\Utils;
use core\RoleUtils;
use core\ParamUtils;
use core\SessionUtils;

class EmployeeCtrl {
    
    const ROLE_CLIENT = 4;
    
    private $stock_q;
    private $locations_q;
    private $occupied_pct;
    private $deliveries_done_q;
    private $releases_done_q;
    private $releases_new_q;
    
    private $release_list;
    
    private $release_details_list;

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

    private function count_all_locations() {
        return App::getDB()->count('locations', 'loc_id');
    }
    
    private function calculate_occupied_pct($stock_q, $locations_q) {
        return $locations_q > 0 ? round(($stock_q / $locations_q) * 100, 2) : 0;
    }
    
    private function count_new_releases() {
       return App::getDB()->count('operations', ['oper_id'], ['oper_type' => 'R', 'oper_emp_id' => null]); 
    }

    private function count_done_operations($logged_user_id, $oper_type) {
        return App::getDB()->count('operations', ['oper_id'], [
                'oper_type' => $oper_type,
                'oper_emp_id' => $logged_user_id
            ]);
    }    
    
    public function action_homeEmployee() {
        SessionUtils::loadMessages();
        
        $user_data = $this->load_user_data();
        $logged_user_id = $user_data['logged_user_id'];
        
        /* main data */
        
        try {
            $this->stock_q = $this->count_stock();
            $this->locations_q = $this->count_all_locations();

            $this->occupied_pct = $this->locations_q > 0 ? round(($this->stock_q / $this->locations_q) * 100, 2) : 0;
            
            $query_result_del = App::getDB()->select(
                'operations',
                ['done_deliveries' => App::getDB()->raw('COUNT(oper_id)')],
                [
                    'oper_type' => 'D',
                    'oper_emp_id' => $logged_user_id
                ]
                );
            $this->deliveries_done_q = ($query_result_del[0]['done_deliveries']);
            
            $query_result_rel = App::getDB()->select('operations',
                ['done_releases' => App::getDB()->raw('COUNT(oper_id)')],
                [
                    'oper_type' => 'R',
                    'oper_emp_id' => $logged_user_id
                ]
                );
            $this->releases_done_q = ($query_result_rel[0]['done_releases']);
            
            $this->releases_new_q = App::getDB()->count(                  
                    'operations',
                [
                    'oper_id'
                ], [
                    'oper_type' => 'R',
                    'oper_emp_id' => null
                ]
            );
            
        } catch (PDOException $e){
            $this->handle_error($e);
        }
        
        /* launching the site with variables */

        $data = array_merge($user_data, [
            'stock_count'      => $this->stock_q,
            'loc_count'        => $this->locations_q,
            'occupied_percent' => $this->occupied_pct,
            'done_deliveries'  => $this->deliveries_done_q,
            'done_releases'    => $this->releases_done_q,
            'rel_new_count'    => $this->releases_new_q,
            'show_header'      => true
        ]);
        
        App::getSmarty()->assign($data);
        
        $this->assign_roles();
        
        SessionUtils::storeMessages();

        App::getSmarty()->display("HomeEmployeeView.tpl");
    }
    
    public function action_productLocation() {
        SessionUtils::loadMessages();
        
        $user_data = $this->load_user_data();
        
        /* search engine data */
        
        $filter_prod = ParamUtils::getFromGet("prod");
        $filter_date = ParamUtils::getFromGet("date");
        
        /* main data */

        try {
            $tables = [
                '[>]oper_positions' => ['stock_pos_id' => 'pos_id'],
                '[>]operations' => ['oper_positions.pos_oper_id' => 'oper_id'],
                '[>]products' => ['oper_positions.pos_prod_id' => 'prod_id'],
                '[>]locations' => ['stock_loc_id' => 'loc_id'],
                '[>]users' => ['operations.oper_cl_id' => 'user_id']
            ];

            $selectedColumns = [
                'products.prod_id',
                'products.prod_name',
                'operations.oper_receive_date',
                'locations.loc_alley',
                'locations.loc_side',
                'locations.loc_number',
                'users.user_name_surname'
            ];
            
            $queryConditions = [];
            
            if (isset($filter_prod) && strlen($filter_prod) > 0) {
                $search_param = $filter_prod . '%';
                
                $queryConditions['OR'] = [
                    'products.prod_name[~]' => $search_param,
                    'products.prod_id[~]' => $search_param
                ];
            }

            if (!empty($filter_date)) {
                $queryConditions['operations.oper_receive_date'] = $filter_date;
            }

            $this->prod_list = App::getDB()->select(
                'stock',
                $tables,
                $selectedColumns,
                $queryConditions
            );       
            // DEBUG: echo App::getDB()->debug()->last();
        } catch (PDOException $e){
            $this->handle_error($e);
        }
        
        /* launching the site with variables */
        
        $data = array_merge($user_data, [
            'prod_list'   => $this->prod_list,
            'filter_prod' => $filter_prod,
            'filter_date' => $filter_date,
            'show_header' => false,
        ]);

        App::getSmarty()->assign($data); 
        
        $this->assign_roles();
        
        App::getSmarty()->display("ProductLocView.tpl");
    }
    
    public function action_warehouseDel() {
        SessionUtils::loadMessages();
        
        $user_data = $this->load_user_data();
        
        /* main data */
        
        try {
            $this->supplier_list = App::getDB()->select(
                    'users',
                [
                    '[>]user_privileges' => ['users.user_id' => 'priv_user_id'],
                    '[>]roles' => ['user_privileges.priv_rol_id' => 'rol_id']
                ], [
                    'users.user_name_surname',
                    'users.user_id'
                ], [
                    'roles.rol_id' => self::ROLE_CLIENT
                ]
            );
        } catch (PDOException $e){
            $this->handle_error($e);
        }
        
        /* launching the site with variables */
        
        $data = array_merge($user_data, [
            'supplier_list'      => $this->supplier_list,
            'show_header'      => false
        ]);
        
        App::getSmarty()->assign($data);
        
        $this->assign_roles();
        
        SessionUtils::storeMessages();
        
        App::getSmarty()->display("WarehouseDelView.tpl");
    }
    
    public function action_newDeliveryForm() {
        SessionUtils::loadMessages();
        
        $user_data = $this->load_user_data();
        
        /* chosen client */
        
        $supplier_id = ParamUtils::getFromGet("supplier");
        SessionUtils::store('supplier_id', $supplier_id);
        // DEBUG: Utils::addErrorMessage($supplier_id);
        
        /* main data */
        
        try {
            $this->sup_prod_amount = App::getDB()->count('products', ['prod_cl_id' => $supplier_id]);
            // DEBUG: Utils::addErrorMessage($this->sup_prod_amount);
            
            $this->prod_list = App::getDB()->select(
                'products',
                [
                    '[>]users' => ['products.prod_cl_id' => 'user_id']
                ], [
                    'products.prod_id',
                    'products.prod_name',
                    'users.user_name_surname'
                ], [
                    'prod_cl_id' => $supplier_id
                ]
            );
        } catch (PDOException $e){
            $this->handle_error($e);
        }
        // DEBUG: Utils::addInfoMessage('Poprawnie wybrano dostawcę i utworzono formularz');
        
        /* launching the site with variables */
        
        $data = array_merge($user_data, [
            'prod_list'       => $this->prod_list,
            'sup_prod_amount' => $this->sup_prod_amount,
            'supplier_id'     => SessionUtils::load('supplier_id', true),
            'show_header'     => false,
        ]);

        App::getSmarty()->assign($data);
        
        $this->assign_roles();
        
        SessionUtils::storeMessages();
        
        App::getSmarty()->display("NewDeliveryFormView.tpl");
    }
    
    public function action_doneDeliveryForm() {
        SessionUtils::loadMessages();
        
        $user_data = $this->load_user_data();
        $logged_user_id = $user_data['logged_user_id'];
        
        /* form data */
        
        $supplier_id = SessionUtils::load('supplier_id', $keep = false);
        // DEBUG: Utils::addErrorMessage('$supplier_id: '.$supplier_id);
        
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
        /** DEBUG:
        foreach ($form_details as $row) {
            $prod_id = $row["prod_id"];
            $quantity = $row["quantity"];
            Utils::addInfoMessage("prod_id = $prod_id, quantity = $quantity");
        }
        */
        
        /* checking if there is enough space */
        
        $locations_q = $this->count_all_locations();
        // DEBUG: Utils::addInfoMessage('$total_locations: ' . $total_locations);
        
        $full_locations = App::getDB()->count('stock', 'stock_id');
        // DEBUG: Utils::addInfoMessage('$full_locations: ' . $full_locations);
        
        $empty_locations = $locations_q - $full_locations;
        
        if ($quantity_sum >= 1) {
            if ($empty_locations >= $quantity_sum) {
                // DEBUG: Utils::addInfoMessage('Wystarczy miejsca na magazynie');
                try {
                    App::getDB()->pdo->beginTransaction();

                    App::getDB()->insert(
                        'operations',
                        [
                            'oper_type' => 'D',
                            'oper_receive_date' => App::getDB()->raw('SYSDATE()'),
                            'oper_cl_id' => $supplier_id,
                            'oper_emp_id' => $logged_user_id
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

                            $last_pos_id = App::getDB()->id(); // INFO: Retrieve the pos_id from the last insert

                            /* finding next free location in stock table */

                            for ($i = 0; $i < $quantity; $i++) {
                                $next_free_location_query = "SELECT loc_id FROM locations WHERE loc_id NOT IN (SELECT stock_loc_id FROM stock) LIMIT 1";
                                $next_free_location_result = App::getDB()->query($next_free_location_query);
                                $next_free_location = $next_free_location_result->fetchColumn();

                                App::getDB()->insert(
                                    'stock',
                                    [
                                        'stock_pos_id' => $last_pos_id,
                                        'stock_loc_id' => $next_free_location,
                                        'stock_prod_id' => $prod_id
                                    ]
                                );
                            }
                        }        
                    }
                    App::getDB()->pdo->commit();

                    Utils::addInfoMessage('Poprawnie przyjęto dostawę');
                    // DEBUG: SessionUtils::loadMessages($keep = false);            
                } catch (Exception $e) {
                    App::getDB()->pdo->rollBack(); // Rollback transaction on error
                    $this->handle_error($e);
                } 
            } else {   
                Utils::addErrorMessage('Brak miejsca na magazynie - nie można przyjąć dostawy.<br>Wolna ilość miejsc: ' . $empty_locations);
            }       
        } else {
            Utils::addErrorMessage('Formularz niepoprawnie uzupełniony - nie podano ilości');
        }

        /* home page data */

        $this->stock_q = $this->count_stock();
        $this->locations_q = $this->count_all_locations();

        $this->occupied_pct = $this->calculate_occupied_pct($this->stock_q, $this->locations_q);

        $this->deliveries_done_q = $this->count_done_operations($logged_user_id, 'D');

        $this->releases_done_q = $this->count_done_operations($logged_user_id, 'R');

        $this->releases_new_q = $this->count_new_releases();

        /* launching the site with variables */

        $data = array_merge($user_data, [
            'stock_count'      => $this->stock_q,
            'loc_count'        => $this->locations_q,
            'occupied_percent' => $this->occupied_pct,
            'done_deliveries'  => $this->deliveries_done_q,
            'done_releases'    => $this->releases_done_q,
            'rel_new_count'    => $this->releases_new_q,
            'show_header'      => true,
        ]);

        App::getSmarty()->assign($data);
        
        $this->assign_roles();
        
        SessionUtils::storeMessages();
        
        App::getRouter()->redirectTo("homeEmployee");
    }    
    
    public function action_warehouseRel() {
        SessionUtils::loadMessages();
        
        $user_data = $this->load_user_data();
        
        try {
            // SQL: SELECT oper_id, oper_cl_id FROM operations WHERE oper_type = 'R' AND oper_emp_id IS NULL;
            $this->release_list = App::getDB()->select( 
                    'operations',
                [
                    'oper_id',
                    'oper_cl_id'
                ], [
                    'oper_type' => 'R',
                    'oper_emp_id' => null
                ]
            );
        } catch (PDOException $e){
            $this->handle_error($e);
        }
        
        /* launching the site with variables */

        $data = array_merge($user_data, [
            'release_list' => $this->release_list,
            'show_header'  => false,
        ]);

        App::getSmarty()->assign($data);
        
        $this->assign_roles();
        
        SessionUtils::storeMessages();
        
        App::getSmarty()->display("WarehouseRelView.tpl");
    }

    public function action_newWarehouseRelForm() {
        SessionUtils::loadMessages();
        
        $user_data = $this->load_user_data();
        
        /* chosen operation */
        
        $oper_id = ParamUtils::getFromGet("operation");
        SessionUtils::store('oper_id', $oper_id);
        // DEBUG: Utils::addErrorMessage('oper_id: '.$oper_id);
        
        /* main data */
        
        $this->release_details_list = App::getDB()->select(
            'oper_positions',
            [
                '[>]products' => ['pos_prod_id' => 'prod_id']
            ],    
            [
                'pos_prod_id',
                'pos_amount',
                'prod_name'
            ], [
                'pos_oper_id' => $oper_id
            ]
        );
        /* DEBUG:
         * foreach ($this->release_details_list as $row) {
         *     Utils::addErrorMessage('prod_id: '.$row["pos_prod_id"].', amount: '.$row["pos_amount"]);
         * }
         */
        
        /** INFO: prepared for the list with the locations of each product
        $warehouse_rel_list = [];
        
        foreach ($this->release_details_list as $row) {
            $pos_prod_id = $row['pos_prod_id'];
            $pos_amount = $row['pos_amount'];
            $prod_name = $row['prod_name'];
            
            $stock = App::getDB()->select(
                'stock',
                [
                    '[>]oper_positions' => ['stock_pos_id' => 'pos_id']
                ], [
                    'stock.stock_loc_id',
                    'oper_positions.pos_prod_id(prod_id)',
                    'stock.stock_id'
                ], [
                    'oper_positions.pos_prod_id' => $pos_prod_id
                ]
            );
            
            $stock_id = $stock[0]['stock_id'];

            for ($i = 0; $i < $pos_amount; $i++) {
                $warehouse_rel_list[] = [
                                            "prod_id" => $pos_prod_id,
                                            "prod_name" => $prod_name,
                                            "stock_id" => $stock_id
                                        ];
            }  
        }
        */
        // DEBUG: var_dump($warehouse_rel_list);
        
        /* launching the site with variables */

        $data = array_merge($user_data, [
            'release_details_list' => $this->release_details_list,
            'oper_id'              => SessionUtils::load('oper_id', true),
            'show_header'          => false,
        ]);

        App::getSmarty()->assign($data);
        
        $this->assign_roles();
        
        SessionUtils::storeMessages();

        App::getSmarty()->display("NewWarehouseRelFormView.tpl");
    }
    
    public function action_doneWarehouseRelForm() {
        SessionUtils::loadMessages();
        
        $user_data = $this->load_user_data();
        $logged_user_id = $user_data['logged_user_id'];
        
        /* chosen operation */
        
        $oper_id = SessionUtils::load('oper_id', $keep = false);
        
        /* database operations */
        
        try {
            App::getDB()->pdo->beginTransaction();
            
            $this->release_details_list = App::getDB()->select(
                'oper_positions',
                [
                    '[>]products' => ['pos_prod_id' => 'prod_id']
                ],    
                [
                    'pos_prod_id',
                    'pos_amount'
                ], [
                    'pos_oper_id' => $oper_id
                ]
            );
            
            $prod_total = 0;
            $delete_total = 0;
            $warehouse_rel_list = [];

            foreach ($this->release_details_list as $row) {
                $pos_prod_id = $row['pos_prod_id'];
                $pos_amount = $row['pos_amount'];
                
                // DEBUG: Utils::addInfoMessage("prod_id = $pos_prod_id, quantity = $pos_amount");

                for ($i = 0; $i < $pos_amount; $i++) {
                    $new_row = ["prod_id" => $pos_prod_id];
                    
                    $stock_id_to_del = App::getDB()->get(
                        "stock",
                        "stock_id",
                        ["stock_prod_id" => $pos_prod_id]
                    );
                    // DEBUG: Utils::addErrorMessage('$stock_id_to_del: '.$stock_id_to_del);
                    
                    if (!empty($stock_id_to_del)) {
                        $new_row["stock_id"] = $stock_id_to_del;
                        
                        App::getDB()->delete("stock", ["stock_id" => $stock_id_to_del]);
                        
                        $delete_total++;
                    } else {
                        Utils::addErrorMessage('Wystąpił błąd w $stock_id_to_del');
                    }
                    $warehouse_rel_list[] = $new_row;
                    
                    $prod_total++;
                }  
            }
            
            if ($prod_total === $delete_total) {
                App::getDB()->update(
                    'operations',
                    ['oper_emp_id' => $logged_user_id],
                    ['oper_id' => $oper_id]
                );
                
                // DEBUG: Utils::addInfoMessage('Poprawnie przygotowano wydanie zewnętrzne');
                
                App::getDB()->pdo->commit();
            } else {
                App::getDB()->pdo->rollback();
                
                Utils::addErrorMessage('Wystąpił błąd podczas operacji');
            }
            
            $messages = [];
            $messages[] = 'Operacja wykonana poprawnie.<br><br>Produkty do wydania:<br>';
            foreach ($warehouse_rel_list as $row) {
                $message = 'Produkt o ID: ' . $row['prod_id'];
                if (isset($row['stock_id'])) {
                    $message .= ' => z lokalizacji o ID: ' . $row['stock_id'];
                }
                $messages[] = $message;
            }
            $info_for_warehouseman = implode('<br>', $messages);      
            Utils::addInfoMessage($info_for_warehouseman);
            
            /* DEBUG:
             * foreach ($warehouse_rel_list as $row) {
             *     Utils::addErrorMessage('prod_id: '.$row["prod_id"]);
             * }
             */            
            
            /* home page data */

            $this->stock_q = $this->count_stock();
            $this->locations_q = $this->count_all_locations();

            $this->occupied_pct = $this->calculate_occupied_pct($this->stock_q, $this->locations_q);
            
            $this->deliveries_done_q = $this->count_done_operations($logged_user_id, 'D');
            
            $this->releases_done_q = $this->count_done_operations($logged_user_id, 'R');
            
            $this->releases_new_q = $this->count_new_releases();
            
        } catch (PDOException $e){
            App::getDB()->pdo->rollBack();
            $this->handle_error($e);
        }

        /* launching the site with variables */
      
        $data = array_merge($user_data, [
            'stock_count'      => $this->stock_q,
            'loc_count'        => $this->locations_q,
            'occupied_percent' => $this->occupied_pct,
            'done_deliveries'  => $this->deliveries_done_q,
            'done_releases'    => $this->releases_done_q,
            'rel_new_count'    => $this->releases_new_q,
            'show_header'      => true,
        ]);

        App::getSmarty()->assign($data);
        
        $this->assign_roles();
        
        SessionUtils::storeMessages();
        
        App::getRouter()->redirectTo("homeEmployee");
    }
}