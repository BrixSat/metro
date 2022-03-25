<?php

$lines = explode( "\n", file_get_contents( 'metro_povoa.csv' ) );
$headers = str_getcsv( array_shift( $lines ) );
$data = array();
foreach ( $lines as $line ) {
    
    foreach ( str_getcsv( $line ) as $key => $field )
    {
        if (!isset($data[$headers[ $key ]]))
        {
            $data += [$headers[$key] => array()];
        }
        $data += [$headers[$key] => array_push($headers[$key],  array($field))];
        $a= $a+2;
    }
}

var_dump($data);
