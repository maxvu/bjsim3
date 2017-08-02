<?php

namespace maxvu\bjsim3;

class ShoeCount {

    protected $counts;

    public function __construct ( $counts ) {
        $this->counts = [];
        foreach ( Rank::getAll() as $rank ) {
            if ( !isset( $counts[ $rank ] ) )
                throw new \Exception( "ShoeCount initialization incomplete." );
        }
        foreach ( $counts as $rank => $count ) {
            if ( !Rank::isValid( $rank ) )
                throw new \Exception(
                    "Invalid rank in ShoeCount initialization."
                );
            if ( $count < 0 )
                throw new \Exception(
                    "Negative count in ShoeCount initialization."
                );
        }
        $this->counts = $counts;
    }

    public function getCountByRank ( $rank ) {
        if ( !Rank::isValid( $rank ) )
            throw new \Exception( "Invalid rank: $rank." );
        return $this->counts[ $rank ];
    }

    public function getIncidenceByRank ( $rank ) {
        if ( !Rank::isValid( $rank ) )
            throw new \Exception( "Invalid rank: $rank." );
        $total = 0;
        foreach ( $this->counts as $count )
            $total += $count;
        return $this->counts[ $rank ] / $total;
    }

    public function getIncidencesByRank ( $ranks ) {
        $p = 0;
        foreach ( $ranks as $rank )
            $p += $this->getIncidence( $rank );
        return $p;
    }

    public function getIncidenceByValue ( $loValue ) : float {
        if ( !in_array( $loValue, range( 1, 11 ) ) )
            throw new \Exception( "Invalid low value: $loValue." );
        $incidence = 0;
        $total = 0;
        foreach ( $this->counts as $rank => $count ) {
            if ( Rank::getLowValue( $rank ) === $loValue )
                $incidence += $count;
            $total += $count;
        }
        return $incidence / $total;
    }

    public function add ( $rank ) : ShoeCount {
        if ( !Rank::isValid( $rank ) )
            throw new \Exception( "Invalid rank: $rank." );
        $rankLo = Rank::getLowValue( $rank );
        $this->counts[ $rankLo ]++;
        return $this;
    }

    public function sub ( $rank ) : ShoeCount {
        if ( !Rank::isValid( $rank ) )
            throw new \Exception( "Invalid rank: $rank." );
        $rankLo = Rank::getLowValue( $rank );
        $this->counts[ $rankLo ]++;
        return $this;
    }

    public function copy () : ShoeCount {
        return new ShoeCount( $this->counts );
    }

};
