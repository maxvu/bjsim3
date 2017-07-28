<?php

namespace maxvu\bjsim3;

class BasicStrategy implements Strategy {

    static function printTable ( $table, $name = '' ) {
        $columns = [];
        foreach ( $table as $row ) {
            foreach ( $row as $col => $val ) {
                $columns[ $col ] = 1;
            }
        }
        echo "$name\t";
        foreach ( $columns as $colName => $cell )
            echo "$colName\t";
        echo "\n";
        foreach ( $table as $rowLabel => $row ) {
            echo "$rowLabel\t";
            foreach ( $row as $cell ) {
                echo "$cell\t";
            }
            echo "\n";
        }
    }

    const DEALEROUTCOMES_ITERATIONS = 10000;
    const HITOUTCOMES_ITERATIONS = 10000;

    public static function generate ( Table $table ) {

        /*

        */

        $shoe = $table->getShoe()->copy();
        $s17Stand = $table->getRules()[ 'dealer.s17-stand' ];
        $maxPenetration = $table->getSettings()[ 'shoe.penetration' ];
        $loseTies = $table->getRules()[ 'dealer.wins-ties' ];

        /*

        */

        $allRanks = range( 1, 10 );
        $allHandTotals = array_merge( [ 0 ], range( 2, 21 ) );
        $allDealerHands = [ 0, 17, 18, 19, 20, 21 ];

        $pDealer = [];
        $evStand = [];
        $evHitHard = [];
        $evHitSoft = [];
        $evSplit = [];

        foreach ( $allRanks as $rank ) {
            $pDealer[ $rank ] = [];
            foreach ( $allDealerHands as $dealerHand ) {
                $pDealer[ $rank ][ $dealerHand ] = 0;
            }
            foreach ( $allHandTotals as $handTotal ) {
                $evStand[ $rank ][ $handTotal ] = 0;
                $evHitHard[ $rank ][ $handTotal ] = 0;
                $evHitSoft[ $rank ][ $handTotal ] = 0;
            }
            foreach ( $allRanks as $pairHandRank ) {
                $evSplit[ $rank ][ $pairHandRank ] = 0;
            }
        }

        $shoe->reshuffle();
        for ( $i = 0; $i < BasicStrategy::DEALEROUTCOMES_ITERATIONS; $i++ ) {
            $downCard = $shoe->draw();
            $upCard = $shoe->draw();
            $hand = new Hand([ $downCard, $upCard ]);
            if ( $hand->is21() ) {
                $i--;
                continue;
            }
            $hand = Dealer::playHand( $hand, $s17Stand, $shoe );
            $result = $hand->getBestValue();
            $pDealer[ Rank::getLowValue( $upCard->getRank() ) ][ $result ]++;
            $pDealer[ Rank::getLowValue( $downCard->getRank() ) ][ $result ]++;
            if ( $shoe->getPenetration() >= $maxPenetration )
                $shoe->reshuffle();
        }

        foreach ( $allRanks as $rank ) {
            $rowTotal = 0;
            foreach ( $allDealerHands as $dealerHand ) {
                $rowTotal += $pDealer[ $rank ][ $dealerHand ];
            }
            foreach ( $allDealerHands as $dealerHand ) {
                $pDealer[ $rank ][ $dealerHand ] /= $rowTotal;
            }
        }

        $shoe->reshuffle();
        foreach ( [ 17 ] as $rank ) {
            $ev =& $evStand[ $rank ];
            $pd =& $pDealer[ $rank ];
            foreach ( range( 16, 21 ) as $myHand ) {
                foreach ( [ 17, 18, 19, 20, 21 ] as $dealerHand ) {
                    echo "rank $rank, hand $myHand, dealer $dealerHand: ";
                    if ( $myHand > $dealerHand ) {
                        $evStand[ $rank ][ $myHand ] += $pd[ $rank ][ $dealerHand ];
                    } else if ( $myHand === $dealerHand ) {
                        if ( $loseTies )
                            $evStand[ $rank ][ $myHand ] += $pd[ $rank ][ $dealerHand ];
                    } else {
                        $evStand[ $rank ][ $myHand ] += $pd[ $rank ][ $dealerHand ];
                    }
                }
            }
        }

        echo "=====\n\n\n\n=====\n";

        BasicStrategy::printTable( $pDealer, 'pDealer' );
        BasicStrategy::printTable( $evStand, 'evStand' );
        // BasicStrategy::printTable( $evHitHard, 'evHitHard' );
        // BasicStrategy::printTable( $evHitSoft, 'evHitSoft' );
        // BasicStrategy::printTable( $evSplit, 'evSplit' );


    }

    public function __construct () {

    }

    public function onCard ( Card $card ) {

    }

    public function onShuffle () {

    }


    public function decideHand ( Round $round ) {

    }

    public function decideBet ( $table ) {

    }

    public function decideInsurance ( $hand, $upCard ) {

    }


};
