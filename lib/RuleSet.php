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
            'dealer.peek-ten' => true,
            'dealer.peek-ace' => true,
            'dealer.wins-ties' => false,
            'hand.surrender' => true,
            'hand.double.restrict' => false,
            'hand.split.unlike-tens' => true,
            'hand.split.aces-halt' => true,
            'hand.double.after-split' => true,
            'hand.bet.max' => 4,
            'hand.split.max' => 3
        ];
    }

    public static function getSummaryString ( RuleSet $rules ) {
        // x-deck, s17 Y, peek10+A,
        $decks = $rules[ 'game.deck-count' ];
        $s17 = $rules[ 'dealer.s17-stand' ] ? 'S17 stand' : 'S17 hit';
        $peek10 = $rules[ 'dealer.peek-ten' ] ? 'peek ten' : '';
        $peekAce = $rules[ 'dealer.peek-ace' ] ? 'peek ace' : '';
        return "{$decks}-deck, $s17, $peek10, $peekAce";
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
