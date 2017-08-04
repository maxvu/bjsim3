<?php

namespace maxvu\bjsim3;

class Turn {

    protected $player;
    protected $openHands;
    protected $closedHands;
    protected $insuranceBet;

    public function __construct ( Player & $player, Amount $initialBet ) {
        $this->player = $player;
        $this->openHands = [ new Hand( [], [ $initialBet ] ) ];
        $this->closedHands = [];
        $this->insuranceBet = new Amount( 0.0 );
    }

    public function & getPlayer () {
        return $this->player;
    }

    public function getInsurance () {
        return $this->insuranceBet;
    }

    public function setInsurance ( Amount $insurance ) {
        $this->insuranceBet = $insurance;
        return $this;
    }

    public function pushHand ( Hand $hand ) {
        $this->openHands[] = $hand;
        return $this;
    }

    public function isOver () {
        return sizeof( $this->openHands ) === 0;
    }

    public function & getHand () {
        return $this->openHands[ sizeof( $this->openHands ) - 1 ];
    }

    public function splitHand ( Card $a, Card $b ) {
        if ( $this->isOver() )
            throw new \Exception( "No hands to split!" );
        $originalHand = array_pop( $this->openHands );
        array_push(
            $this->openHands,
            new Hand(
                [ $originalHand->pop(), $a ],
                $originalHand->getBets()
            )
        );
        array_push(
            $this->openHands,
            new Hand(
                [ $originalHand->pop(), $b ],
                $originalHand->getBets()
            )
        );
        return $this;
    }

    public function nextHand () {
        if ( !$this->isOver() )
            array_push( $this->closedHands, array_pop( $this->openHands ) );
        return $this;
    }

    public function getOpenHands () {
        return $this->openHands;
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
