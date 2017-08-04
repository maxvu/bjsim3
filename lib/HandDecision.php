<?php

namespace maxvu\bjsim3;

class HandDecision {

    const STAND = 1;
    const HIT = 2;
    const DOUBLEDOWN = 4;
    const SPLIT = 8;
    const SURRENDER = 16;

    public static function getAll () {
        return [
            HandDecision::STAND,
            HandDecision::HIT,
            HandDecision::DOUBLEDOWN,
            HandDecision::SPLIT,
            HandDecision::SURRENDER
        ];
    }

    public static function toString ( $decision ) {
        switch ( $decision ) {
            case HandDecision::STAND: return 'stand'; break;
            case HandDecision::HIT: return 'hit'; break;
            case HandDecision::DOUBLEDOWN: return 'double'; break;
            case HandDecision::SPLIT: return 'split'; break;
            case HandDecision::SURRENDER: return 'surrender'; break;
            default: return "[[unknown hand decision]]"; break;
        }
    }

    public function isValid ( $decision ) {
        return in_array( $decision, HandDecision::getAll() );
    }

};
