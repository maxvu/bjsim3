<?php
namespace maxvu\bjsim3;
use \maxvu\bjsim3\Util\Table as DataTable;
use \maxvu\bjsim3\Report\ReadoutReport as ReadoutReport;
use \maxvu\bjsim3\Strategy\BasicStrategy\BasicStrategy as BasicStrategy;
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
    "BASIC-A",
    100000.00,
    $basicStrategy
);

$playerB = new Player(
    "BASIC-B",
    100000.00,
    $basicStrategy
);

$playerC = new Player(
    "BASIC-C",
    100000.00,
    $basicStrategy
);

$playerD = new Player(
    "BASIC-D",
    100000.00,
    $basicStrategy
);

$table->addPlayer( $playerA );
$table->addPlayer( $playerB );
$table->addPlayer( $playerC );
$table->addPlayer( $playerD );
$table->addReport( new ReadoutReport() );

for ( $i = 0; $i < 20; $i++ ) {
    $table->playRound();
}
