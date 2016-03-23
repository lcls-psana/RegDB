<?php

/*
 */
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
        "X001"
    ) ;
    $infos     = array() ;
    $is_editor = array() ;
    $is_viewer = array() ;

    foreach ($proposals as $proposalNo) {

        $info = $SVC->safe_assign(
            $SVC->urawi()->proposalInfo($proposalNo) ,
            "No such proposal found: {$proposalNo}." ) ;

        $experimentName = $info->posix_group('2016-03-24') ;
        $exper = $SVC->safe_assign(
            $SVC->regdb()->find_experiment_by_unique_name($experimentName) ,
            "We're sorry - this proposal is not found in our system") ;

        $infos    [$proposalNo] = $info ;
        $is_editor[$proposalNo] = $SVC->authdb()->hasRole($SVC->authdb()->authName(), $exper->id(), 'ExperimentInfo', 'Editor') ;
        $is_viewer[$proposalNo] = $SVC->authdb()->hasRole($SVC->authdb()->authName(), $exper->id(), 'ExperimentInfo', 'Reader') ;
    }
?>

<!doctype html>
<html>

<head>

<title>Run 14 Proposal Catalog</title>

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

<div id="title" >Run 14 Proposal Catalog</div>

<div id="comments" >
    Please, select your proposal and follow the link by clicking on the proposal
    number. You will see the Web form and instructions for how to fill it out.
</div>

<table>
  <thead>
    <tr>
      <td class="proposal"     > Proposal </td>
      <td class="instrument"   > Instrument  </td>
      <td class="spokesperson" > Spokesperson  </td>
      <td class="access"       > Your Access Level </td>
    </tr>
  </thead>
  <tbody>
<?php foreach ($infos as $proposalNo => $info) { ?>
    <tr>
      <td class="proposal"     ><a class="link" href="https://pswww.slac.stanford.edu/apps-dev/regdb/run14_proposal_questionnaire?proposal=<?=$proposalNo?>" target="_blank" ><?=$proposalNo?></a></td>
      <td class="instrument"   ><?=$info->instrument()?></td>
      <td class="spokesperson" ><?=$info->contact()->name()?></td>
      <td class="access"       ><?=($is_editor[$proposalNo] ? 'editor' : ($is_viewer[$proposalNo] ? 'viewer' : 'no access'))?></td>
    </tr>      
<?php } ?>

  </tbody>
</table>
</body>
</html>
<?php
})
?>