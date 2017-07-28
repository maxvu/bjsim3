<?php
namespace maxvu\bjsim3;
require_once( __DIR__ . '/Test.php' );
require_once( __DIR__ . '/../vendor/autoload.php' );


foreach ( [

    (new \Test( "Amount init", function () {
        return (new Amount( 1.5 ))->get();
    } ))->eq( 1.5 ),

    (new \Test( "Amount round", function () {
        return (new Amount( 1.25, 0.50 ))->get();
    } ))->eq( 1.0 ),

] as $test ) {
    runConsoleTest( $test );
}
