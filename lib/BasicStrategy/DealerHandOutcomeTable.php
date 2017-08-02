<?php

namespace maxvu\bjsim3\BasicStrategy;
use \maxvu\bjsim3\Rank as Rank;
use \maxvu\bjsim3\Hand as Hand;
use \maxvu\bjsim3\Shoe as Shoe;
use \maxvu\bjsim3\Dealer as Dealer;
use \maxvu\bjsim3\RuleSet as RuleSet;
use \maxvu\bjsim3\Settings as Settings;

class DealerHandOutcomeTable {

    public static function getAllOutcomes () {
        // 0 means 'bust' and 99 means 'blackjack'.
        // Numeric aliases chosen for ease of numeric comparison.
        return [ 0, 17, 18, 19, 20, 21, 99 ];
    }

    public static function generate (
        RuleSet $rules,
        Settings $settings,
        Shoe $shoe = null,
        int $iterations = 1000000
    ) {

        $table = [];

        $s17Stand = $rules[ 'dealer.s17-stand' ];
        $loseTies = $rules[ 'dealer.wins-ties' ];
        $peek10 = $rules[ 'dealer.peek-ten' ];
        $peekAce = $rules[ 'dealer.peek-ace' ];

        $maxPenetration = $settings[ 'shoe.penetration' ];
        if ( $shoe === null )
            $shoe = new Shoe( $rules[ 'game.deck-count' ] );

        $allRanks = array_map( function ( $rank ) {
            return Rank::getLowValue( $rank );
        }, Rank::getAll() );
        $allDealerHandOutcomes = DealerHandOutcomeTable::getAllOutcomes();

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
                if ( $upCard->isTenCard() && $peek10 ) {
                    $iterations++;
                } else if ( $upCard->getRank() === Rank::ACE && $peekAce ) {
                    $iterations++;
                } else {
                    $table[ Rank::getLowValue( $upCard->getRank() ) ][ 99 ]++;
                }
                continue;
            }
            $hand = Dealer::playHand( $hand, $s17Stand, $shoe );
            $result = $hand->getBestValue();
            $table[ Rank::getLowValue( $upCard->getRank() ) ][ $result ]++;
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

        return new DealerHandOutcomeTable (
            $rules,
            $table
        );

    }

    protected $table;
    protected $rules;

    private function __construct (
        RuleSet $rules,
        $DealerHandOutcomeTable
    ) {
        $this->rules = $rules;
        $this->table = $DealerHandOutcomeTable;
    }

    private function rankToLoValue ( $rank ) {
        if ( !Rank::isValid( $rank ) )
            throw new \Exception( "Invalid rank: $upCardRank." );
        return Rank::getLowValue( $rank );
    }

    private function validatePlayerTotal ( $playerTotal ) {
        if ( !in_array( $playerTotal, range( 4, 21 ) ) )
            throw new \Exception( "Invalid player hand total: $playerTotal." );
        return $playerTotal;
    }

    public function getProbabilityOfBlackjack ( $upCardRank ) {
        $upCardLo = $this->rankToLoValue( $upCardRank );
        return $this->table[ $upCardLo ][ 99 ];
    }

    public function getProbabilityOfWin ( $upCardRank, $playerTotal ) {
        $upCardLo = $this->rankToLoValue( $upCardRank );
        $this->validatePlayerTotal( $playerTotal );
        $p = 0;
        foreach ( DealerHandOutcomeTable::getAllOutcomes() as $dealerTotal ) {
            $upCardLo = Rank::getLowValue( $upCardRank );
            if ( $playerTotal > $dealerTotal )
                $p += $this->table[ $upCardLo ][ $dealerTotal ];
        }
        return $p;
    }

    public function getProbabilityOfTie ( $upCardRank, $playerTotal ) {
        $upCardLo = $this->rankToLoValue( $upCardRank );
        $this->validatePlayerTotal( $playerTotal );
        $p = 0;
        foreach ( DealerHandOutcomeTable::getAllOutcomes() as $dealerTotal ) {
            $upCardLo = Rank::getLowValue( $upCardRank );
            if ( $playerTotal === $dealerTotal )
                $p += $this->table[ $upCardLo ][ $dealerTotal ];
        }
        return $p;
    }

    public function getProbabilityOfLoss ( $upCardRank, $playerTotal ) {
        $upCardLo = $this->rankToLoValue( $upCardRank );
        $this->validatePlayerTotal( $playerTotal );
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
