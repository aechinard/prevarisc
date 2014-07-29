<?php

use Zend_Controller_Router_Route_Static as RouteStatic;

$routes = array(

    /*
    |--------------------------------------------------------------------------
    | Gestion de l'authentification
    |--------------------------------------------------------------------------
    */
    'login' => new RouteStatic('login', array('controller' => 'auth', 'action' => 'login')),
    'logout' => new RouteStatic('logout', array('controller' => 'auth', 'action' => 'logout')),

    /*
    |--------------------------------------------------------------------------
    | Erreurs
    |--------------------------------------------------------------------------
    */
    'error' => new RouteStatic('error', array('controller' => 'error', 'action' => 'error')),

);
