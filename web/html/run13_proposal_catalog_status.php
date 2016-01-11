<?php

/*
 */
require_once 'dataportal/dataportal.inc.php' ;
require_once 'lusitime/lusitime.inc.php' ;

use \LusiTime\LusiTime ;

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
    $modified = array() ;
    $infos    = array() ;

    foreach ($proposals as $proposalNo) {

        $info = $SVC->safe_assign(
            $SVC->urawi()->proposalInfo($proposalNo) ,
            "No such proposal found: {$proposalNo}." ) ;

        $experimentName = $info->posix_group('2016-03-24') ;
        $exper = $SVC->safe_assign(
            $SVC->regdb()->find_experiment_by_unique_name($experimentName) ,
            "We're sorry - this proposal is not found in our system") ;

        $modified_num  = 0 ;
        $modified_time = 0 ;
        $modified_uid  = '' ;
        foreach ($exper->getProposalParams_Run13() as $param) {
            $modified_num++ ;
            if ($param['modified_time'] > $modified_time) {
                $modified_time = $param['modified_time'] ;
                $user = $SVC->regdb()->find_user_account($param['modified_uid']) ;
                $modified_uid  = $user['gecos'] ;
            }
        }
        $modified[$proposalNo] = array (
            'num'  => $modified_num  ? $modified_num : '' ,
            'time' => $modified_time ? LusiTime::from64($modified_time)->toStringShort() : '' ,
            'uid'  => $modified_uid
        ) ;
        $infos[$proposalNo] = $info ;
    }
?>

<!doctype html>
<html>

<head>

<title>Run 13 Proposal Catalog</title>

<style>

body {
    margin:     0;
    padding:    0;
}

#title {
    padding:    20px;
    text-align: center;

    font-weight:    bold;
    font-family:    'Segoe UI',Tahoma,Helvetica,Arial,Verdana,sans-serif;
    font-size:      24px;
}
#comments {
    padding:        20px;
    padding-left:   30px;
    padding-bottom:  5px;
    max-width:      640px;
    font-family:    'Segoe UI',Tahoma,Helvetica,Arial,Verdana,sans-serif;
    font-size:      14px;
}
#error {
    padding:    20px;
    color:      red;
    font-family:    'Segoe UI',Tahoma,Helvetica,Arial,Verdana,sans-serif;
    font-weight:    bold;
    font-size:      20px
}


table {
    border-spacing:     0;
    border-collapse:    separate;
    margin:             20px;
    margin-left:        40px;
}
table td {
    padding:        2px 6px;
    border-right:   solid 1px #b0b0b0;
    font-family:    verdana, sans-serif;
    font-size:      13px;
}
table > thead td {
    border-bottom:  solid 1px #b0b0b0;
    font-weight:    bold;
    white-space:    nowrap;
}
table tr td:last-child {
    border-right:   0;
}
table > tbody td {
    border-bottom:  solid 1px #e0e0e0;
    padding-top:    4px;
    padding-bottom: 4px;
    vertical-align: top;
}
table > tbody td.access {
    font-weight:    bold;
}
table > tbody tr:hover {
    background-color:   aliceblue;
}
table > tbody tr:last-child td {
    border-bottom:   0;
}

a.link {
    text-decoration:    none    !important;
    font-weight:        bold    !important;
    color:              #0071bc !important;
}
a:hover, a.link:hover {
  color: red;
}

</style>
</head>
<body>

<div id="title" >Run 13 Proposal Catalog (and the last modifications)</div>

<div id="comments" >
    Please, select your proposal and follow the link by clicking on the proposal
    number. You will see the Web form and instructions for how to fill it out.
</div>

<table>
  <thead>
    <tr>
      <td class="proposal"      > Proposal </td>
      <td class="instrument"    > Instrument  </td>
      <td class="spokesperson"  > Spokesperson  </td>
      <td class="modified-num"  > # of modified fields </td>
      <td class="modified-time" > Last Modification </td>
      <td class="modified-uid"  > By User </td>
    </tr>
  </thead>
  <tbody>
<?php foreach ($infos as $proposalNo => $info) { ?>
    <tr>
      <td class="proposal"      ><a class="link" href="https://pswww.slac.stanford.edu/apps-dev/regdb/run13_proposal_questionnaire?proposal=<?=$proposalNo?>" target="_blank" ><?=$proposalNo?></a></td>
      <td class="instrument"    ><?=$info->instrument()?></td>
      <td class="spokesperson"  ><?=$info->contact()->name()?></td>
      <td class="modified-num"  >&nbsp;<?=$modified[$proposalNo]['num']?></td>
      <td class="modified-time" >&nbsp;<?=$modified[$proposalNo]['time']?></td>
      <td class="modified-uid"  >&nbsp;<?=$modified[$proposalNo]['uid']?></td>
    </tr>      
<?php } ?>

  </tbody>
</table>
</body>
</html>
<?php
})
?>