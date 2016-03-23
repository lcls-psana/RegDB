<?php

/*
 * Returns parameters of the proposal (Run 14)
 * 
 * PARAMETERS:
 * 
 *   <proposal> <id>
 *
 */
require_once 'dataportal/dataportal.inc.php' ;

\DataPortal\ServiceJSON::run_handler ('POST', function ($SVC) {
    
    $proposal             = $SVC->required_str('proposal') ;
    $urawi_authentication = $SVC->required_int('urawi_authentication') ;
    $urawi_personId       = $SVC->required_int('urawi_personId') ;

    return array (
        'params' => $SVC->regdb()->getProposalParams_Run14($proposal)
    ) ;        
}) ;