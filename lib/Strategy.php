<?php

namespace maxvu\bjsim3;

interface Strategy {

    public function onCard ( Card $card );
    public function onShuffle ();

    public function decideHand ( Round $round );
    public function decideBet ( $table );
    public function decideInsurance ( $hand, $upCard );

};
