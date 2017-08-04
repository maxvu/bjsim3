<?php

namespace maxvu\bjsim3;

interface Strategy {

    public function onCard ( Card $card );
    public function onShuffle ();

    public function decideHand ( HandOption $options, Hand $hand );
    public function decideBet ( Table $table );
    public function decideInsurance ( Turn $turn, Card $upCard );

    public function getIdentifier () : string;

};
