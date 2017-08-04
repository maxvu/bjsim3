<?php

namespace maxvu\bjsim3;

class Dealer {

    // TODO: merge these two (make DealerHandOutcomeTable use the second one)

    public static function playHand (
        Hand $hand,
        bool $soft17Stand,
        Shoe &$shoe
    ) {
        while ( !$hand->isBust() ) {
            if ( $hand->isHard() ) {
                if ( $hand->getBestValue() >= 17 ) break;
            } else {
                if ( $hand->getBestValue() === 17 && $soft17Stand ) break;
                if ( $hand->getBestValue() > 17 ) break;
            }
            $hand->push( $shoe->draw() );
        }
        return $hand;
    }

    public static function decideHand ( Hand $hand, bool $soft17stand ) {
        if ( $hand->getBestValue() > 17 ) {
            return HandDecision::STAND;
        } else if ( $hand->getBestValue() === 17 ) {
            if ( $hand->isSoft() && $soft17stand )
                return HandDecision::STAND;
        }
        return HandDecision::HIT;
    }

};
