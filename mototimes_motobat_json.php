<?php
ini_set ( 'display_errors', 'On' );
ini_set ( 'display_startup_errors', 'On' );
header ( 'Content-Type: text/html; charset=utf-8' );
if ( isset ( $_GET [ 'method' ] ) ) {
    $_POST = $_GET;
}

function __autoload ( $class_name ) {
    require_once 'class/' . $class_name . '.class.php';
}

switch ( mb_strtolower ( $_POST [ 'method' ] ) ) {
    case 'getrole':
        $response = new Role( $_POST );
        $response->readRole ();
        break;
    case 'setrole':
        $response = new Role( $_POST );
        $response->setRole ();
        break;
    case 'getlist':
        $response = new GetList();
        $response->getlist ();
        break;
    case 'create':
        $response = new CreatePoint( $_POST );
        break;
    default :
        $response = new WrongMethod( $_POST );
}
if ( $response->isError () ) {
    $result = array (
        'ERROR' => array (
            'text' => $response->getErrorText (),
            'object' => $response->getErrorObject ()
        )
    );
} else {
    $result = array ( 'RESULT' => $response->getResult () );
}
print_r ( json_encode ( $result ) );
//var_dump($result);
?>