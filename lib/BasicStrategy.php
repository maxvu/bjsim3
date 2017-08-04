<?php

namespace maxvu\bjsim3;

class BasicStrategy implements Strategy {

    protected $rules;
    protected $hitTable;
    protected $standTable;
    protected $doubleTable;
    protected $splitTable;
    protected $identifier;

    public function __construct (
        RuleSet $rules,
        Settings $settings,
        int $iterations = 1000000
    ) {
        $this->rules = $rules;
        $dealerTable = BasicStrategy\DealerHandOutcomeTable::generate(
            $rules,
            $settings,
            new Shoe( $rules[ 'game.deck-count' ] ),
            $iterations
        );
        $this->standTable = BasicStrategy\StandValueTable::generate(
            $dealerTable,
            $rules
        );
        $this->hitTable = BasicStrategy\HitValueTable::generate(
            $dealerTable,
            $this->standTable,
            $rules,
            $settings,
            new Shoe( $rules[ 'game.deck-count' ] )
        );
        $this->doubleTable = BasicStrategy\DoubleValueTable::generate(
            $this->standTable,
            $rules,
            new Shoe( $rules[ 'game.deck-count' ] )
        );
        $this->splitTable = BasicStrategy\SplitValueTable::generate(
            $this->hitTable,
            $this->standTable,
            $this->doubleTable,
            $rules,
            new Shoe( $rules[ 'game.deck-count' ] )
        );
        $this->identifer = "basic-strategy-i{$iterations}";
    }

    public function onCard ( Card $card ) {

    }

    public function onShuffle () {

    }


    public function decideHand (
        HandOption $options,
        Hand $hand,
        Card $upCard
    ) {
        return $this->getBestOption( $options, $hand, $upCard );
    }

    public function getBestOption (
        HandOption $options,
        Hand $hand,
        Card $upCard
    ) {
        $candidates = [
            [
                HandDecision::HIT,
                $this->hitTable->getEV(
                   $upCard,
                   $hand
               )
            ],
            [
                HandDecision::STAND,
                $this->standTable->getEV(
                    $upCard->getRank(),
                    $hand->getBestValue()
                )
            ]
        ];
        if ( $options->canDouble() ) {
            $candidates[] = [
                HandDecision::DOUBLEDOWN,
                $hand->isHard()
                    ? $this->doubleTable->getEVHard(
                        $upCard->getRank(),
                        $hand->getBestValue()
                    )
                    : $this->doubleTable->getEVSoft(
                        $upCard->getRank(),
                        $hand->getBestValue()
                    )
            ];
        }
        if ( $options->canSplit() ) {
            $candidates[] = [
                HandDecision::SPLIT,
                $this->splitTable->getEV(
                    $upCard->getRank(),
                    $hand->getCards()[ 0 ]->getRank()
                )
            ];
        }
        if ( $options->canSurrender() ) {
            $candidates[] = [
                HandDecision::SURRENDER,
                -0.5
            ];
        }
        usort( $candidates, function ( $a, $b ) {
            list( $optA, $evA ) = $a;
            list( $optB, $evB ) = $b;
            return $evA <=> $evB;
        } );
        list( $bestOption, $ev ) = array_pop( $candidates );
        return $bestOption;
    }

    public function decideBet ( Table $table ) {
        return $table->getSettings()[ 'bet.min' ];
    }

    public function decideInsurance ( Turn $turn, Card $upCard ) {
        return null;
    }

    public function getTables () {
        $hards = [];
        $softs = [];
        $splits = [];

        foreach ( Rank::getAll() as $upCardRank ) {
            $upCardLo = Rank::getLowValue( $upCardRank );
            $hards[ $upCardLo ] = [];
            $softs[ $upCardLo ] = [];
            $splits[ $upCardLo ] = [];
            foreach ( range( 4, 20 ) as $hardTotal ) {
                $evHit = $this->hitTable->getEVHard(
                    $upCardRank,
                    $hardTotal
                );
                $evStand = $this->standTable->getEV(
                    $upCardRank,
                    $hardTotal
                );
                $evDouble = $this->doubleTable->getEVHard(
                    $upCardRank,
                    $hardTotal
                );
                if ( $evHit >= $evStand && $evHit >= $evDouble ) {
                    if ( $evHit < -0.5 )
                        $hards[ $upCardLo ][ $hardTotal ] = 'Rh';
                    $hards[ $upCardLo ][ $hardTotal ] = 'H';
                } else if ( $evStand >= $evHit && $evStand >= $evDouble ) {
                    if ( $evStand < -0.5 )
                        $hards[ $upCardLo ][ $hardTotal ] = 'Rs';
                    $hards[ $upCardLo ][ $hardTotal ] = 'S';
                } else {
                    if ( $evStand > $evHit )
                        $hards[ $upCardLo ][ $hardTotal ] = 'Ds';
                    else
                        $hards[ $upCardLo ][ $hardTotal ] = 'Dh';
                }
            }
            foreach ( range( 13, 20 ) as $softTotal ) {
                $evHit = $this->hitTable->getEVSoft(
                    $upCardRank,
                    $softTotal
                );
                $evStand = $this->standTable->getEV(
                    $upCardRank,
                    $softTotal
                );
                $evDouble = $this->doubleTable->getEVSoft(
                    $upCardRank,
                    $softTotal
                );
                if ( $evHit >= $evStand && $evHit >= $evDouble ) {
                    if ( $evHit < -0.5 )
                        $hards[ $upCardLo ][ $softTotal ] = 'Rh';
                    $softs[ $upCardLo ][ $softTotal ] = 'H';
                } else if ( $evStand >= $evHit && $evStand >= $evDouble ) {
                    if ( $evStand < -0.5 )
                        $softs[ $upCardLo ][ $softTotal ] = 'Rs';
                    $softs[ $upCardLo ][ $softTotal ] = 'S';
                } else {
                    if ( $evStand > $evHit )
                        $softs[ $upCardLo ][ $softTotal ] = 'Ds';
                    else
                        $softs[ $upCardLo ][ $softTotal ] = 'Dh';
                }
            }
            foreach ( Rank::getAll() as $splitCardRank ) {
                $originalHand = new Hand([
                    new Card( Suit::CLUBS, $splitCardRank ),
                    new Card( Suit::CLUBS, $splitCardRank )
                ]);
                $evHit = -2;
                $evStand = -2;
                $evDouble = -2;
                $evSplit = -2;
                $splitCardLo = Rank::getLowValue( $splitCardRank );
                if ( $originalHand->isHard() ) {
                    $evHit = $this->hitTable->getEVHard(
                        $upCardRank,
                        $originalHand->getBestValue()
                    );
                    $evDouble = $this->doubleTable->getEVHard(
                        $upCardRank,
                        $originalHand->getBestValue()
                    );
                } else {
                    $evHit = $this->hitTable->getEVSoft(
                        $upCardRank,
                        $originalHand->getBestValue()
                    );
                    $evDouble = $this->doubleTable->getEVSoft(
                        $upCardRank,
                        $originalHand->getBestValue()
                    );
                }
                $evStand = $this->standTable->getEV(
                    $upCardRank,
                    $originalHand->getBestValue()
                );
                $evSplit = $this->splitTable->getEV(
                    $upCardRank,
                    $splitCardRank
                );
                if ( $evHit >= max( $evStand, $evDouble, $evSplit ) ) {
                    $splits[ $upCardLo ][ $splitCardLo ] = 'H';
                } else if ( $evStand > max( $evHit, $evDouble, $evSplit ) ) {
                    $splits[ $upCardLo ][ $splitCardLo ] = 'S';
                } else if ( $evDouble > max( $evHit, $evStand, $evSplit ) ) {
                    if ( $evStand > $evHit )
                        $splits[ $upCardLo ][ $splitCardLo ] = 'Ds';
                    else
                        $splits[ $upCardLo ][ $splitCardLo ] = 'Dh';
                } else {
                    $splits[ $upCardLo ][ $splitCardLo ] = 'P';
                }
            }
        }

        return [
            'hards' => $hards,
            'softs' => $softs,
            'splits' => $splits
        ];
    }

    public function getIdentifier () : string {
        return $this->identifer;
    }

};
