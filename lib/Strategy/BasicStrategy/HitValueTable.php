<?php

namespace maxvu\bjsim3\Strategy\BasicStrategy;

use \maxvu\bjsim3\Rank as Rank;
use \maxvu\bjsim3\Hand as Hand;
use \maxvu\bjsim3\Card as Card;
use \maxvu\bjsim3\Dealer as Dealer;
use \maxvu\bjsim3\Shoe as Shoe;
use \maxvu\bjsim3\RuleSet as RuleSet;
use \maxvu\bjsim3\Settings as Settings;

class HitValueTable {

    public static function getHardHandTotals () {
        return array_reverse( range( 4, 21 ) );
    }

    public static function getSoftHandTotals () {
        return array_reverse( range( 13, 21 ) );
    }

    public static function generate (
        DealerHandOutcomeTable $dealerTable,
        StandValueTable $standTable,
        RuleSet $rules,
        Settings $settings,
        Shoe $shoe = null
    ) {

        if ( $shoe === null )
            $shoe = new Shoe( $rules[ 'game.deck-count' ] );
        $count = $shoe->getCount();

        /*
            Initialize all tables.
        */

        $hards = [];
        $softs = [];

        foreach ( range( 1, 10 ) as $upCardLo ) {
            $hards[ $upCardLo ] = [];
            foreach ( HitValueTable::getHardHandTotals() as $hardHand ) {
                $hards[ $upCardLo ][ $hardHand ] = 0;
            }
            $softs[ $upCardLo ] = [];
            foreach ( HitValueTable::getSoftHandTotals() as $softHand ) {
                $softs[ $upCardLo ][ $softHand ] = 0;
            }
            $splits[ $upCardLo ] = [];
            foreach ( range( 1, 10 ) as $splitCardLo ) {
                $splits[ $upCardLo ][ $splitCardLo ] = 0;
            }
        }

        /*
            Base case: hitting on a hard 21 is a guaranteed loss.
        */

        foreach ( range( 1, 10 ) as $upCardLo ) {
            $hards[ $upCardLo ][ 21 ] = -1;
        }

        /*
            Fill in the hard table, in reverse, from 20 to 11.
        */

        foreach ( Rank::getAll() as $upCardRank ) {
            $upCardLo = Rank::getLowValue( $upCardRank );
            foreach ( array_reverse( range( 11, 20 ) ) as $hardTotal ) {
                $ev = 0;
                foreach ( Rank::getAll() as $hitCardRank ) {
                    $hitCardLo = Rank::getLowValue( $hitCardRank );
                    $hitCardP = $count->getIncidenceByRank( $hitCardRank );
                    $hitHandTotal = $hardTotal + $hitCardLo;
                    if ( $hitHandTotal > 21 ) {
                        $ev -= $hitCardP;
                        continue;
                    }
                    $hardLookup = $hards[ $upCardLo ][ $hitHandTotal ] ?? -1;
                    $standLookup = $standTable->getEV(
                        $upCardRank,
                        $hitHandTotal
                    );
                    $ev += $hitCardP * max(
                        $hards[ $upCardLo ][ $hitHandTotal ] ?? -1,
                        $standTable->getEV(
                            $upCardRank,
                            $hitHandTotal
                        )
                    );
                }
                $hards[ $upCardLo ][ $hardTotal ] = $ev;
            }
        }

        /*
            Fill in the soft table.
        */

        foreach ( Rank::getAll() as $upCardRank ) {
            $upCardLo = Rank::getLowValue( $upCardRank );
            foreach ( array_reverse( range( 12, 21 ) ) as $softTotal ) {
                $ev = 0;
                foreach ( Rank::getAll() as $hitCardRank ) {
                    $hitCardLo = Rank::getLowValue( $hitCardRank );
                    $hitCardP = $count->getIncidenceByRank( $hitCardRank );
                    $hitHandTotal = $softTotal + $hitCardLo;
                    if ( $hitHandTotal > 21 ) {
                        $hitHandTotal -= 10;
                        $hardLookup = $hards[ $upCardLo ][ $hitHandTotal ] ?? -1;
                        $standLookup = $standTable->getEV(
                            $upCardRank,
                            $hitHandTotal
                        );
                        $ev += $hitCardP * max( $hardLookup, $standLookup );
                    } else {
                        $softLookup = $softs[ $upCardLo ][ $hitHandTotal ] ?? -1;
                        $standLookup = $standTable->getEV(
                            $upCardRank,
                            $hitHandTotal
                        );
                        $ev += $hitCardP * max( $softLookup, $standLookup );
                    }
                }
                $softs[ $upCardLo ][ $softTotal ] = $ev;
            }
        }

        /*
            Fill in the rest of the hard table: 2 to 11.
        */

        foreach ( Rank::getAll() as $upCardRank ) {
            $upCardLo = Rank::getLowValue( $upCardRank );
            foreach ( array_reverse( range( 2, 11 ) ) as $hardTotal ) {
                $ev = 0;
                foreach ( Rank::getAll() as $hitCardRank ) {
                    $hitCardLo = Rank::getLowValue( $hitCardRank );
                    $hitCardP = $count->getIncidenceByRank( $hitCardRank );
                    if ( $hitCardRank === Rank::ACE ) {
                        if ( $hardTotal + 11 > 21 ) {
                            $ev += $hitCardP * max(
                                $hards[ $upCardLo ][ $hardTotal + 1 ],
                                $standTable->getEV(
                                    $upCardRank,
                                    $hitHandTotal
                                )
                            );
                        } else {
                            $ev += $hitCardP * max(
                                $softs[ $upCardLo ][ $hardTotal + 11 ],
                                $standTable->getEV(
                                    $upCardRank,
                                    $hitHandTotal
                                )
                            );
                        }
                    } else {
                        $hitHandTotal = $hardTotal + $hitCardLo;
                        $ev += $hitCardP * max(
                            $standTable->getEV(
                                $upCardRank,
                                $hitHandTotal
                            ),
                            $hards[ $upCardLo ][ $hitHandTotal ]
                        );
                    }
                }
                $hards[ $upCardLo ][ $hardTotal ] = $ev;
            }
        }

        return new HitValueTable( $hards, $softs );

    }

    protected $evHitHard;
    protected $evHitSoft;

    private function __construct ( $evHitHard, $evHitSoft ) {
        $this->evHitHard = $evHitHard;
        $this->evHitSoft = $evHitSoft;
    }

    public function getEV ( Card $upCard, Hand $playerHand ) {
        $upCardLo = $upCard->getLowValue();
        $handTotal = $playerHand->getBestValue();
        if ( $playerHand->isSoft() ) {
            return $this->evHitSoft[ $upCardLo ][ $handTotal ];
        } else {
            return $this->evHitHard[ $upCardLo ][ $handTotal ];
        }
    }

    public function getEVHard ( $upCardRank, $handTotal ) {
        if ( !Rank::isValid( $upCardRank ) )
            throw new \Exception( "Invalid rank: $rank." );
        if ( !in_array( $handTotal, range( 4, 21 ) ) )
            throw new \Exception( "Invalid hand total $handTotal." );
        $upCardLo = Rank::getLowValue( $upCardRank );
        return $this->evHitHard[ $upCardLo ][ $handTotal ];
    }

    public function getEVSoft ( $upCardRank, $handTotal ) {
        if ( !Rank::isValid( $upCardRank ) )
            throw new \Exception( "Invalid rank: $rank." );
        if ( !in_array( $handTotal, range( 4, 21 ) ) )
            throw new \Exception( "Invalid hand total $handTotal." );
        $upCardLo = Rank::getLowValue( $upCardRank );
        return $this->evHitSoft[ $upCardLo ][ $handTotal ];
    }

    public function getHardTable () {
        return $this->evHitHard;
    }

    public function getSoftTable () {
        return $this->evHitSoft;
    }

};
