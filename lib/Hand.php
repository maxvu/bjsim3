<?php

namespace maxvu\bjsim3;

class Hand {

    protected $cards;
    protected $bets;

    public function __construct ( $initCards = [], $initBets = [] ) {
        if ( !is_array( $initCards ) )
            $initCards = [ $initCards ];
        foreach ( $initCards as $card ) {
            if ( !is_a( $card, 'maxvu\\bjsim3\\Card' ) ) {
                print_r( $initCards );
                throw new \Exception( "Invalid Hand initialization." );
            }
        }

        $this->cards = $initCards;
        $this->bets = $initBets;
    }

    public function push ( Card $card ) {
        $this->cards[] = $card;
        return $this;
    }

    public function pop () {
        if ( sizeof( $this->cards ) === 0 )
            throw new \Exception( "Tried popping on an empty Hand." );
        return array_pop( $this->cards );
    }

    public function getCards () {
        return $this->cards;
    }

    public function getBets () {
        return $this->bets;
    }

    public function addBet ( Amount $bet ) {
        $this->bets[] = $bet;
    }

    public function getLowValue () {
        $value = 0;
        foreach ( $this->cards as $card ) {
            switch ( $card->getRank() ) {
                case Rank::ACE   : $value += 1;  break;
                case Rank::TWO   : $value += 2;  break;
                case Rank::THREE : $value += 3;  break;
                case Rank::FOUR  : $value += 4;  break;
                case Rank::FIVE  : $value += 5;  break;
                case Rank::SIX   : $value += 6;  break;
                case Rank::SEVEN : $value += 7;  break;
                case Rank::EIGHT : $value += 8;  break;
                case Rank::NINE  : $value += 9;  break;
                case Rank::TEN   : $value += 10; break;
                case Rank::JACK  : $value += 10; break;
                case Rank::QUEEN : $value += 10; break;
                case Rank::KING  : $value += 10; break;
            }
        }
        return $value;
    }

    public function getHighValue () {
        $value = 0;
        $haveSeenAnAce = false;
        foreach ( $this->cards as $card ) {
            switch ( $card->getRank() ) {
                case Rank::ACE:
                    $value += $haveSeenAnAce ? 1 : 11;
                    $haveSeenAnAce = true;
                break;
                case Rank::TWO   : $value += 2;  break;
                case Rank::THREE : $value += 3;  break;
                case Rank::FOUR  : $value += 4;  break;
                case Rank::FIVE  : $value += 5;  break;
                case Rank::SIX   : $value += 6;  break;
                case Rank::SEVEN : $value += 7;  break;
                case Rank::EIGHT : $value += 8;  break;
                case Rank::NINE  : $value += 9;  break;
                case Rank::TEN   : $value += 10; break;
                case Rank::JACK  : $value += 10; break;
                case Rank::QUEEN : $value += 10; break;
                case Rank::KING  : $value += 10; break;
            }
        }
        return $value;
    }

    public function getBestValue () {
        $hi = $this->getHighValue();
        $lo = $this->getLowValue();
        if ( $hi <= 21 )
            return $hi;
        if ( $lo <= 21 )
            return $lo;
        return 0;
    }

    public function isSoft () {
        return $this->getHighValue() != $this->getLowValue();
    }

    public function isHard () {
        return !$this->isSoft();
    }

    public function isBust () {
        return $this->getLowValue() > 21;
    }

    public function is21 () {
        return $this->getBestValue() === 21;
    }

    public function isPair ( $allTensEquivalent = false ) {
        if ( sizeof( $this->openHands ) !== 2 )
            return false;
        $a = $this->openHands[ 0 ];
        $b = $this->openHands[ 1 ];
        if ( $a->getRank() == $b->getRank() )
            return true;
        if ( $allTensEquivalent && $a->isTenCard() && $b->isTenCard() )
            return true;
        return false;
    }

    public function copy () {
        return new Hand( $this->cards, $this->bets );
    }

    public function __toString () {
        return '( ' . implode( ', ', $this->cards ) . ' )';
    }

};
