<?php

namespace maxvu\bjsim3;

class Amount {

    public static function convert ( $amount ) {
        if ( $amount === null )
            return new Amount( 0.0 );
        if ( is_numeric( $amount ) )
            return new Amount( floatval( $amount ) );
        else if ( is_a( $amount, 'maxvu\bjsim3\Amount' ) )
            return $amount;
        else {
            print_r( $amount );
            throw new \Exception( "Can't convert $amount into Amount." );
        }
    }

    protected $amount;
    protected $round;

    public function __construct ( float $amt, $round = 0.5 ) {
        if ( $round > 0 )
            $this->amount = $amt - fmod( $amt, $round );
        else
            $this->amount = $amt;
    }

    public function isZero () {
        return $this->amount === 0.0 || $this->amount === 0;
    }

    public function get () {
        return $this->amount;
    }

    public function add ( $other ) {
        $other = Amount::convert( $other );
        return new Amount( $this->amount + $other->amount, $this->round );
    }

    public function sub ( $other ) {
        $other = Amount::convert( $other );
        return new Amount( $this->amount - $other->amount, $this->round );
    }

    public function mul ( $other ) {
        $other = Amount::convert( $other );
        return new Amount( $this->amount * $other->amount, $this->round );
    }

    public function div ( $other ) {
        $other = Amount::convert( $other );
        return new Amount( $this->amount / $other->amount, $this->round );
    }

    public function ge ( $other ) {
        $other = Amount::convert( $other );
        return $this->amount >= $other->amount;
    }

    public function gt ( $other ) {
        $other = Amount::convert( $other );
        return $this->amount > $other->amount;
    }

    public function giveTo ( Amount &$recip ) {
        $recip->add( $this->amount );
        $this->amount = 0.0;
        return $this;
    }

    public function __toString () {
        return strval( $this->amount );
    }

};
