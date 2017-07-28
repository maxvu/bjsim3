<?php

namespace maxvu\bjsim3\Util;

class Table {

    protected $rowNames;
    protected $colNames;
    protected $table;

    public function __construct ( $table ) {
        $this->table = $table;
        $this->rowNames = [];
        $this->colNames = [];

        foreach ( $this->table as $rowName => $row ) {
            $this->rowNames[ $rowName ] = 1;
            foreach ( $row as $colName => $cell ) {
                $this->colNames[ $colName ] = 1;
            }
        }
        ksort( $this->colNames );
    }

    public function getRowNames () {
        return array_keys( $this->rowNames );
    }

    public function getColumnNames () {
        return array_keys( $this->colNames );
    }

    public function generate ( $separator = ',' ) {
        $table = "${separator}";

        $table .= implode( $separator, $this->getColumnNames() ) ."\n";
        foreach ( $this->getRowNames() as $rowName ) {
            $table .= "{$rowName}{$separator}";
            foreach ( $this->getColumnNames() as $col ) {
                if ( isset( $this->table[ $rowName ][ $col ] ) ) {
                    $val = $this->table[ $rowName ][ $col ];
                    $table .= "{$val}{$separator}";
                } else {
                    $table .= "{$separator}";
                }
            }
            $table .= "\n";
        }
        return $table;
    }

    public function __toString () {
        return $this->toCSV();
    }

    public function toCSV () {
        return $this->generate( ',' );
    }

    public function toTSV () {
        return $this->generate( "\t" );
    }

};
