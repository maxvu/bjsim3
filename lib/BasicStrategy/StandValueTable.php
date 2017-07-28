<?php

namespace maxvu\bjsim3\BasicStrategy;
use \maxvu\bjsim3\Rank as Rank;
use \maxvu\bjsim3\RuleSet as RuleSet;

class StandValueTable {

    public static function generateMonteCarlo (
        DealerHandOutcomeTable $dealerTable,
        RuleSet $rules
    ) {

        $evStand = [];
        $loseTies = $rules[ 'dealer.wins-ties' ];
        $handTotals = range( 4, 21 );
        $allRanks = array_map( function ( $rank ) {
            return Rank::getLowValue( $rank );
        }, Rank::getAll() );
        $dealerTotals = DealerHandOutcomeTable::getAllOutcomes();

        foreach ( $allRanks as $upCardRank ) {
            $evStand[ $upCardRank ] = [];
            foreach ( $handTotals as $handTotal ) {
                $evStand[ $upCardRank ][ $handTotal ] = 0;
            }
        }

        foreach ( Rank::getAll() as $upCardRank ) {
            foreach ( $handTotals as $handTotal ) {
                $ev = 0;
                $ev += $dealerTable->getProbabilityOfWin(
                    $upCardRank,
                    $handTotal
                );
                $ev -= $dealerTable->getProbabilityOfLoss(
                    $upCardRank,
                    $handTotal
                );
                if ( $loseTies ) {
                    $ev -= $dealerTable->getProbabilityOfTie(
                        $upCardRank,
                        $handTotal
                    );
                }
                $upCardLo = Rank::getLowValue( $upCardRank );
                $evStand[ $upCardLo ][ $handTotal ] = $ev;
            }
        }

        return new StandValueTable( $evStand );

    }

    protected $table;

    private function __construct ( $standValueTable ) {
        $this->table = $standValueTable;
    }

    public function getTable () {
        return $this->table;
    }

};
