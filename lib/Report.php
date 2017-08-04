<?php

namespace maxvu\bjsim3;

abstract class Report {

    public function onRoundBegin ( Round $round ) {

    }

    public function onBetsPlaced ( Round $round ) {

    }

    public function onHandsDealt ( Round $round ) {

    }

    public function onSolicitInsurance ( Round $round ) {

    }

    public function onInsuranceBetsPlaced ( Round $round ) {

    }

    public function onPeek ( bool $dealerBlackjack ) {

    }

    public function onBetPlace (
        Round $round,
        Player $player,
        Amount $bet
    ) {

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

    }

    public function onHandPlay (
        Round $round,
        Player $player,
        Hand $handAfter,
        int $play
    ) {

    }

    public function onDealerHandBegin ( Round $round, Hand $hand ) {

    }

    public function onDealerHandPlay (
        Round $round,
        Hand $handAfter,
        int $play
    ) {
        echo "report::ondealerhandplay\n";
    }

    public function onDealerHandEnd ( Round $round, Hand $hand ) {

    }

    public function onHandsPlayed ( Round $round ) {

    }

    public function onRoundEnd ( Round $round ) {

    }

    public function onShuffle ( Round $round ) {

    }

};
