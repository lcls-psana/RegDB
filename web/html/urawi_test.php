<!doctype html>
<html>

<head>

<title>Scheduled Experiments (URAWI)</title>

<style>

body {
    margin:     0;
    padding:    0;
}
#main {
    padding:    20px;
}
table {
    border-spacing: 0;
    border-collapse: separate;
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

</style>

</head>

<body>
  <div id="main" >
    <table>
      <thead>
        <tr>
          <td>P#</td>
          <td>Instr</td>
          <td>Group</td>
          <td>Title</td>
          <td>Contact</td>
          <td>PI UNIX account</td>
          <td>Collaborators</td>
          <td>Start day</td>
          <td>#shifts</td>
        </tr>
      <tbody>
<?php
/*
 * Return Data Retention Policy parameters
 * 
 * AUTHORIZATION: not required
 */
require_once 'dataportal/dataportal.inc.php' ;

\DataPortal\Service::run_handler ('GET', function ($SVC) {
    $proposals = $SVC->urawi()->proposals() ;
    foreach ($proposals as $p) {
        $info = $SVC->urawi()->proposalInfo($p) ;
        $expname = $info->posix_group() ;
        $exper = $SVC->regdb()->find_experiment_by_name($expname) ;
        $group_str = is_null($exper) ? $expname : "<a href=\"../portal/index.php?exper_id={$exper->id()}\" target=\"_blank\">{$expname}</a>" ;
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
        foreach ($info->members() as $m) print "<div class=\"account\" >{$m->uid()}</div>" ;
        print <<<HERE
          </td>
          <td>{$info->start()->toStringDay()}</td>
          <td>{$info->numShifts()}</td>
        </tr>
HERE;
    }
}) ;
?>
      </tbody>
    </table>
  </div>
</body>
</html>
