<?php

namespace maxvu\bjsim3;

class Amount {

    protected $amount;

    public function __construct ( float $amt, float $round = 0.5 ) {
        $this->amount = $amt - fmod( $amt, $round );
    }

    public function isZero () {
        return $this->amount === 0.0 || $this->amount === 0;
    }

    public function get () {
        return $this->amount;
    }

    public function add ( Amount $other ) {
        return Amount( $this->amount + $other->amount, $this->round );
    }

    public function sub ( Amount $other ) {
        return Amount( $this->amount - $other->amount, $this->round );
    }

    public function mul ( Amount $other ) {
        return Amount( $this->amount * $other->amount, $this->round );
    }

    public function div ( Amount $other ) {
        return Amount( $this->amount / $other->amount, $this->round );
    }

    public function giveTo ( Amount &$recip ) {
        $recip->add( $this->amount );
        $this->amount = 0.0;
        return $this;
    }

};
