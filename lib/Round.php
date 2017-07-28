<?php

namespace maxvu\bjsim3;

class Round {

    protected $table;
    protected $dealerHand;
    protected $turns;

    public function __construct ( Table &$table ) {
        $this->table = $table;
        $this->dealerHand = new Hand;
        $this->turns = [];
    }

    public function dealHands () {

    }

    public function solicitInsurance () {

    }

    public function peekDealerBlackjack () {

    }

    public function resolveInsurance () {

    }

    public function playHands () {

    }

    public function playDealerHand () {

    }

    public function resolveBets () {

    }

};
