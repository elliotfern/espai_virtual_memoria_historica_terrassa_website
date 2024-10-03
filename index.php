<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$token = $_ENV['TOKEN'];
define("APP_TOKEN",$token );

class Route {
    private function simpleRoute($file, $route){
        //replacing first and last forward slashes
        //$_REQUEST['uri'] will be empty if req uri is /

        if(!empty($_REQUEST['uri'])){
            $route = preg_replace("/(^\/)|(\/$)/","",$route);
            $reqUri =  preg_replace("/(^\/)|(\/$)/","",$_REQUEST['uri']);
        }else{
            $reqUri = "/";
        }

        if($reqUri == $route){
            $params = [];
            include($file);
            exit();

        }

    }

    function add($route,$file){

        //will store all the parameters value in this array
        $params = [];

        //will store all the parameters names in this array
        $paramKey = [];

        //finding if there is any {?} parameter in $route
        preg_match_all("/(?<={).+?(?=})/", $route, $paramMatches);

        //if the route does not contain any param call simpleRoute();
        if(empty($paramMatches[0])){
            $this->simpleRoute($file,$route);
            return;
        }

        //setting parameters names
        foreach($paramMatches[0] as $key){
            $paramKey[] = $key;
        }

       
        //replacing first and last forward slashes
        //$_REQUEST['uri'] will be empty if req uri is /

        if(!empty($_REQUEST['uri'])){
            $route = preg_replace("/(^\/)|(\/$)/","",$route);
            $reqUri =  preg_replace("/(^\/)|(\/$)/","",$_REQUEST['uri']);
        }else{
            $reqUri = "/";
        }

        //exploding route address
        $uri = explode("/", $route);

        //will store index number where {?} parameter is required in the $route 
        $indexNum = []; 

        //storing index number, where {?} parameter is required with the help of regex
        foreach($uri as $index => $param){
            if(preg_match("/{.*}/", $param)){
                $indexNum[] = $index;
            }
        }

        //exploding request uri string to array to get
        //the exact index number value of parameter from $_REQUEST['uri']
        $reqUri = explode("/", $reqUri);

        //running for each loop to set the exact index number with reg expression
        //this will help in matching route
        foreach($indexNum as $key => $index){

             //in case if req uri with param index is empty then return
            //because url is not valid for this route
            if(empty($reqUri[$index])){
                return;
            }

            //setting params with params names
            $params[$paramKey[$key]] = $reqUri[$index];

            //this is to create a regex for comparing route address
            $reqUri[$index] = "{.*}";
        }

        //converting array to sting
        $reqUri = implode("/",$reqUri);

        //replace all / with \/ for reg expression
        //regex to match route is ready !
        $reqUri = str_replace("/", '\\/', $reqUri);

        //now matching route with regex
        if(preg_match("/$reqUri/", $route))
        {
            include($file);
            exit();

        }
    }

        function notFound($file){
            include($file);
            exit();
        }
    }

    $route = new Route(); 

    $url_root = $_SERVER['DOCUMENT_ROOT'];
    $url_server = $_SERVER['HTTP_HOST'];
    $dev = "";

    define("APP_SERVER", $url_server); 
    define("APP_ROOT", $url_root);
    define("APP_DEV",$dev);

    // Route for paths containing '/control/'
    require_once(APP_ROOT . APP_DEV . '/connection.php');
    require_once(APP_ROOT . APP_DEV . '/public/php/variables.php'); 
    require_once(APP_ROOT . APP_DEV . '/public/php/functions.php');

    $route->add("/login","public/pages/auth/login.php");
    

    // API SERVER 
    $route->add("/api/auth/get","api/auth/auth.php");
    $route->add("/api/auth/login","api/auth/login-process.php");

    $route->add("/api/represaliats/get","api/represaliats/get-represaliats.php");

    $route->add("/api/afusellats/get","api/afusellats/get-afusellats.php");
    $route->add("/api/auxiliars/get","api/auxiliars/get-aux.php");

    $route->add("/api/exiliats/get","api/exiliats/get-exiliats.php");

    // aqui comença la lògica del sistema

    session_set_cookie_params([
        'lifetime' => 60 * 60 * 24 * 30,  // Duración de la cookie en segundos
        'path' => '/',
        'domain' => $url_server,  // Reemplaza con tu dominio real
        'secure' => true,
        'httponly' => true
    ]);
    session_start();

    if (empty($_SESSION['user']) || !session_id()) {

        header('Location: ' .$dev . '/login');
        exit(); 

    } else {

        // Header (solo para las paginas)
        require_once(APP_ROOT . APP_DEV . '/public/php/header.php');
    
        // homepage
        $route->add("/","public/pages/homepage/admin.php");
        $route->add("/admin","public/pages/homepage/admin.php");

        // llistat complet
        $route->add("/represaliats","public/pages/represaliats/index.php");
        $route->add("/represaliats/fitxa/{id}","public/pages/represaliats/fitxa-persona.php");

        // afusellats
        $route->add("/afusellats","public/pages/afusellats/index.php");
        $route->add("/afusellats/fitxa/modifica/{id}","public/pages/afusellats/modificar-fitxa-persona.php");

        // exiliats
        $route->add("/exiliats","public/pages/afusellats/index.php");

}

?>