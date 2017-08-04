<?php

namespace maxvu\bjsim3\Strategy\BasicStrategy;
use \maxvu\bjsim3\Rank as Rank;
use \maxvu\bjsim3\Hand as Hand;
use \maxvu\bjsim3\Dealer as Dealer;
use \maxvu\bjsim3\Shoe as Shoe;
use \maxvu\bjsim3\RuleSet as RuleSet;
use \maxvu\bjsim3\Settings as Settings;

class DoubleValueTable {

    public static function generate (
        StandValueTable $standTable,
        RuleSet $rules,
        Shoe $shoe = null
    ) {

        if ( $shoe === null )
            $shoe = new Shoe( $rules[ 'game.deck-count' ] );

        $count = $shoe->getCount();

        $hards = [];
        $softs = [];

        foreach ( Rank::getAll() as $upCardRank ) {
            $upCardLo = Rank::getLowValue( $upCardRank );
            foreach ( range( 4, 20 ) as $hardTotal ) {
                $hards[ $upCardLo ][ $hardTotal ] = 0;
            }
            $hards[ $upCardLo ][ 21 ] = -2;
            foreach ( range( 12, 20 ) as $softTotal ) {
                $softs[ $upCardLo ][ $hardTotal ] = 0;
            }
            $softs[ $upCardLo ][ 21 ] = -2;
        }

        foreach ( Rank::getAll() as $upCardRank ) {
            $upCardLo = Rank::getLowValue( $upCardRank );
            foreach ( range( 11, 20 ) as $hardTotal ) {
                $ev = 0;
                foreach ( Rank::getAll() as $doubleCardRank ) {
                    $doubleCardLo = Rank::getLowValue( $doubleCardRank );
                    $doubleCardP = $count->getIncidenceByRank( $doubleCardRank );
                    if ( $hardTotal + $doubleCardLo > 21 ) {
                        $ev += $doubleCardP * -2;
                    } else {
                        $evStand = $standTable->getEV(
                            $upCardRank,
                            $hardTotal + $doubleCardLo
                        );
                        $ev += $doubleCardP * 2 * $evStand;
                    }
                }
                $hards[ $upCardLo ][ $hardTotal ] = $ev;
            }
        }

        foreach ( Rank::getAll() as $upCardRank ) {
            $upCardLo = Rank::getLowValue( $upCardRank );
            foreach ( range( 12, 21 ) as $softTotal ) {
                $ev = 0;
                foreach ( Rank::getAll() as $doubleCardRank ) {
                    $doubleCardLo = Rank::getLowValue( $doubleCardRank );
                    $doubleCardP = $count->getIncidenceByRank( $doubleCardRank );
                    $doubleHandTotal = $softTotal + $doubleCardLo;
                    if ( $doubleHandTotal > 21 ) {
                        $doubleHandTotal -= 10;
                    }
                    $ev += $standTable->getEV(
                        $upCardRank,
                        $doubleHandTotal
                    ) * 2 * $doubleCardP;
                }
                $softs[ $upCardLo ][ $softTotal ] = $ev;
            }
        }

        foreach ( Rank::getAll() as $upCardRank ) {
            $upCardLo = Rank::getLowValue( $upCardRank );
            foreach ( range( 4, 10 ) as $hardTotal ) {
                $ev = 0;
                foreach ( Rank::getAll() as $doubleCardRank ) {
                    $doubleCardLo = Rank::getLowValue( $doubleCardRank );
                    $doubleCardP = $count->getIncidenceByRank( $doubleCardRank );
                    if ( $doubleCardRank === Rank::ACE && $hardTotal <= 10 ) {
                        $ev += $standTable->getEV(
                            $upCardRank,
                            $hardTotal + 11
                        ) * 2 * $doubleCardP;
                    } else {
                        $ev += $standTable->getEV(
                            $upCardRank,
                            $hardTotal + $doubleCardLo
                        ) * 2 * $doubleCardP;
                    }
                }
                $hards[ $upCardLo ][ $hardTotal ] = $ev;
            }
        }

        return new DoubleValueTable( $hards, $softs );

    }

    protected $hards;
    protected $softs;

    private function __construct ( array $hards, array $softs ) {
        $this->hards = $hards;
        $this->softs = $softs;
    }

    public function getEVHard ( $upCardRank, $handTotal ) {
        if ( !Rank::isValid( $upCardRank ) )
            throw new \Exception( "Invalid card rank: $rank." );
        $upCardLo = Rank::getLowValue( $upCardRank );
        return $this->hards[ $upCardLo ][ $handTotal ];
    }

    public function getEVSoft ( $upCardRank, $handTotal ) {
        if ( !Rank::isValid( $upCardRank ) )
            throw new \Exception( "Invalid card rank: $rank." );
        $upCardLo = Rank::getLowValue( $upCardRank );
        return $this->softs[ $upCardLo ][ $handTotal ];
    }

    public function getHardTable () {
        return $this->hards;
    }

    public function getSoftTable () {
        return $this->softs;
    }

};
