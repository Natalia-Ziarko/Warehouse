<?php

use core\App;
use core\Utils;

App::getRouter()->setDefaultRoute('home');
App::getRouter()->setLoginRoute('login');

/* global */

Utils::addRoute('home',   'HomeCtrl');
Utils::addRoute('login',  'LoginCtrl');
Utils::addRoute('logout', 'LoginCtrl');

/* admin */

Utils::addRoute('homeAdmin', 'AdminCtrl', ['admin']);

/* manager */

Utils::addRoute('homeManager',   'ManagerCtrl', ['manager']);
Utils::addRoute('warehouseStat', 'ManagerCtrl', ['manager']);
Utils::addRoute('employeeList',  'ManagerCtrl', ['manager']);
Utils::addRoute('warehouseLoc',  'ManagerCtrl', ['manager']);

/* employee */

Utils::addRoute('homeEmployee',        'EmployeeCtrl', ['employee']);

Utils::addRoute('productLocation',     'EmployeeCtrl', ['employee']);

Utils::addRoute('warehouseDel',        'EmployeeCtrl', ['employee']);
Utils::addRoute('newDeliveryForm',     'EmployeeCtrl', ['employee']);
Utils::addRoute('doneDeliveryForm',    'EmployeeCtrl', ['employee']);

Utils::addRoute('warehouseRel',         'EmployeeCtrl', ['employee']);
Utils::addRoute('newWarehouseRelForm',  'EmployeeCtrl', ['employee']);
Utils::addRoute('doneWarehouseRelForm', 'EmployeeCtrl', ['employee']);

/* client */

Utils::addRoute('homeClient',      'ClientCtrl', ['client']);
Utils::addRoute('newReleaseForm',  'ClientCtrl', ['client']);
Utils::addRoute('doneReleaseForm', 'ClientCtrl', ['client']);

/* 
 * Operations:
 * * Warehouse (available for managers and employees):
 * * * goodsReception:  who; what, how many, location
 * * * goodsRelease:    who; what, how many, location
 * * * productLocating: what -> where
 * * Client:
 * * * newOrder: when; what, how many
 * 
 * Views:
 * * Warehouse:  stock level, good location
 * * * only for manager: warehouse locations, employees, statistics(deliveries, releases, available locations)
 * * * only for warehouseman: statistics(new, in progress and done operations)
 * * Client: stock level, statistics(deliveries, releases)
 */