<?php

namespace maxvu\bjsim3\Strategy\BasicStrategy;

use \maxvu\bjsim3\Rank as Rank;
use \maxvu\bjsim3\Suit as Suit;
use \maxvu\bjsim3\Card as Card;
use \maxvu\bjsim3\Hand as Hand;
use \maxvu\bjsim3\Shoe as Shoe;
use \maxvu\bjsim3\RuleSet as RuleSet;

class SplitValueTable {

    public static function generate (
        HitValueTable $hitTable,
        StandValueTable $standTable,
        DoubleValueTable $doubleTable,
        RuleSet $rules,
        Shoe $shoe = null
    ) {

        if ( $shoe === null )
            $shoe = new Shoe( $rules[ 'game.deck-count' ] );
        $count = $shoe->getCount();

        $splits = [];

        foreach ( Rank::getAll() as $upCardRank ) {
            $upCardLo = Rank::getLowValue( $upCardRank );
            $splits[ $upCardLo ] = [];
            foreach ( Rank::getAll() as $splitCardRank ) {
                $splitCardLo = Rank::getLowValue( $splitCardRank );
                $splits[ $upCardLo ][ $splitCardLo ] = 0;
            }
        }

        $getBestPlay = function (
            int $upCardRank,
            int $total,
            bool $isSoft
        ) use (
            $hitTable,
            $standTable,
            $doubleTable
        ) {
            $evHit = null;
            $evStand = null;
            $evDouble = null;
            if ( $isSoft ) {
                $evHit = $hitTable->getEVSoft( $upCardRank, $total );
                $evStand = $standTable->getEV( $upCardRank, $total );
                $evDouble = $doubleTable->getEVSoft( $upCardRank, $total );
            } else {
                $evHit = $hitTable->getEVHard( $upCardRank, $total );
                $evStand = $standTable->getEV( $upCardRank, $total );
                $evDouble = $doubleTable->getEVHard( $upCardRank, $total );
            }
            return max( $evHit, $evStand, $evDouble );
        };

        foreach ( Rank::getAll() as $upCardRank ) {
            foreach ( Rank::getEachValue() as $splitCardRank ) {
                $upCardLo = Rank::getLowValue( $upCardRank );
                $splitCardLo = Rank::getLowValue( $splitCardRank );
                $splitHand = new Hand([
                    new Card( Suit::CLUBS, $splitCardRank )
                ]);
                $ev = 0;
                foreach ( Rank::getAll() as $drawCardRank ) {
                    $drawCardP = $count->getIncidenceByRank( $drawCardRank );
                    $drawCard = new Card( Suit::CLUBS, $drawCardRank );
                    $hand = $splitHand->copy()->push( $drawCard );
                    if ( $hand->isBust() ) {
                        $ev += ( -2 * $drawCardP );
                    } else if ( $splitCardRank === Rank::ACE ) {
                        $ev += ( $standTable->getEV(
                            $upCardRank,
                            $hand->getBestValue()
                        ) * 2 * $drawCardP );
                    } else {
                        $ev += ( $getBestPlay(
                            $upCardRank,
                            $hand->getBestValue(),
                            $hand->isSoft()
                        ) * 2 * $drawCardP );
                    }
                }
                $splits[ $upCardLo ][ $splitCardLo ] = $ev;
            }
        }


        return new SplitValueTable( $splits );

    }

    protected $splits;

    private function __construct ( $splits ) {
        $this->splits = $splits;
    }

    public function getTable () {
        return $this->splits;
    }

    public function getEV ( $upCardRank, $splitCardRank ) {
        if ( !Rank::isValid( $upCardRank ) )
            throw new \Exception( "Invalid rank: $upCardRank." );
        if ( !Rank::isValid( $splitCardRank ) )
            throw new \Exception( "Invalid rank: $splitCardRank." );
        $upCardLo = Rank::getLowValue( $upCardRank );
        $splitCardLo = Rank::getLowValue( $splitCardRank );
        return $this->splits[ $upCardLo ][ $splitCardLo ];
    }

};
