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

    // Page access is restricted to the LCLS personell logged via the WebAuth
    // authentication system.
    $is_editor = $SVC->authdb()->hasRole($SVC->authdb()->authName(), null, 'ExperimentInfo', 'Editor') ;
    $is_reader = $SVC->authdb()->hasRole($SVC->authdb()->authName(), null, 'ExperimentInfo', 'Reader') ;
    $SVC->assert(
        $is_editor || $is_reader ,
        "We're sorry - you're not authorized to view this document") ;
        
    $modified = array() ;
    $infos    = array() ;

    foreach ($SVC->regdb()->getProposalContacts_Run14() as $proposalNo => $lcls_contact) {

        $info = $SVC->safe_assign(
            $SVC->urawi()->proposalInfo($proposalNo) ,
            "No such proposal found: {$proposalNo}." ) ;

        $modified_num  = 0 ;
        $modified_time = 0 ;
        $modified_uid  = '' ;
        foreach ($SVC->regdb()->getProposalParams_Run14($proposalNo) as $param) {
            $modified_num++ ;
            if ($param['modified_time'] > $modified_time) {
                $modified_time = $param['modified_time'] ;
                $modified_uid  = $param['modified_uid'] ;
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

<title>Run 14 Proposal Catalog (and the last modifications)</title>

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

<div id="title" >Run 14 Proposal Catalog (and the last modifications)</div>

<div id="comments" >
    Please, select your proposal and follow the link by clicking on the proposal
    number. You will see the Web form and instructions for how to fill out
    the proposal questionnaire.
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
      <td class="proposal"     ><a class="link" href="https://pswww.slac.stanford.edu/apps-dev/regdb/run14_proposal_questionnaire?proposal=<?=$proposalNo?>" target="_blank" ><?=$proposalNo?></a></td>
      <td class="instrument"   ><?=$info->instrument()?></td>
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