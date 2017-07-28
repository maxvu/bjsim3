<?php

namespace maxvu\bjsim3;

class Shoe {

    protected $undrawn;
    protected $discard;

    public function __construct ( $deckCount ) {
        $deckCount = intval( $deckCount );
        if ( $deckCount < 1 )
            throw new \Exception( "Empty shoe." );
        $this->discard = [];
        $this->undrawn = [];
        for ( $i = 0; $i < $deckCount; $i++ ) {
            foreach ( Suit::getAll() as $suit ) {
                foreach ( Rank::getAll() as $rank ) {
                    $this->undrawn[] = new Card( $suit, $rank );
                }
            }
        }
        $this->reshuffle();
    }

    public function reshuffle () {
        $this->undrawn = array_merge( $this->discard, $this->undrawn );
        $this->discard = [];
        shuffle( $this->undrawn );
        return $this;
    }

    public function getPenetration () {
        $nDiscard = sizeof( $this->discard );
        $nUndrawn = sizeof( $this->undrawn );
        return $nDiscard / ( $nDiscard + $nUndrawn );
    }

    public function isExhausted () {
        return sizeof( $this->undrawn ) == 0;
    }

    public function draw () {
        if ( $this->isExhausted() )
            $this->reshuffle();
        $drawn = array_pop( $this->undrawn );
        array_push( $this->discard, $drawn );
        return $drawn;
    }

    public function getDiscardStack () {
        return $this->discard;
    }

    public function getUndrawnStack () {
        return $this->discard;
    }

    public function getCount () {
        $counts = [];
        foreach ( range( 1, 10 ) as $lowValue )
            $counts[ $lowValue ] = 0;
        foreach ( $this->undrawn as $card )
            $counts[ $card->getLowValue() ]++;
        return new ShoeCount( $counts );
    }

    public function copy () {
        $copy = new Shoe( 1 );
        $copy->undrawn = $this->undrawn;
        $copy->discard = $this->discard;
        return $copy;
    }

};
