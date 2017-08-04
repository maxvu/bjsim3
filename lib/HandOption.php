<?php

namespace maxvu\bjsim3;

class HandOption {

    protected $canDouble;
    protected $canSplit;
    protected $canSurrender;

    public function __construct (
        bool $canDouble,
        bool $canSplit,
        bool $canSurrender
    ) {
        $this->canDouble = $canDouble;
        $this->canSplit = $canSplit;
        $this->canSurrender = $canSurrender;
    }

    public function canDouble () {
        return $this->canDouble;
    }

    public function canSplit () {
        return $this->canSplit;
    }

    public function canSurrender () {
        return $this->canSurrender;
    }

};
