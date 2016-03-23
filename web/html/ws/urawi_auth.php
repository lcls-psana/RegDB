<?php

/*
 * Authenticate the user against URAWI and return the URAWI 'personId'
 * if user credentials are valid.
 * 
 * PARAMETERS:
 * 
 *   <username> <password>
 *
 */
require_once 'dataportal/dataportal.inc.php' ;

\DataPortal\ServiceJSON::run_handler ('POST', function ($SVC) {

    $personId = $SVC->urawi()->authenticate (
        $SVC->required_str('username') ,
        $SVC->required_str('password')
    ) ;
    return array (
        'personId' => $personId ? $personId : 0
    ) ;
}) ;