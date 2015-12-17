<!doctype html>
<html>

<head>

<title>Scheduled Experiments (URAWI)</title>

<style>

body {
    margin:     0;
    padding:    0;
}
#comments {
    width:      100%;
    padding:    20px;

    background-color:   #f0f0f0;

    font-family:    verdana, sans-serif;
    font-size:      12px;
}
#comments .important {
    color:          red;
    font-weight:    bold;
}
#main {
    width:  100%;
}
table {
    padding:            20px;
    border-spacing:     0;
    border-collapse:    separate;
}
td {
    padding:        2px 6px;
    border-right:   solid 1px #b0b0b0;
    font-family:    verdana, sans-serif;
    font-size:      12px;
}
thead td {
    border-bottom:  solid 1px #b0b0b0;
    font-weight:    bold;
    white-space:    nowrap;
}
thead tr td:last-child {
    border-right:   0;
}
tbody td {
    border-bottom:  solid 1px #e0e0e0;
    vertical-align: top;
}

tbody tr td:nth-child(2),
tbody tr td:nth-child(3) {
    text-align:     right;
}
tbody tr td:nth-child(5),
tbody tr td:nth-child(8) {
    white-space:    nowrap;
}
tbody tr td:nth-child(4) {
    width:  25%;
}
tbody tr:hover {
    background-color:   aliceblue;
}
tbody tr:last-child td {
    border-bottom:   0;
}
tbody tr > td:first-child {
    font-weight:    bold;
}
tbody tr > td:last-child {
    border-right:   0;
}
.account {
    float:  left;
    width:  72px;
}
.account > a {
    color:  #0071bc;
}
.account-not-member,
.account-not-member > a {
    color:  red;
}
.experiment-not-registered {
    color:  red;
}
.control-button {
    font-size:  10px;
}
</style>

<script type="text/javascript" src="/jquery/js/jquery-1.8.2.js"></script>

<script>

function sync_posix_group (proposalNo) {
    var url    = '../regdb/ws/proposal_sync_group.php' ;
    var params = {proposal: proposalNo} ;
    var jqXHR  = $.get (
        url ,
        params ,
        function(data) {
            var result = eval(data) ;
            if (result.status != 'success') {
                alert('the Web service reported the following problem with the request: '+result.message) ;
                return ;
            }
            window.location.reload(true) ;
        } ,
        'JSON'
    ).error(function () {
        alert('operation failed because of: '+jqXHR.statusText) ;
    }) ;
}

</script>

</head>

<body>
  <div id="comments" >
    <b>COMMENTS:</b>
    <ul>
      <li>This page displays the experiment registration status of the beamline proposals for the rest of the present LCLS run.</li>
      <li>Experiments are sorted by a day of their first shift as per the <a href="http://www-ssrl.slac.stanford.edu/lcls-resources/schedules" target="_blank" >LCLS schedules</a>.</li>
      <li>Experiment names shown in the <span class="important" >red</span> color are not registered in PCDS.</li>
      <li>Collaborator accounts shown in the <span class="important" >red</span> color are not registered
          as member of the corresponding POSIX group of an experiment.</li>
    </ul>
  </div>
  <div id="main" >
<?php
/*
 * Return Data Retention Policy parameters
 * 
 * AUTHORIZATION: not required
 */
require_once 'dataportal/dataportal.inc.php' ;

\DataPortal\Service::run_handler ('GET', function ($SVC) {
    
    $startDate = $SVC->optional_time('start_date', null) ;
    $stopDate  = $SVC->optional_time('stop_date',  null) ;

    $is_RegDB_Editor = $SVC->authdb()->hasRole($SVC->authdb()->authName(), null, 'RegDB', 'Editor') ;

    print <<<HERE
<table>
  <thead>
    <tr>
      <td>P#</td>
      <td>Instr</td>
      <td>Experiment</td>
      <td>Title</td>
      <td>Contact</td>
      <td>PI UNIX account</td>
      <td>Collaborators (UNIX accounts)</td>
      <td>Start day</td>
      <td>#shifts</td>
HERE;
    if ($is_RegDB_Editor) print <<<HERE
      <td>Actions</td>
HERE;
    print <<<HERE
    </tr>
  <tbody>
HERE;
//    $proposals = $SVC->urawi()->proposals($startDate, $stopDate) ;
    $proposals = array(
"LK85",
"LK86",
"LK88",
"LK89",
"LK96",
"LK99",
"LL02",
"LL04",
"LL05",
"LL09",
"LL13",
"LL14",
"LL20",
"LL22",
"LL23",
"LL25",
"LL28",
"LL29",
"LL31",
"LL33",
"LL34",
"LL36",
"LL37",
"LL38",
"LL41",
"LL44",
"LL48",
"LL58",
"LL71",
"LL72",
"LL78",
"LL82",
"LL84",
"LL86",
"LL94",
"LM01",
"LM04",
"LM08",
"LM09",
"LM11",
"LM14",
"LM16",
"LM18",
"LM20",
"LM23",
"LM27",
"LM38",
"LM47",
"LM48",
"LM51",
"LM52"
    ) ;
    $fixed_start_date = "2016-03-24" ;
//    $fetchProposalInfoMethod = 'proposal_basic' ;
    foreach ($proposals as $p) {
//        $info = $SVC->urawi()->proposalInfo($p, $fetchProposalInfoMethod) ;
        $info = $SVC->urawi()->proposalInfo($p) ;
        $expname = $info->posix_group($fixed_start_date) ;
        $exper = $SVC->regdb()->find_experiment_by_name($expname) ;
        $group_str = is_null($exper) ?
            "<div class=\"experiment-not-registered\" >{$expname}</div>" :
            "<a href=\"../portal/index.php?exper_id={$exper->id()}\" target=\"_blank\">{$expname}</a>" ;
        print <<<HERE
    <tr>
      <td>{$info->number()}</td>
      <td>{$info->instrument()}</td>
      <td>{$group_str}</td>
      <td>{$info->title()}</td>
      <td>{$info->contact_email()}</td>
      <td>{$info->contact()->uid()}</td>
      <td>
HERE;
        $posix_group_members = array() ;
        if (!is_null($exper)) {
            foreach ($SVC->regdb()->posix_group_members($expname) as $m) {
                $posix_group_members[$m['uid']] = $m ;
            }
        }
        $members_not_in_group = array() ;
        foreach ($info->members() as $m) {
            $uid = $m->uid() ;
            $classes = 'account' ;
            if (!array_key_exists($uid, $posix_group_members)) {
                $classes .= ' account-not-member' ;
                array_push($members_not_in_group, $uid) ;
            }
            print <<<HERE
        <div class="{$classes}" ><a href="javascript:alert('{$m->email()}')" >{$uid}</a></div>
HERE;
        }
        $start_day = $info->start() ? $info->start()->toStringDay() : '&nbsp;' ;
        print <<<HERE
      </td>
      <td>{$start_day}</td>
      <td>{$info->numShifts()}</td>
HERE;
        if ($is_RegDB_Editor) {
            $actions = '' ;
            if (is_null($exper)) {
                $actions .= "<button class=\"control-button register-experiment\" name=\"{$expname}\" disabled=]\"disabled\" >REGISTER</button>" ;
            } else {
                if (count($members_not_in_group))
                    $actions .= "<button class=\"control-button synch-group-members\" name=\"{$expname}\" onclick=\"javascript:window.sync_posix_group('{$info->number()}')\" >SYNCRONIZE</button>" ;
            }
            print <<<HERE
      <td>{$actions}</td>
HERE;
        }
        print <<<HERE
    </tr>
HERE;
    }
    print <<<HERE
  </tbody>
</table>
HERE;
}) ;
?>
  </div>
</body>
</html>
