<?php

namespace maxvu\bjsim3\Report;

use \maxvu\bjsim3\Amount as Amount;
use \maxvu\bjsim3\Hand as Hand;
use \maxvu\bjsim3\Turn as Turn;
use \maxvu\bjsim3\Round as Round;
use \maxvu\bjsim3\Player as Player;
use \maxvu\bjsim3\HandDecision as HandDecision;

class AggregateReport extends Report {

    protected $subreports;

    public function __construct ( $subreports = [] ) {
        $this->subreports = [];
    }

    public function addReport ( Report $report ) {
        $this->subreports[] = $report;
        return $this;
    }

    public function onRoundBegin ( Round $round ) {
        foreach ( $this->subreports as $report )
            $report->onRoundBegin( $round );
        return $this;
    }

    public function onBetsPlaced ( Round $round ) {
        foreach ( $this->subreports as $report )
            $report->onBetsPlaced( $round );
        return $this;
    }

    public function onHandsDealt ( Round $round ) {
        foreach ( $this->subreports as $report )
            $report->onHandsDealt( $round );
        return $this;
    }

    public function onSolicitInsurance ( Round $round ) {
        foreach ( $this->subreports as $report )
            $report->onSolicitInsurance( $round );
        return $this;
    }

    public function onInsuranceBetsPlaced ( Round $round ) {
        foreach ( $this->subreports as $report )
            $report->onInsuranceBetsPlaced( $round );
        return $this;
    }

    public function onPeek ( Round $round, bool $dealerBlackjack ) {
        foreach ( $this->subreports as $report )
            $report->onPeek( $round, $dealerBlackjack );
        return $this;
    }

    public function onBetPlace (
        Round $round,
        Player $player,
        Amount $bet
    ) {
        foreach ( $this->subreports as $report )
            $report->onBetPlace( $round, $player, $bet );
        return $this;
    }

    public function onBetClaim (
        Round $round,
        Player $player,
        Amount $originalBet
    ) {
        foreach ( $this->subreports as $report )
            $report->onBetClaim( $round, $player, $originalBet );
        return $this;
    }

    public function onBetPayout (
        Round $round,
        Player $player,
        Amount $originalBet,
        Amount $totalPayout
    ) {
        foreach ( $this->subreports as $report )
            $report->onBetPayout( $round, $player, $originalBet, $totalPayout );
        return $this;
    }

    public function onHandBegin (
        Round $round,
        Player $player,
        Hand $hand
    ) {
        foreach ( $this->subreports as $report )
            $report->onHandBegin( $round, $player, $hand );
        return $this;
    }

    public function onHandPlay (
        Round $round,
        Player $player,
        Hand $handAfter,
        int $play
    ) {
        foreach ( $this->subreports as $report )
            $report->onHandPlay( $round, $player, $handAfter, $play );
        return $this;
    }

    public function onHandBlackjack (
        Turn $turn,
        Hand $player,
        Hand $dealer,
        Amount $bet,
        Amount $payout
    ) {
        foreach ( $this->subreports as $report )
            $report->onHandBlackjack( $turn, $player, $dealer, $bet, $payout );
        return $this;
    }

    public function onHandWin (
        Turn $turn,
        Hand $player,
        Hand $dealer,
        Amount $bet
    ) {
        foreach ( $this->subreports as $report )
            $report->onHandWin( $turn, $player, $dealer, $bet );
        return $this;
    }

    public function onHandPush (
        Turn $turn,
        Hand $player,
        Hand $dealer,
        Amount $bet
    ) {
        foreach ( $this->subreports as $report )
            $report->onHandPush( $turn, $player, $dealer, $bet );
        return $this;
    }

    public function onHandLoss (
        Turn $turn,
        Hand $player,
        Hand $dealer,
        Amount $bet
    ) {
        foreach ( $this->subreports as $report )
            $report->onHandLoss( $turn, $player, $dealer, $bet );
        return $this;
    }

    public function onDealerHandBegin ( Round $round, Hand $hand ) {
        foreach ( $this->subreports as $report )
            $report->onDealerHandBegin( $round, $hand );
        return $this;
    }

    public function onDealerHandPlay (
        Round $round,
        Hand $handAfter,
        int $play
    ) {
        foreach ( $this->subreports as $report )
            $report->onDealerHandPlay( $round, $handAfter, $play );
    }

    public function onDealerHandEnd ( Round $round, Hand $hand ) {
        foreach ( $this->subreports as $report )
            $report->onDealerHandEnd( $round, $hand );
    }

    public function onHandsPlayed ( Round $round ) {
        foreach ( $this->subreports as $report )
            $report->onHandsPlayed( $round );
        return $this;
    }

    public function onRoundEnd ( Round $round ) {
        foreach ( $this->subreports as $report )
            $report->onRoundEnd( $round );
        return $this;
    }

    public function onShuffle ( Round $round ) {
        foreach ( $this->subreports as $report )
            $report->onShuffle( $round );
        return $this;
    }

};
