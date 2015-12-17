<?php

/*
 * Save a parameter of the proposal
 * 
 * PARAMETERS:
 * 
 *   <exper_id> <id> <val>
 *
 */
require_once 'dataportal/dataportal.inc.php' ;
require_once 'lusitime/lusitime.inc.php' ;

\DataPortal\ServiceJSON::run_handler ('POST', function ($SVC) {
    
    $proposal = $SVC->required_str('proposal') ;
    $exper_id = $SVC->required_int('exper_id') ;
    $id       = $SVC->required_str('id') ;
    $val      = $SVC->required_str('val') ;

    $exper = $SVC->safe_assign (
        $SVC->regdb()->find_experiment_by_id($exper_id) ,
        "no experiment is registered for id: {$exper_id} and proposal number: {$proposal}"
    ) ;    
    $exper->saveProposalParam_Run13($id, $val) ;
    
    return array (
        'proposal' => array (
            'modified_time' => \LusiTime\LusiTime::now()->to64() ,
            'modified_uid'  => $SVC->authdb()->authName()
        )
    ) ;
}) ;