<?php

require_once 'dataportal/dataportal.inc.php' ;

class EventHandler {
    public function on_error($msg, $stack=null) {
        print <<<HERE
<div id="error">
  $msg
</div>
HERE;
    }
} ;
\DataPortal\Service::set_custom_error_handler(new EventHandler()) ;

\DataPortal\Service::run_handler ('GET', function ($SVC) {
    
    $proposals = array (
        "LK85" ,
        "LK86" ,
        "LK88" ,
        "LK89" ,
        "LK96" ,
        "LK99" ,
        "LL02" ,
        "LL04" ,
        "LL05" ,
        "LL09" ,
        "LL13" ,
        "LL14" ,
        "LL20" ,
        "LL22" ,
        "LL23" ,
        "LL25" ,
        "LL28" ,
        "LL29" ,
        "LL31" ,
        "LL33" ,
        "LL34" ,
        "LL36" ,
        "LL37" ,
        "LL38" ,
        "LL41" ,
        "LL44" ,
        "LL48" ,
        "LL58" ,
        "LL71" ,
        "LL72" ,
        "LL74" ,
        "LL78" ,
        "LL82" ,
        "LL84" ,
        "LL86" ,
        "LL94" ,
        "LM01" ,
        "LM04" ,
        "LM08" ,
        "LM09" ,
        "LM11" ,
        "LM14" ,
        "LM16" ,
        "LM18" ,
        "LM20" ,
        "LM23" ,
        "LM27" ,
        "LM38" ,
        "LM47" ,
        "LM48" ,
        "LM51" ,
        "LM52"
    ) ;
    $num = 0 ;
    foreach ($proposals as $proposalNo) {

        $info = $SVC->safe_assign(
            $SVC->urawi()->proposalInfo($proposalNo) ,
            "No such proposal found: {$proposalNo}." ) ;

        $experimentName = $info->posix_group('2016-03-24') ;
        $exper = $SVC->safe_assign(
            $SVC->regdb()->find_experiment_by_unique_name($experimentName) ,
            "We're sorry - this proposal is not found in our system") ;

        $uid = $exper->leader_account() ;
        if ($SVC->authdb()->hasRole($uid, $exper->id(), 'ExperimentInfo', 'Editor')) continue ;
        $SVC->authdb()->createRolePlayer('ExperimentInfo', 'Editor', $exper->id(), $uid) ;
        print "Authorized account '{$uid}' for proposal {$proposalNo} (experiment '{$experimentName}'<br>" ;
        $num++ ;
    }
    print "Fixed {$num} accounts<br>" ;
    $SVC->finish() ;
}) ;
?>
