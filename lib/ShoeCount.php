<?php

namespace maxvu\bjsim3;

class ShoeCount {

    protected $counts;

    public function __construct ( $counts ) {
        $this->counts = [];
        foreach ( Rank::getAll() as $rank ) {
            $rankLowValue = Rank::getLowValue( $rank );
            $this->counts[ $rankLowValue ] = $counts[ $rankLowValue ];
        }
        if ( sizeof( array_keys( $this->counts ) ) !== 10 )
            throw new \Exception( "Invalid shoe count." );
    }

    public function getCount ( $rankLo ) {
        if ( $rankLo < 1 || $rankLo > 10 || !is_int( $rankLo ) )
            throw new \Exception( "Invalid rank low-value: $rankLo." );
        return $this->counts[ $rankLo ];
    }

    public function getIncidence ( $rankLo ) {
        $totalSize = 0;
        foreach ( $this->counts as $count )
            $totalSize += $count;
        return $this->counts[ $rankLo ] / $totalSize;
    }

    public function add ( $rankLo ) {
        if ( $rankLo < 1 || $rankLo > 10 || !is_int( $rankLo ) )
            throw new \Exception( "Invalid rank low-value: $rankLo." );
        $this->counts[ $rankLo ]++;
        return $this;
    }

    public function sub ( $rankLo ) {
        if ( $rankLo < 1 || $rankLo > 10 || !is_int( $rankLo ) )
            throw new \Exception( "Invalid rank low-value: $rankLo." );
        $this->counts[ $rankLo ]++;
        return $this;
    }

    public function copy () {
        return new ShoeCount( $this->counts );
    }

};
