<?php

namespace maxvu\bjsim3;

abstract class Suit {

    const CLUBS = 1;
    const DIAMONDS = 2;
    const HEARTS = 4;
    const SPADES = 8;

    public static function getAll () {
        return [
            Suit::CLUBS,
            Suit::DIAMONDS,
            Suit::HEARTS,
            Suit::SPADES
        ];
    }

    public static function toString ( $suit ) {
        switch ( $suit ) {
            case Suit::CLUBS:    return '♣'; break;
            case Suit::DIAMONDS: return '♦'; break;
            case Suit::HEARTS:   return '♥'; break;
            case Suit::SPADES:   return '♥'; break;
            default: return '?'; break;
        }
    }

};
