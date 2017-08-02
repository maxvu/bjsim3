<?php

namespace maxvu\bjsim3;

abstract class Rank {

    const ACE = 1;
    const TWO = 2;
    const THREE = 3;
    const FOUR = 4;
    const FIVE = 5;
    const SIX = 6;
    const SEVEN = 7;
    const EIGHT = 8;
    const NINE = 9;
    const TEN = 10;
    const JACK = 11;
    const QUEEN = 12;
    const KING = 13;

    public static function getAll () {
        return [
            Rank::ACE,
            Rank::TWO,
            Rank::THREE,
            Rank::FOUR,
            Rank::FIVE,
            Rank::SIX,
            Rank::SEVEN,
            Rank::EIGHT,
            Rank::NINE,
            Rank::TEN,
            Rank::JACK,
            Rank::QUEEN,
            Rank::KING
        ];
    }

    public static function getTenCards () {
        return [
            Rank::TEN,
            Rank::JACK,
            Rank::QUEEN,
            Rank::KING
        ];
    }

    public static function getEachValue () {
        return [
            Rank::ACE,
            Rank::TWO,
            Rank::THREE,
            Rank::FOUR,
            Rank::FIVE,
            Rank::SIX,
            Rank::SEVEN,
            Rank::EIGHT,
            Rank::NINE,
            Rank::TEN
        ];
    }

    public static function toString ( $rank ) {
        switch ( $rank ) {
            case Rank::ACE:   return 'A';  break;
            case Rank::TWO:   return '2';  break;
            case Rank::THREE: return '3';  break;
            case Rank::FOUR:  return '4';  break;
            case Rank::FIVE:  return '5';  break;
            case Rank::SIX:   return '6';  break;
            case Rank::SEVEN: return '7';  break;
            case Rank::EIGHT: return '8';  break;
            case Rank::NINE:  return '9';  break;
            case Rank::TEN:   return '10'; break;
            case Rank::JACK:  return 'J';  break;
            case Rank::QUEEN: return 'Q';  break;
            case Rank::KING:  return 'K';  break;
            default: return '?'; break;
        }
    }

    public static function getLowValue ( $rank ) {
        switch ( $rank ) {
            case Rank::ACE:   return 1;  break;
            case Rank::TWO:   return 2;  break;
            case Rank::THREE: return 3;  break;
            case Rank::FOUR:  return 4;  break;
            case Rank::FIVE:  return 5;  break;
            case Rank::SIX:   return 6;  break;
            case Rank::SEVEN: return 7;  break;
            case Rank::EIGHT: return 8;  break;
            case Rank::NINE:  return 9;  break;
            case Rank::TEN:   return 10; break;
            case Rank::JACK:  return 10;  break;
            case Rank::QUEEN: return 10;  break;
            case Rank::KING:  return 10;  break;
            default:
                throw new \Exception( "Invalid rank: $rank." );
            break;
        }
    }

    public static function getHighValue ( $rank ) {
        switch ( $rank ) {
            case Rank::ACE:   return 11;  break;
            case Rank::TWO:   return 2;  break;
            case Rank::THREE: return 3;  break;
            case Rank::FOUR:  return 4;  break;
            case Rank::FIVE:  return 5;  break;
            case Rank::SIX:   return 6;  break;
            case Rank::SEVEN: return 7;  break;
            case Rank::EIGHT: return 8;  break;
            case Rank::NINE:  return 9;  break;
            case Rank::TEN:   return 10; break;
            case Rank::JACK:  return 10;  break;
            case Rank::QUEEN: return 10;  break;
            case Rank::KING:  return 10;  break;
            default:
                throw new \Exception( "Invalid rank: $rank." );
            break;
        }
    }

    public static function isValid ( $rank ) {
        return in_array( $rank, Rank::getAll() );
    }

};
