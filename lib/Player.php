<?php

namespace maxvu\bjsim3;

class Player {

    protected $name;
    protected $bankroll;
    protected $strategy;

    public function __construct (
        string $name,
        float $initBank,
        Strategy\Strategy $strategy
    ) {
        $this->name = $name;
        $this->bankroll = new Amount( $initBank );
        $this->strategy = $strategy;
    }

    public function getName () { return $this->name; }
    public function getBankroll () { return $this->bankroll; }
    public function getStrategy () { return $this->strategy; }

    public function give ( Amount $amt ) {
        $this->bankroll->add( $amt );
        return $this;
    }

    public function take ( Amount $amt ) {
        $this->bankroll->sub( $amt );
        return $this;
    }

    public function canAfford ( Amount $bet ) {
        return $this->bankroll->ge( $bet );
    }


};
