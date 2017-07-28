<?php

namespace maxvu\bjsim3;

class HandOption {

    protected $mask;

    public function __construct (
        boolean $canDouble,
        boolean $canSplit,
        boolean $canSurrender
    ) {
        $mask = HandDecision::STAND | HandDecision::HIT;
        if ( $canDouble ) $mask |= HandDecision::DOUBLEDOWN;
        if ( $canSplit ) $mask |= HandDecision::SPLIT;
        if ( $canSurrender ) $mask |= HandDecision::SURRENDER;
    }

    public function canDouble () {
        return $this->mask & HandDecision::DOUBLEDOWN;
    }

    public function canSplit () {
        return $this->mask & HandDecision::DOUBLEDOWN;
    }

    public function canSurrender () {
        return $this->mask & HandDecision::DOUBLEDOWN;
    }

};
