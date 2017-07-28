<?php

class Test {

    protected $name;
    protected $test;
    protected $assertions;
    protected $expectsException;
    protected $result;

    public function __construct ( string $name, callable $test ) {
        $this->name = $name;
        $this->test = $test;
        $this->assertions = [];
        $this->expectsException = false;
        $this->result = null;
    }

    public function getName () {
        return $this->name;
    }

    public function run () {
        try {
            $this->result = call_user_func( $this->test );
        } catch ( \Exception $ex ) {
            if ( $this->expectsException )
                return true;
            else
                return false;
        }
        foreach ( $this->assertions as $assert ) {
            $result = $this->result;
            if ( !call_user_func( $assert, $this->result ) ) {
                return false;
            }
        }
        return true;
    }

    public function eq ( $val ) {
        $this->assertions[] = function ( $ret ) use ( $val ) {
            return $val === $ret;
        };
        return $this;
    }

    public function ne ( $val ) {
        $this->assertions[] = function ( $ret ) use ( $val ) {
            return $val === $ret;
        };
        return $this;
    }

    public function excepts () {
        $this->expectsException = true;
        return $this;
    }

};

function runConsoleTest ( $test ) {

    $sayGreen = function ( $msg ) {
        return "\033[" . '0;32' . $msg . 'm' . "\033[0m";
    };
    $sayRed = function ( $msg ) {
        return "\033[" . '0;31' . $msg . 'm' . "\033[0m";
    };
    if ( $test->run() ) {
        echo $test->getName() . " OK\n";
    } else {
        echo $test->getName() . " !!\n";
        print_r( $test );
    }
}
