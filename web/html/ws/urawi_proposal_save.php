<?php

/*
 * Save a parameter of the proposal (Run 14)
 * 
 * PARAMETERS:
 * 
 *   <exper_id> <id> <val>
 *
 */
require_once 'dataportal/dataportal.inc.php' ;
require_once 'lusitime/lusitime.inc.php' ;

\DataPortal\ServiceJSON::run_handler ('POST', function ($SVC) {
    
    $proposal             = $SVC->required_str('proposal') ;
    $id                   = $SVC->required_str('id') ;
    $val                  = $SVC->required_str('val') ;
    $urawi_authentication = $SVC->required_int('urawi_authentication') ;
    $urawi_personId       = $SVC->required_int('urawi_personId') ;

    $modified_uid = $urawi_authentication ? "urawi:{$urawi_personId}" : $SVC->authdb()->authName() ;

    $SVC->regdb()->saveProposalParam_Run14($proposal, $id, $val, $modified_uid) ;
    
    return array (
        'proposal' => array (
            'modified_time' => \LusiTime\LusiTime::now()->to64() ,
            'modified_uid'  => $modified_uid
        )
    ) ;
}) ;