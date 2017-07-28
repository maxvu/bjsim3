<?php

namespace maxvu\bjsim3;

class Turn {

    protected $player;
    protected $openHands;
    protected $closedHands;
    protected $insuranceBet;

    public function __construct ( Player & $player ) {
        $this->player = $player;
        $this->openHands = [];
        $this->closedHands = [];
        $this->insuranceBet = Amount( 0.0 );
    }

    public function &getInsurance () {
        return $this->insuranceBet;
    }

    public function pushHand ( Hand $hand ) {
        $this->openHands[] = $hand;
        return $this;
    }

    public function isOver () {
        return sizeof( $this->openHands ) === 0;
    }

    public function getHand () {
        if ( $this->isOver() )
            return null;
        return $this->openHands[ sizeof( $this->openHands ) - 1 ];
    }

    public function splitHand ( Card $a, Card $b ) {
        if ( $this->isOver() )
            throw new \Exception( "No hands to split!" );
        $originalHand = array_pop( $this->openHands );
        array_push( $this->openHands( $originalHand->pop(), $a ) );
        array_push( $this->openHands( $originalHand->pop(), $b ) );
        return $this;
    }

    public function nextHand () {
        if ( !$this->isOver() )
            array_push( $this->closedHands, array_pop( $this->openHands ) );
        return $this;
    }

    public function getAllHands () {
        return array_merge( $this->openHands, $this->closedHands );
    }

    public function isBlackjack () {
        $hands = $this->getAllHands();
        return sizeof( $hands ) == 1 && (array_pop( $hands ))->is21();
    }

    public function wasSplit () {
        return sizeof( $this->getAllHands() ) > 1;
    }

    public function getHandCount () {
        return sizeof( $this->openHands ) + sizeof( $this->closedHands );
    }

    public function getBetCount () {
        $betCount = 0;
        foreach ( $this->getAllHands() as $hand )
            $betCount += sizeof( $hand->getBets() );
        return $betCount;
    }

};
