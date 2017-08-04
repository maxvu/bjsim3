<?php

namespace maxvu\bjsim3\Report;

use \maxvu\bjsim3\Amount as Amount;
use \maxvu\bjsim3\Hand as Hand;
use \maxvu\bjsim3\Turn as Turn;
use \maxvu\bjsim3\Round as Round;
use \maxvu\bjsim3\Player as Player;
use \maxvu\bjsim3\HandDecision as HandDecision;

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

    public function onPeek ( Round $round, bool $dealerBlackjack ) {

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
        Amount $payout
    ) {

    }

    public function onTurnBegin (

    ) {

    }

    public function onTurnEnd (
        Round $round,
        Turn $turn
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

    public function onHandBlackjack (
        Turn $turn,
        Hand $player,
        Hand $dealer,
        Amount $bet,
        Amount $payout
    ) {

    }

    public function onHandWin (
        Turn $turn,
        Hand $player,
        Hand $dealer,
        Amount $bet
    ) {

    }

    public function onHandPush (
        Turn $turn,
        Hand $player,
        Hand $dealer,
        Amount $bet
    ) {

    }

    public function onHandLoss (
        Turn $turn,
        Hand $player,
        Hand $dealer,
        Amount $bet
    ) {

    }

    public function onDealerHandBegin ( Round $round, Hand $hand ) {

    }

    public function onDealerHandPlay (
        Round $round,
        Hand $handAfter,
        int $play
    ) {

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
