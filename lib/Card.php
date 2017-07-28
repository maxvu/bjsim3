<?php

namespace maxvu\bjsim3;

class Card {

    protected $suit;
    protected $rank;

    public function __construct ( $suit, $rank ) {
        if ( !in_array( $suit, Suit::getAll() ) )
            throw new \Exception( "Invalid suit $suit." );
        if ( !in_array( $rank, Rank::getAll() ) )
            throw new \Exception( "Invalid rank $rank." );
        $this->suit = $suit;
        $this->rank = $rank;
    }

    public function getSuit () {
        return $this->suit;
    }

    public function getRank () {
        return $this->rank;
    }

    public function getLowValue () {
        switch ( $this->rank ) {
            case Rank::ACE:   return   1;  break;
            case Rank::TWO:   return   2;  break;
            case Rank::THREE: return   3;  break;
            case Rank::FOUR:  return   4;  break;
            case Rank::FIVE:  return   5;  break;
            case Rank::SIX:   return   6;  break;
            case Rank::SEVEN: return   7;  break;
            case Rank::EIGHT: return   8;  break;
            case Rank::NINE:  return   9;  break;
            case Rank::TEN:   return  10;  break;
            case Rank::JACK:  return  10;  break;
            case Rank::QUEEN: return  10;  break;
            case Rank::KING:  return  10;  break;
        }
    }

    public function getHighValue () {
        switch ( $this->rank ) {
            case Rank::ACE:   return  11;  break;
            case Rank::TWO:   return   2;  break;
            case Rank::THREE: return   3;  break;
            case Rank::FOUR:  return   4;  break;
            case Rank::FIVE:  return   5;  break;
            case Rank::SIX:   return   6;  break;
            case Rank::SEVEN: return   7;  break;
            case Rank::EIGHT: return   8;  break;
            case Rank::NINE:  return   9;  break;
            case Rank::TEN:   return  10;  break;
            case Rank::JACK:  return  10;  break;
            case Rank::QUEEN: return  10;  break;
            case Rank::KING:  return  10;  break;
        }
    }

    public function __toString () {
        return Rank::toString( $this->rank ) . Suit::toString( $this->suit );
    }

    public function isTenCard () {
        switch ( $this->rank ) {
            case Rank::TEN:
            case Rank::JACK:
            case Rank::QUEEN:
            case Rank::KING:
                return true;
            break;
            default:
                return false;
            break;
        }
    }

};
