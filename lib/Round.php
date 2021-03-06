<?php

namespace maxvu\bjsim3;

class Round {

    protected $table;
    protected $dealerHand;
    protected $turns;

    public function __construct ( Table &$table ) {
        $this->table = $table;
        $this->dealerHand = new Hand;
        $this->turns = [];
    }

    public function getTurns () {
        return $this->turns;
    }

    public function getTurn ( Player $player ) {
        foreach ( $this->turns as $turn ) {
            if ( $turn->getPlayer() === $player )
                return $turn;
        }
        throw new \Exception( "Couldn't find a turn for player." );
    }

    public function getUpCard () {
        return $this->dealerHand->getCards()[ 1 ];
    }

    public function dealCard ( $showTable = true ) {
        $card = $this->table->getShoe()->draw();
        if ( $showTable ) {
            foreach ( $this->turns as $turn ) {
                $turn->getPlayer()->getStrategy()->onCard( $card );
            }
        }
        return $card;
    }

    public function allPlayersBust () {
        foreach ( $this->turns as $turn ) {
            foreach ( $turn->getAllHands() as $hand ) {
                if ( !$hand->isBust() ) {
                    return false;
                }
            }
        }
        return true;
    }

    public function play () {
        if ( $this->table->shouldShuffle() ) {
            $this->table->getReport()->onShuffle( $this );
            $this->table->getShoe()->reshuffle();
        }
        $this->table->getReport()->onRoundBegin( $this );
        $this->solicitBets();
        if ( !sizeof( $this->turns ) )
            return $this;
        $this->table->getReport()->onBetsPlaced( $this );
        $this->dealHands();
        $this->table->getReport()->onHandsDealt( $this );

        $peekAce = (
            $this->table->getRules()[ 'dealer.peek-ace' ] &&
            $this->getUpCard()->getRank() === Rank::ACE
        );
        $peekTen = (
            $this->table->getRules()[ 'dealer.peek-ten' ] &&
            $this->getUpCard()->isTenCard()
        );
        if ( $peekTen || $peekAce ) {
            $this->solicitInsurance();
            if ( $this->dealerHand->isBlackjack() ) {
                $this->table->getReport()->onPeek( $this, true );
                $this->resolveInsurance();
                foreach ( $this->turns as $turn ) {
                    $this->resolveTurn( $turn );
                }
                $this->table->getReport()->onRoundEnd( $this );
                return $this;
            } else {
                $this->table->getReport()->onPeek( $this, false );
            }
        }
        foreach ( $this->turns as $turn ) {
            $this->playTurn( $turn );
        }
        if ( !$this->allPlayersBust() ) {
            $this->playDealerHand();
        }
        $this->table->getReport()->onHandsPlayed( $this );
        foreach ( $this->turns as $turn ) {
            $this->resolveTurn( $turn );
        }
        $this->resolveInsurance();
        $this->table->getReport()->onRoundEnd( $this );
        return $this;
    }

    public function solicitBets () {
        foreach ( $this->table->getPlayers() as $player ) {
            $bet = Amount::convert(
                $player->getStrategy()->decideBet( $this->table )
            );
            if ( $bet->gt( 0.0 ) ) {
                $player->take( $bet );
                $this->turns[] = (new Turn( $player, $bet ));
                $this->table->getReport()->onBetPlace( $this, $player, $bet );
            }
        }
        return $this;
    }

    public function dealHands () {
        foreach ( $this->turns as $turn ) {
            $turn->getHand()->push( $this->dealCard() );
        }
        $this->dealerHand->push( $this->dealCard( false ) );
        foreach ( $this->turns as $turn ) {
            $turn->getHand()->push( $this->dealCard() );
        }
        $this->dealerHand->push( $this->dealCard() );
        return $this;
    }

    public function solicitInsurance () {
        $this->table->getReport()->onSolicitInsurance( $this );
        foreach ( $this->turns as $turn ) {
            $insurance = Amount::convert(
                $turn->getPlayer()->getStrategy()->decideInsurance(
                    $turn,
                    $this->getUpCard()
                )
            );
            $handBet = $turn->getHand()->getBets()[ 0 ];
            if ( $insurance->ge( $handBet->div( 2.0 ) ) ) {
                throw new \Exception(
                    "Bet $insurance exceeds half of original bet $handBet."
                );
            }
            if ( $insurance !== null && $insurance->get() > 0.0 ) {
                $turn->getPlayer()->take( $insurance );
                $turn->setInsurance( $insurance );
            }
        }
        $this->table->getReport()->onInsuranceBetsPlaced( $this );
        return $this;
    }

    public function resolveInsurance () {
        foreach ( $this->turns as $turn ) {
            if ( ( $bet = $turn->getInsurance() )->gt( 0.0 ) ) {
                if ( $this->dealerHand->isBlackjack() ) {
                    $payout = $bet->mul( 2.0 );
                    $turn->getPlayer()->give( $bet->mul( 2.0 ) );
                    $this->table->getReport()->onBetPayout(
                        $this,
                        $turn->getPlayer(),
                        $bet,
                        $payout
                    );
                } else {
                    $this->table->getReport()->onBetClaim(
                        $this,
                        $turn->getPlayer(),
                        $bet
                    );
                }
                $turn->getInsurance = new Amount( 0.0 );
            }
        }
        return $this;
    }

    public function getHandOptions ( Turn $turn ) {
        list(
            $doubleAfterSplit,
            $doubleRestrict,
            $maxBets,
            $maxSplits,
            $surrender,
            $splitUnlikeTens,
            $onFirstPlay,
            $haveSplit
        ) = [
            $this->table->getRules()[ 'hand.double.after-split' ],
            $this->table->getRules()[ 'hand.double.restrict' ],
            $this->table->getRules()[ 'hand.bet.max' ],
            $this->table->getRules()[ 'hand.split.max' ],
            $this->table->getRules()[ 'hand.surrender' ],
            $this->table->getRules()[ 'hand.split.unlike-tens' ],
            $turn->getHand()->getSize() === 2,
            $turn->getHandCount() > 1
        ];

        $canDouble = (
            $turn->getBetCount() < $maxBets &&
            ( !$haveSplit || $doubleAfterSplit ) &&
            $onFirstPlay
        );

        $canSplit = (
            $turn->getHand()->isPair( $splitUnlikeTens ) &&
            $turn->getBetCount() < $maxBets &&
            $turn->getHandCount() - 1 < $maxSplits
        );

        $canSurrender = $surrender && $onFirstPlay;
        return new HandOption( $canDouble, $canSplit, $canSurrender );
    }

    public function playTurn ( Turn $turn ) {
        $this->table->getReport()->onTurnBegin( $this, $turn );
        if ( $turn->getHand()->isBlackjack() ) {
            $this->table->getReport()->onHandBegin(
                $this,
                $turn->getPlayer(),
                $turn->getHand()
            );
            return $this;
        }
        while ( !$turn->isOver() ) {
            $hand = $turn->getHand();
            $handOptions = $this->getHandOptions( $turn );
            $decision = $turn->getPlayer()->getStrategy()->decideHand(
                $handOptions,
                $turn->getHand(),
                $this->getUpCard()
            );
            if ( $hand->isFirstPlay() ) {
                $this->table->getReport()->onHandBegin(
                    $this,
                    $turn->getPlayer(),
                    $turn->getHand()
                );
            }
            if ( !HandDecision::isValid( $decision ) )
                throw new \Exception( "Invalid HandDecision: $decision" );
            $initialBet = $turn->getHand()->getBets()[ 0 ] ?? null;
            if ( $decision === HandDecision::HIT ) {
                $turn->getHand()->push( $this->dealCard() );
            } else if ( $decision === HandDecision::DOUBLEDOWN ) {
                if ( !$handOptions->canDouble() )
                    throw new \Exception( "Illegal double attempted." );
                if ( !$turn->getPlayer()->canAfford( $initialBet ) )
                    throw new \Exception( "NSF enough to double down." );
                $turn->getPlayer()->take( $initialBet );
                $turn->getHand()->addBet( $initialBet );
                $this->table->getReport()->onBetPlace(
                    $this,
                    $turn->getPlayer(),
                    $initialBet
                );
                $turn->getHand()->push( $this->dealCard() );
            } else if ( $decision === HandDecision::SPLIT ) {
                if ( !$handOptions->canSplit() )
                    throw new \Exception( "Illegal split attempted." );
                if ( !$turn->getPlayer()->canAfford( $initialBet ) )
                    throw new \Exception( "NSF enough to split." );
                $turn->getPlayer()->take( $initialBet );
                $this->table->getReport()->onBetPlace(
                    $this,
                    $turn->getPlayer(),
                    $initialBet
                );
                $turn->splitHand(
                    $this->dealCard(),
                    $this->dealCard()
                );
            } else if ( $decision === HandDecision::SURRENDER ) {
                if ( !$handOptions->canSurrender() )
                    throw new \Exception( "Illegal surrender attempted." );
                $turn->getPlayer()->give( $initialBet->div(
                    new Amount( 2.0 )
                ) );
            }
            $this->table->getReport()->onHandPlay(
                $this,
                $turn->getPlayer(),
                $turn->getHand(),
                $decision
            );
            if (
                $decision === HandDecision::STAND ||
                $decision === HandDecision::SURRENDER ||
                $hand->isBust()
            ) {
                $turn->nextHand();
            }
        }
        $this->table->getReport()->onTurnEnd( $this, $turn );
    }

    public function playDealerHand () {
        $this->table->getReport()->onDealerHandBegin(
            $this,
            $this->dealerHand
        );
        while ( !$this->dealerHand->isBust() ) {
            $decision = Dealer::decideHand(
                $this->dealerHand,
                $this->table->getRules()[ 'dealer.s17-stand' ]
            );
            if ( $decision === HandDecision::STAND ) {
                $this->table->getReport()->onDealerHandPlay(
                    $this,
                    $this->dealerHand,
                    $decision
                );
                break;
            }
            $this->dealerHand->push( $this->dealCard() );
            $this->table->getReport()->onDealerHandPlay(
                $this,
                $this->dealerHand,
                $decision
            );
        }
        $this->table->getReport()->onDealerHandEnd(
            $this,
            $this->dealerHand
        );
        return $this;
    }

    public function resolveTurn ( Turn $turn ) {
        $blackjackPayout = $this->table->getRules()[ 'game.blackjack-payout' ];
        $loseTies = $this->table->getRules()[ 'dealer.wins-ties' ];
        $dealerHand = $this->dealerHand;
        foreach ( $turn->getAllHands() as $hand ) {
            $myBest = $hand->getBestValue();
            $dealerBest = $dealerHand->getBestValue();
            $myBj = $hand->isBlackjack();
            $dealerBj = $dealerHand->isBlackjack();
            $outcomeLoss = (
                $hand->isBust() ||
                ( $myBest < $dealerBest ) ||
                ( $loseTies && ( $myBest === $dealerBest ) ) ||
                ( $dealerBj && !$myBj )
            );
            $outcomeBlackjack = $myBj && !$dealerBj;
            $outcomePush = (
                !$outcomeLoss && !$outcomeBlackjack && (
                    ( $myBj && $dealerBj ) ||
                    ( !$loseTies && ( $myBest === $dealerBest ) )
                )
            );
            $outcomeWin = (
                !$outcomeLoss &&
                !$outcomeBlackjack &&
                $myBest > $dealerBest
            );

            // sanity check: only one of these outcomes, right?
            $outcomesTriggered = 0;
            foreach ( [
                $outcomeLoss,
                $outcomeWin,
                $outcomeBlackjack,
                $outcomePush
            ] as $outcome ) {
                if ( $outcome )
                    $outcomesTriggered++;
            }
            if ( $outcomesTriggered !== 1 ) {
                throw new \Exception(
                    "Multiple outcomes seen: $hand vs. $dealerHand\n"
                );
            }

            if ( $outcomeBlackjack ) {
                $bet = $hand->getBets()[ 0 ];
                $payout = $bet->mul( $blackjackPayout );
                $turn->getPlayer()->give( $bet )->give( $payout );
                $this->table->getReport()->onHandBlackjack(
                    $turn,
                    $hand,
                    $dealerHand,
                    $bet,
                    $payout
                );
                $this->table->getReport()->onBetPayout(
                    $this,
                    $turn->getPlayer(),
                    $bet,
                    $payout
                );
            } else if ( $outcomeWin ) {
                $totalBet = new Amount( 0.0 );
                foreach ( $hand->getBets() as $bet ) {
                    $totalBet = $totalBet->add( $bet );
                    $payout = $bet;
                    $turn->getPlayer()->give( $bet )->give( $payout );
                    $this->table->getReport()->onBetPayout(
                        $this,
                        $turn->getPlayer(),
                        $bet,
                        $payout
                    );
                }
                $this->table->getReport()->onHandWin(
                    $turn,
                    $hand,
                    $dealerHand,
                    $totalBet
                );
            } else if ( $outcomePush ) {
                $totalBet = new Amount( 0.0 );
                foreach ( $hand->getBets() as $bet ) {
                    $totalBet = $totalBet->add( $bet );
                    $turn->getPlayer()->give( $bet );
                }
                $this->table->getReport()->onHandPush(
                    $turn,
                    $hand,
                    $dealerHand,
                    $totalBet
                );
            } else {
                $totalBet = new Amount( 0.0 );
                foreach ( $hand->getBets() as $bet ) {
                    $totalBet = $totalBet->add( $bet );
                    $this->table->getReport()->onBetClaim(
                        $this,
                        $turn->getPlayer(),
                        $bet
                    );
                }
                $this->table->getReport()->onHandLoss(
                    $turn,
                    $hand,
                    $dealerHand,
                    $totalBet
                );
            }
        }
    }

};
