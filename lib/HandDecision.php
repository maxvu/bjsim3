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

    public function isValid ( $decision ) {
        return in_array( $decision, HandDecision::getAll() );
    }

};
