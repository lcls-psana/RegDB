<?php

/*
 * Returns parameters of the proposal
 * 
 * PARAMETERS:
 * 
 *   <exper_id> <id>
 *
 */
require_once 'dataportal/dataportal.inc.php' ;

\DataPortal\ServiceJSON::run_handler ('GET', function ($SVC) {
    
    $proposal = $SVC->required_str('proposal') ;
    $exper_id = $SVC->required_int('exper_id') ;

    $exper = $SVC->safe_assign (
        $SVC->regdb()->find_experiment_by_id($exper_id) ,
        "no experiment is registered for id: {$exper_id} and proposal number: {$proposal}"
    ) ;
    return array (
        'params' => $exper->getProposalParams_Run13()
    ) ;        
}) ;