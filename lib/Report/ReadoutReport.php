<?php

namespace maxvu\bjsim3\Report;

use \maxvu\bjsim3\Amount as Amount;
use \maxvu\bjsim3\Hand as Hand;
use \maxvu\bjsim3urn as Turn;
use \maxvu\bjsim3\Round as Round;
use \maxvu\bjsim3\Player as Player;
use \maxvu\bjsim3\HandDecision as HandDecision;

class ReadoutReport extends \maxvu\bjsim3\Report {

    private function printHand ( Hand $hand ) {
        if ( $hand->isBlackjack() )
            return 'blackjack';
        if ( $hand->isBust() )
            return 'bust';
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
        echo "dealer solicits insurance\n";
    }

    public function onInsuranceBetsPlaced ( Round $round ) {
        echo "all insurance bets placed.\n";
        foreach ( $round->getTurns() as $turn ) {
            if ( $turn->getInsurance() ) {
                printf(
                    "%s insures for %.2f\n",
                    $turn->getPlayer()->getName(),
                    $turn->getInsurance()->get()
                );
            }
        }
    }

    public function onPeek ( bool $dealerBlackjack ) {
        printf(
            "dealer peeks -- %s\n",
            $dealerBlackjack ? 'blackjack.' : 'no blackjack.'
        );
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
        printf(
            "house claims %s's bet of %.2f\n",
            $player->getName(),
            $originalBet->get()
        );
    }

    public function onBetPayout (
        Round $round,
        Player $player,
        Amount $originalBet,
        Amount $totalPayout
    ) {
        printf(
            "house pays out %.2f to %s\n",
            $totalPayout->get(),
            $player->getName()
        );
    }

    public function onHandBegin (
        Round $round,
        Player $player,
        Hand $hand
    ) {
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

    public function onDealerHandEnd ( Round $round, Hand $hand ) {
        echo "dealer hand ends play\n";
    }

    public function onHandsPlayed ( Round $round ) {

    }

    public function onRoundEnd ( Round $round ) {

    }

    public function onShuffle ( Round $round ) {

    }

};
