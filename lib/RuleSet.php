<?php

namespace maxvu\bjsim3;

class RuleSet implements \ArrayAccess {

    public static function VegasStrip () {
        return new RuleSet([
            'game.deck-count' => 4,
            'hand.surrender' => false,
            'dealer.peek-ten' => true,
            'hand.double.restrict' => false,
            'hand.split.unlike-tens' => true,
            'hand.double.after-split' => true
        ]);
    }

    public static function AtlanticCity () {
        return new RuleSet([
            'game.deck-count' => 8,
            'hand.surrender' => true,
            'dealer.peek-ten' => true,
            'hand.double.restrict' => false,
            'hand.split.unlike-tens' => true,
            'hand.double.after-split' => true
        ]);
    }

    public static function getDefault () {
        return [
            'game.deck-count' => 6,
            'game.blackjack-payout' => 1.50,
            'dealer.s17-stand' => true,
            'dealer.insurance' => true,
            'dealer.peek-ten' => false,
            'dealer.peek-ace' => true,
            'dealer.wins-ties' => false,
            'hand.surrender' => false,
            'hand.double.restrict' => false,
            'hand.split.unlike-tens' => true,
            'hand.split.aces-halt' => true,
            'hand.double.after-split' => true,
            'hand.bet.max' => 4,
            'hand.split.max' => 3
        ];
    }

    protected $rules;

    public function __construct ( $rules = [] ) {
        $default = RuleSet::getDefault();
        foreach ( $rules as $ruleName => $ruleVal ) {
            if ( !isset( $default[ $ruleName ] ) ) {
                throw new \Exception( "unknown rule: $ruleName" );
            }
        }
        $this->rules = array_merge( RuleSet::getDefault(), $rules );
    }

    public function offsetExists ( $ruleName ) {
        return isset( $this->rules[ $ruleName ] );
    }

    public function offsetGet ( $ruleName ) {
        if ( !isset( $this->rules[ $ruleName ] ) )
            throw new \Exception( "Unknown rule: $ruleName." );
        return $this->rules[ $ruleName ];
    }

    public function offsetSet ( $ruleName, $value ) {
        throw new \Exception( "Tried to modify RuleSet at '$ruleName'." );
    }

    public function offsetUnset ( $ruleName ) {
        throw new \Exception( "Tried to modify RuleSet at '$ruleName'." );
    }

};
