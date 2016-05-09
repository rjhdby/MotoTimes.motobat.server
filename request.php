<?php
ini_set ( 'display_errors', 'On' );
ini_set ( 'display_startup_errors', 'On' );
header ( 'Content-Type: application/json; charset=utf-8' );

function __autoload ( $class_name ) {
    require_once 'class/' . $class_name . '.php';
}

switch ( mb_strtolower ( $_GET [ 'm' ] ) ) {
//    case 'getrole':
//    case 'setrole':
    case 'list':
        $request = new GetList();
        break;
//    case 'create':
//    case 'cpk':
//    case 'createyandexpoint':
    default :
        $request = new WrongMethod();
}
$request->setData ( $_GET );
$request->execute ();

print_r ( json_encode ( $request->result () ) );
?>