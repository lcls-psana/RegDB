<?php

/*
 * Save a parameter of the proposal (Run 14)
 * 
 * PARAMETERS:
 * 
 *   <proposal>
 *   <id> <val>
 *   <urawi_authentication> <urawi_personId> <urawi_contact>
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
    $urawi_contact        = $SVC->required_str('urawi_contact') ;

    $modified_uid = '' ;
    if ($urawi_authentication) {
        $modified_uid = $urawi_contact ;
    } else {
        $uid = $SVC->authdb()->authName() ;
        $account = $SVC->safe_assign (
            $SVC->regdb()->find_user_account($uid) ,
            "No user account found for UID: '{$uid}'" ) ;
        $modified_uid = $account['gecos'] ;
    }
    $SVC->regdb()->saveProposalParam_Run14($proposal, $id, $val, $modified_uid) ;
    
    return array (
        'proposal' => array (
            'modified_time' => \LusiTime\LusiTime::now()->to64() ,
            'modified_uid'  => $modified_uid
        )
    ) ;
}) ;