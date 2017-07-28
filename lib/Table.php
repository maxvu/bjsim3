<?php

namespace maxvu\bjsim3;

class Table {

    protected $rules;
    protected $settings;
    protected $shoe;
    protected $players;
    protected $log;

    // TODO: dealer draws but doesn't notify player strategies

    public function __construct (
        RuleSet $rules,
        Settings $settings,
        array $players
    ) {
        $this->rules = $rules;
        $this->settings = $settings;
        $this->shoe = new Shoe( $this->rules[ 'game.deck-count' ] );
        $this->players = $players;

        if ( !sizeof( $this->players ) )
            throw new \Exception( "No players given to Table." );
    }

    public function getRules () {
        return $this->rules;
    }

    public function getSettings () {
        return $this->settings;
    }

    public function getShoe () {
        return $this->shoe;
    }

};
