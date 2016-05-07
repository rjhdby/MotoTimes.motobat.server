<?php
ini_set ( 'display_errors', 'On' );
ini_set ( 'display_startup_errors', 'On' );
header ( 'Content-Type: text/html; charset=utf-8' );
if ( isset( $_GET[ 'm' ] ) ) {
    $_GET [ 'method' ] = $_GET [ 'm' ];
}
if ( isset ( $_GET [ 'method' ] ) ) {
    $_POST = $_GET;
}

function __autoload ( $class_name ) {
    require_once 'class/' . $class_name . '.class.php';
}

switch ( mb_strtolower ( $_POST [ 'method' ] ) ) {
    case 'getrole':
        $response = new User( $_POST );
        $response->readRole ();
        break;
    case 'setrole':
        $response = new User( $_POST );
        $response->setRole ();
        break;
    case 'getpolice':
        $response = new GetPolice();
        $response->getList ();
        break;
    case 'getobjects':
        $response = new GetObjects();
        $response->getObjects ();
        break;
    case 'create':
        $response = new CreatePoint( $_POST );
        break;
    case 'cpk':
        $response = new Point( $_POST );
        $response->changeKarma ();
        break;
    case 'createyandexpoint':
        $response = new CreateYandexPoint( $_POST );
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