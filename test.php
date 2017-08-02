<?php
namespace maxvu\bjsim3;
use \maxvu\bjsim3\Util\Table as DataTable;
require( 'vendor/autoload.php' );

// $shoe = new Shoe( 6 );
// for ( $i = 0; $i < 52; $i++ ) {
//     echo $shoe->draw() . " ";
// }
// echo "\npenetration " . $shoe->getPenetration() . "\n";
$timer = null;
// $table = new Table(
//     new RuleSet(),
//     new Settings(),
//     [new Player(
//         "BS-1",
//         10000.00,
//         null
//     )]
// );
$rules = new RuleSet();
$settings = new Settings();
$shoe = new Shoe( $rules[ 'game.deck-count' ] );

// foreach ( BasicStrategy::generate( $table ) as $name => $table )
//     printTable( $table, $name );
// $timeBefore = microtime(true);
// BasicStrategy::generate( $table );
// $elapsed = microtime(true) - $timeBefore;
// echo "took {$elapsed} seconds\n";

$timer = microtime( true );
$dealerHandTable = BasicStrategy\DealerHandOutcomeTable::generate(
    $rules,
    $settings,
    $shoe->copy(),
    10000000
);
$timer = microtime( true ) - $timer;
echo (new DataTable( $dealerHandTable->getTable() ))->toTSV();
echo "dealer hand table generated in $timer seconds\n";

$timer = microtime( true );
$standTable = BasicStrategy\StandValueTable::generate(
    $dealerHandTable,
    $rules
);
$timer = microtime( true ) - $timer;
echo "stand ev table generated in $timer seconds\n";
echo (new \maxvu\bjsim3\Util\Table( $standTable->getTable() ))->toTSV();

$timer = microtime( true );
$hitTable = BasicStrategy\HitValueTable::generate(
    $dealerHandTable,
    $standTable,
    $rules,
    $settings
);
$timer = microtime( true ) - $timer;
echo "hit ev table generated in $timer seconds\n";
echo (new \maxvu\bjsim3\Util\Table( $hitTable->getHardTable() ))->transpose()->toTSV();
echo (new \maxvu\bjsim3\Util\Table( $hitTable->getSoftTable() ))->transpose()->toTSV();

$timer = microtime( true );
$doubleTable = BasicStrategy\DoubleValueTable::generate(
    $standTable,
    $rules,
    $shoe->copy()
);
$timer = microtime( true ) - $timer;
echo "double table generated in $timer seconds\n";
echo (new \maxvu\bjsim3\Util\Table( $doubleTable->getHardTable() ))->transpose()->toTSV();
echo (new \maxvu\bjsim3\Util\Table( $doubleTable->getSoftTable() ))->transpose()->toTSV();

$timer = microtime( true );
$splitTable = BasicStrategy\SplitValueTable::generate(
    $hitTable,
    $standTable,
    $doubleTable,
    $rules,
    $shoe->copy()
);
$timer = microtime( true ) - $timer;
echo "split table generated in $timer seconds\n";
echo (new \maxvu\bjsim3\Util\Table( $splitTable->getTable() ))->transpose()->toTSV();

// $rules = new RuleSet([
//     'dealer.s17-stand' => false,
// ]);
// $settings = new Settings();
// $bs = new BasicStrategy( $rules, $settings, 100000 );
// foreach ( $bs->getTables() as $name => $table ) {
//     echo "$name:\n";
//     echo (new \maxvu\bjsim3\Util\Table( $table ))->transpose()->toTSV();
// }

echo "ok.\n";
