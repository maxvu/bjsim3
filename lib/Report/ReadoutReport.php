<?php

namespace maxvu\bjsim3\Report;

use \maxvu\bjsim3\Amount as Amount;
use \maxvu\bjsim3\Hand as Hand;
use \maxvu\bjsim3\Turn as Turn;
use \maxvu\bjsim3\Rank as Rank;
use \maxvu\bjsim3\Round as Round;
use \maxvu\bjsim3\Player as Player;
use \maxvu\bjsim3\HandDecision as HandDecision;

class ReadoutReport extends Report {

    private function printHand ( Hand $hand ) {
        if ( $hand->isBlackjack() )
            return 'blackjack';
        if ( $hand->isBust() )
            return 'bust';
        if ( $hand->isPair( false ) )
            return 'a pair of ' . Rank::toString(
                $hand->getCards()[ 0 ]->getRank()
            ) . 's: ' . $hand;
        return sprintf(
            "%s%d %s",
            $hand->isSoft() ? 'soft ' : '',
            $hand->getBestValue(),
            $hand
        );
    }

    public function __construct () {

    }

    public function onRoundBegin ( Round $round ) {
        echo "round begin\n";
    }

    public function onBetsPlaced ( Round $round ) {
        echo "all bets placed\n";
    }

    public function onHandsDealt ( Round $round ) {
        printf(
            "dealer shows a %s\n",
            $round->getUpCard()
        );
    }

    public function onSolicitInsurance ( Round $round ) {

    }

    public function onInsuranceBetsPlaced ( Round $round ) {
        echo "all insurance bets placed.\n";
        foreach ( $round->getTurns() as $turn ) {
            if ( $turn->getInsurance()->gt( 0.0 ) ) {
                printf(
                    "%s insures for %.2f\n",
                    $turn->getPlayer()->getName(),
                    $turn->getInsurance()->get()
                );
            } else {
                printf(
                    "%s refuses insurance.\n",
                    $turn->getPlayer()->getName()
                );
            }
        }
    }

    public function onPeek ( Round $round, bool $dealerBlackjack ) {
        if ( !$dealerBlackjack ) {
            echo "dealer peeks -- no blackjack.\n";
        } else {
            echo "dealer peeks blackjack.\n";
            foreach ( $round->getTurns() as $turn ) {
                $playerName = $turn->getPlayer()->getName();
                $hand = $turn->getHand();
                if ( $turn->getHand()->isBlackjack() ) {
                    echo "{$playerName} pushes with their own blackjack: $hand.\n";
                } else {
                    echo "{$playerName} loses with a $hand.\n";
                }
            }
        }
    }

    public function onBetPlace (
        Round $round,
        Player $player,
        Amount $bet
    ) {
        printf( "%s bets %.2f\n", $player->getName(), $bet->get() );
    }

    public function onBetClaim (
        Round $round,
        Player $player,
        Amount $originalBet
    ) {

    }

    public function onBetPayout (
        Round $round,
        Player $player,
        Amount $originalBet,
        Amount $totalPayout
    ) {

    }

    public function onHandBegin (
        Round $round,
        Player $player,
        Hand $hand
    ) {
        if ( $hand->isBlackjack() ) {
            printf(
                "%s is dealt blackjack: %s\n",
                $player->getName(),
                $hand
            );
        }
        printf(
            "%s opens with a %s\n",
            $player->getName(),
            $this->printHand( $hand )
        );
    }

    public function onHandPlay (
        Round $round,
        Player $player,
        Hand $handAfter,
        int $play
    ) {
        switch ( $play ) {
            case HandDecision::STAND:
                printf(
                    "%s stands on a %s\n",
                    $player->getName(),
                    $this->printHand( $handAfter )
                );
            break;
            case HandDecision::HIT:
                printf(
                    "%s hits, getting a %s and leaving %s\n",
                    $player->getName(),
                    $handAfter->getCards()[ $handAfter->getSize() - 1 ],
                    $this->printHand( $handAfter )
                );
            break;
            case HandDecision::DOUBLEDOWN:
                printf(
                    "%s doubles, getting a %s and leaving %s\n",
                    $player->getName(),
                    $handAfter->getCards()[ $handAfter->getSize() - 1 ],
                    $this->printHand( $handAfter )
                );
            break;
            case HandDecision::SPLIT:
                $hands = $round->getTurn( $player )->getOpenHands();
                $handA = $hands[ sizeof( $hands ) - 1 ];
                $handB = $hands[ sizeof( $hands ) - 2 ];
                printf(
                    "%s splits, leaving %s and %s\n",
                    $player->getName(),
                    $this->printHand( $handA ),
                    $this->printHand( $handB )
                );
            break;
            case HandDecision::SURRENDER:
                printf(
                    "%s surrenders\n",
                    $player->getName()
                );
            break;
        }
    }

    public function onDealerHandBegin ( Round $round, Hand $hand ) {
        printf(
            "dealer reveals downcard %s\n",
            $hand->getCards()[ 0 ]
        );
    }

    public function onDealerHandPlay (
        Round $round,
        Hand $handAfter,
        int $play
    ) {
        if ( $play === HandDecision::HIT ) {
            printf(
                "dealer hits, gets %s, leaving %s\n",
                $handAfter->getCards()[ $handAfter->getSize() - 1 ],
                $this->printHand( $handAfter )
            );
        } else {
            printf(
                "dealer stands on a %s\n",
                $this->printHand( $handAfter )
            );
        }

    }

    public function onHandBlackjack (
        Turn $turn,
        Hand $player,
        Hand $dealer,
        Amount $bet,
        Amount $payout
    ) {
        printf(
            "%s wins blackjack %s, winning %.2f\n",
            $turn->getPlayer()->getName(),
            $player,
            $payout->get()
        );
    }

    public function onHandWin (
        Turn $turn,
        Hand $player,
        Hand $dealer,
        Amount $bet
    ) {
        printf(
            "%s's %s beats dealer's %s, winning %.2f\n",
            $turn->getPlayer()->getName(),
            $this->printHand( $player ),
            $this->printHand( $dealer ),
            $bet->get()
        );
    }

    public function onHandPush (
        Turn $turn,
        Hand $player,
        Hand $dealer,
        Amount $bet
    ) {
        printf(
            "%s's %s pushes with dealer's %s\n",
            $turn->getPlayer()->getName(),
            $this->printHand( $player ),
            $this->printHand( $dealer )
        );
    }

    public function onHandLoss (
        Turn $turn,
        Hand $player,
        Hand $dealer,
        Amount $bet
    ) {
        if ( $player->isBust() ) {
            printf(
                "%s busted, loses %.2f",
                $turn->getPlayer()->getName(),
                $bet->get()
            );
        } else {
            printf(
                "%s's %s loses to dealer's %s, losing %.2f\n",
                $turn->getPlayer()->getName(),
                $this->printHand( $player ),
                $this->printHand( $dealer ),
                $bet->get()
            );
        }
    }

    public function onDealerHandEnd ( Round $round, Hand $hand ) {
        echo "dealer hand ends play\n";
    }

    public function onHandsPlayed ( Round $round ) {

    }

    public function onRoundEnd ( Round $round ) {
        echo "round end\n";
    }

    public function onShuffle ( Round $round ) {
        echo "dealer shuffles the deck\n";
    }

};
