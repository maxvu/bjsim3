<?php

namespace maxvu\bjsim3\BasicStrategy;
use \maxvu\bjsim3\Rank as Rank;
use \maxvu\bjsim3\Hand as Hand;
use \maxvu\bjsim3\Dealer as Dealer;

class DealerHandOutcomeTable {

    public static function getAllOutcomes () {
        return [ 0, 17, 18, 19, 20, 21 ];
    }

    public static function generateMonteCarlo (
        \maxvu\bjsim3\RuleSet $rules,
        \maxvu\bjsim3\Settings $settings,
        \maxvu\bjsim3\Shoe $shoe,
        int $iterations = 1000000
    ) {

        $table = [];

        $s17Stand = $rules[ 'dealer.s17-stand' ];
        $loseTies = $rules[ 'dealer.wins-ties' ];
        $maxPenetration = $settings[ 'shoe.penetration' ];

        $allDealerHandOutcomes = [ 0, 17, 18, 19, 20, 21 ];
        $allRanks = array_map( function ( $rank ) {
            return Rank::getLowValue( $rank );
        }, Rank::getAll() );

        foreach ( $allRanks as $upCardValue ) {
            $table[ $upCardValue ] = [];
            foreach ( $allDealerHandOutcomes as $hands ) {
                $table[ $upCardValue ][ $hands ] = 0;
            }
        }

        $shoe->reshuffle();
        while ( $iterations-- ) {
            $downCard = $shoe->draw();
            $upCard = $shoe->draw();
            $hand = new Hand([ $downCard, $upCard ]);
            if ( $hand->is21() ) {
                $iterations--;
                continue;
            }
            $hand = Dealer::playHand( $hand, $s17Stand, $shoe );
            $result = $hand->getBestValue();
            $table[ Rank::getLowValue( $upCard->getRank() ) ][ $result ]++;
            $table[ Rank::getLowValue( $downCard->getRank() ) ][ $result ]++;
            if ( $shoe->getPenetration() >= $maxPenetration )
                $shoe->reshuffle();
        }

        foreach ( $allRanks as $rank ) {
            $rowTotal = 0;
            foreach ( $allDealerHandOutcomes as $dealerHand ) {
                $rowTotal += $table[ $rank ][ $dealerHand ];
            }
            foreach ( $allDealerHandOutcomes as $dealerHand ) {
                $table[ $rank ][ $dealerHand ] /= $rowTotal;
            }
        }

        return new DealerHandOutcomeTable ( $table );

    }

    protected $table;

    private function __construct ( $DealerHandOutcomeTable ) {
        $this->table = $DealerHandOutcomeTable;
    }

    public function getProbabilityOfWin ( $upCardRank, $playerTotal ) {
        if ( !Rank::isValid( $upCardRank ) )
            throw new \Exception( "Invalid rank: $upCardRank." );
        if ( !in_array( $playerTotal, range( 4, 21 ) ) )
            throw new \Exception( "Invalid player hand total: $playerTotal." );
        $p = 0;
        foreach ( DealerHandOutcomeTable::getAllOutcomes() as $dealerTotal ) {
            $upCardLo = Rank::getLowValue( $upCardRank );
            if ( $playerTotal > $dealerTotal )
                $p += $this->table[ $upCardLo ][ $dealerTotal ];
        }
        return $p;
    }

    public function getProbabilityOfTie ( $upCardRank, $playerTotal ) {
        if ( !Rank::isValid( $upCardRank ) )
            throw new \Exception( "Invalid rank: $upCardRank." );
        if ( !in_array( $playerTotal, range( 4, 21 ) ) )
            throw new \Exception( "Invalid player hand total: $playerTotal." );
        $p = 0;
        foreach ( DealerHandOutcomeTable::getAllOutcomes() as $dealerTotal ) {
            $upCardLo = Rank::getLowValue( $upCardRank );
            if ( $playerTotal === $dealerTotal )
                $p += $this->table[ $upCardLo ][ $dealerTotal ];
        }
        return $p;
    }

    public function getProbabilityOfLoss ( $upCardRank, $playerTotal ) {
        if ( !Rank::isValid( $upCardRank ) )
            throw new \Exception( "Invalid rank: $rank." );
        if ( !in_array( $playerTotal, range( 4, 21 ) ) )
            throw new \Exception( "Invalid player hand total: $playerTotal." );
        $p = 0;
        foreach ( DealerHandOutcomeTable::getAllOutcomes() as $dealerTotal ) {
            $upCardLo = Rank::getLowValue( $upCardRank );
            if ( $playerTotal < $dealerTotal )
                $p += $this->table[ $upCardLo ][ $dealerTotal ];
        }
        return $p;
    }

    public function getTable () {
        return $this->table;
    }

};
