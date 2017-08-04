<?php

namespace maxvu\bjsim3\Strategy;

use \maxvu\bjsim3\Card as Card;
use \maxvu\bjsim3\Turn as Turn;
use \maxvu\bjsim3\Table as Table;
use \maxvu\bjsim3\Hand as Hand;
use \maxvu\bjsim3\HandOption as HandOption;

interface Strategy {

    public function onCard ( Card $card );
    public function onShuffle ();

    public function decideHand (
        HandOption $options,
        Hand $hand,
        Card $upCard
    );
    public function decideBet ( Table $table );
    public function decideInsurance ( Turn $turn, Card $upCard );

    public function getIdentifier () : string;

};
