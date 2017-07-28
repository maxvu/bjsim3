<?php
namespace maxvu\bjsim3;

class Dealer {

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

};
