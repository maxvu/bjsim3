<?php
namespace maxvu\bjsim3;
require( 'vendor/autoload.php' );

// $shoe = new Shoe( 6 );
// for ( $i = 0; $i < 52; $i++ ) {
//     echo $shoe->draw() . " ";
// }
// echo "\npenetration " . $shoe->getPenetration() . "\n";

$table = new Table(
    new RuleSet(),
    new Settings(),
    [new Player(
        "BS-1",
        10000.00,
        new BasicStrategy()
    )]
);

// foreach ( BasicStrategy::generate( $table ) as $name => $table )
//     printTable( $table, $name );
// $timeBefore = microtime(true);
// BasicStrategy::generate( $table );
// $elapsed = microtime(true) - $timeBefore;
// echo "took {$elapsed} seconds\n";

$dealerHandTable = BasicStrategy\DealerHandOutcomeTable::generateMonteCarlo(
    new RuleSet(),
    new Settings(),
    new Shoe( 6 )
);

$standTable = BasicStrategy\StandValueTable::generateMonteCarlo(
    $dealerHandTable,
    new RuleSet()
);

echo (new \maxvu\bjsim3\Util\Table( $standTable->getTable() ))->toTSV();

echo "ok.\n";
