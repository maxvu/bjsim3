<?php

namespace maxvu\bjsim3;

use maxvu\bjsim3\Report\Report as Report;
use maxvu\bjsim3\Report\AggregateReport as AggregateReport;

class Table {

    protected $rules;
    protected $settings;
    protected $shoe;
    protected $players;
    protected $reports;

    public function __construct (
        RuleSet $rules,
        Settings $settings,
        array $players = []
    ) {
        $this->rules = $rules;
        $this->settings = $settings;
        $this->shoe = new Shoe( $this->rules[ 'game.deck-count' ] );
        $this->players = $players;
        $this->reports = new AggregateReport;
    }

    public function getRules () {
        return $this->rules;
    }

    public function getSettings () {
        return $this->settings;
    }

    public function & getShoe () {
        return $this->shoe;
    }

    public function shouldShuffle () {
        $maxPenetration = $this->settings[ 'shoe.penetration' ];
        return $this->shoe->getPenetration() > $maxPenetration;
    }

    public function playRound () {
        if ( !sizeof( $this->players ) )
            throw new \Exception( "No players given to Table." );
        return (new Round( $this ))->play();
    }

    public function addPlayer ( Player $player ) {
        $this->players[] = $player;
        return $this;
    }

    public function getPlayers () {
        return $this->players;
    }

    public function addReport ( Report $report ) {
        $this->reports->addReport( $report );
        return $this;
    }

    public function & getReport () {
        return $this->reports;
    }

};
