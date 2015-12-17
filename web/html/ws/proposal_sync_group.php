<?php

/*
 * Add UNIX accounts of the proposal collaborators to the corresponding
 * POSIX group of the experiment.
 * 
 * PARAMETERS:
 * 
 *   <proposal>
 *
 */
require_once 'dataportal/dataportal.inc.php' ;

\DataPortal\ServiceJSON::run_handler ('GET', function ($SVC) {
    
    $proposalNo = $SVC->required_str('proposal') ;

    $SVC->assert (
        $SVC->authdb()->hasRole($SVC->authdb()->authName(), null, 'RegDB', 'Editor') ,
        "your account is not authorized for this operation"
    ) ;

    $info    = $SVC->urawi()->proposalInfo($proposalNo) ;
    $expname = $info->posix_group() ;
    $exper   = $SVC->regdb()->find_experiment_by_name($expname) ;
    if (!$exper)
        $SVC->abort("no experiment '{$expname}' registered yet for proposal number '{$proposalNo}'") ;

    // Get the list of the POSIX groups members
    $posix_group_members = array() ;
    if (!is_null($exper)) {
        foreach ($SVC->regdb()->posix_group_members($expname) as $m) {
            $posix_group_members[$m['uid']] = $m ;
        }
    }

    // Add collaborators into the group if they're not in there yet
    foreach ($info->members() as $m) {
        $uid = $m->uid() ;
        if (!is_null($uid)) {
            if (array_key_exists($uid, $posix_group_members)) continue ;
            $SVC->regdb()->add_user_to_posix_group($uid, $expname) ;
        }
    }
}) ;