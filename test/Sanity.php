<?php
require_once( __DIR__ . '/Test.php' );

foreach ( [

    (new Test( "1 + 1 === 2", function () {
        return 1 + 1;
    } ))->eq( 2 ),

    (new Test( "exception", function () {
        throw new \Exception();
    } ))->excepts()

] as $test ) {
    runConsoleTest( $test );
}
