<?php
namespace maxvu\bjsim3;
use \maxvu\bjsim3\Util\Table as DataTable;
use \maxvu\bjsim3\Report\ReadoutReport as ReadoutReport;
require( 'vendor/autoload.php' );

$rules = new RuleSet();
$settings = new Settings();
$shoe = new Shoe( $rules[ 'game.deck-count' ] );

$table = new Table(
    new RuleSet(),
    new Settings([
        'bet.min' => 5.0
    ])
);

$basicStrategy = new BasicStrategy( $rules, $settings, 1000 );

$playerA = new Player(
    "DUMMY-A",
    100000.00,
    $basicStrategy
);

$playerB = new Player(
    "DUMMY-B",
    100000.00,
    $basicStrategy
);

$table->addPlayer( $playerA );
$table->addPlayer( $playerB );
$table->addReport( new ReadoutReport() );

for ( $i = 0; $i < 10; $i++ ) {
    $table->playRound();
}
