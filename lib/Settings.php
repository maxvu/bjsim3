<?php

namespace maxvu\bjsim3;

class Settings implements \ArrayAccess {

    public static function getDefault () {
        return [
            'bet.min' => 5.00,
            'bet.max' => 200.00,
            'bet.step' => 1.00,
            'shoe.penetration' => 0.80
        ];
    }

    public function __construct ( $settings = [] ) {
        $default = Settings::getDefault();
        foreach ( $settings as $settingName => $settingVal ) {
            if ( !isset( $default[ $settingName ] ) ) {
                throw new \Exception( "unknown setting: $settingName" );
            }
        }
        $this->settings = array_merge( $default, $settings );
    }

    public function offsetExists ( $settingName ) {
        return isset( $this->settings[ $settingName ] );
    }

    public function offsetGet ( $settingName ) {
        if ( !isset( $this->settings[ $settingName ] ) )
            throw new \Exception( "unknown setting: $settingName" );
        return $this->settings[ $settingName ];
    }

    public function offsetSet ( $settingName, $value ) {
        throw new \Exception( "Tried to modify RuleSet at '$settingName'." );
    }

    public function offsetUnset ( $settingName ) {
        throw new \Exception( "Tried to modify RuleSet at '$settingName'." );
    }

};
