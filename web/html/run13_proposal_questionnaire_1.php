<!doctype html>
<html>

<head>

<title>Run 13 Proposal Questionnaire</title>

<style>

body {
    margin:     0;
    padding:    0;
}
#proposal {
    padding:    20px;
    border-top:  solid 1px #c0c0c0;

    font-family:    'Segoe UI',Tahoma,Helvetica,Arial,Verdana,sans-serif;
    font-size:  20px;
}
#proposal > div.key,
#proposal > div.val {
    float:  left;
}
#proposal > div.key {
    width:          152px;
    font-weight:    bold;
}
#proposal > div.val {
    color: #0071bc !important;
}
#proposal > div.end {
    clear:  both;
}

#comments {
/*    width:      100%;*/
    padding:    20px;
    border-top:  solid 1px #c0c0c0;

    border-left:    solid 1px #c0c0c0;

/*    background-color:   #f0f0f0;*/

    font-family:    'Segoe UI',Tahoma,Helvetica,Arial,Verdana,sans-serif;
    font-size:      14px;
}
#comments > ul {
    margin-top:     10px;
    padding-left:   25px;
}
#comments .important {
    color:          red;
    font-weight:    bold;
}

#tabs {
    padding:          0;

    background:       0;

    font-family: 'Segoe UI',Tahoma,Helvetica,Arial,Verdana,sans-serif;
    font-size:        11px;
}

#tabs > ul {
    background:       #f5f8f9 url(/jquery/css/custom-theme-1.9.1/images/ui-bg_inset-hard_100_f5f8f9_1x100.png) 50% 50% repeat-x;
}
#tabs > ul > li {
  border-radius:    0 !important;
}
#tabs > div {
    padding-left:   5px;
    padding-top:    10px;
}

/* Override JQuery UI styles */

li > a.ui-tabs-anchor {
    font-size:      14px;
    font-weight:    nomal;
    color:          #000000 !important;
}
li.ui-tabs-active > a.ui-tabs-anchor {
    font-weight:    bold !important;
}
.ui-corner-all {
    -webkit-border-radius:  0 !important;
    border-radius:          0 !important;
}

#error {
    padding:    20px;
    color:      red;
    font-family:    'Segoe UI',Tahoma,Helvetica,Arial,Verdana,sans-serif;
    font-weight:    bold;
    font-size:      20px
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
thead tr td:nth-child(1) {
    text-align:     right;
}
thead tr td:nth-child(2) {
    text-align:     center;
}
thead tr td:nth-child(4) {
    text-align:     center;
}
thead tr td:last-child {
    border-right:   0;
}
tbody td {
    border-bottom:  solid 1px #e0e0e0;
    padding-top:    4px;
    padding-bottom: 4px;
    vertical-align: top;
}
tbody td.noborder {
    border-bottom-color:  #ffffff;
}
tbody td.item {
    color:  #0071bc;
    font-size:  13px;
}
tbody td.item_group {
    font-weight:    bold;
}
tbody td.val > select,
tbody td.val > input,
tbody td.val > textarea {
    width:  100%;
    border: 1px solid #ffffff;
}
tbody td.val > select:hover,
tbody td.val > input:hover,
tbody td.val > textarea:hover {
    border-color:   default;
}
tbody td.unit {
/*    width:  40px;*/
}
tbody td.instr {
    font-style: italic;
}
tbody tr td:nth-child(1) {
    text-align:     right;
}
tbody tr td:nth-child(2) {
    text-align:     center;
    font-weight:    bold;
}
tbody tr td:nth-child(3),
tbody tr td:nth-child(3) > input {
    text-align:     left;
}
tbody tr td:nth-child(4) {
    text-align:     center;
    font-weight:    bold;
}
tbody tr td:nth-child(1) {
    white-space:    nowrap;
}
tbody tr td:nth-child(5) {
    width:  40%;
}
tbody tr:hover {
    background-color:   aliceblue;
}
tbody tr:last-child td {
    border-bottom:   0;
}
tbody tr > td:first-child {
/*    font-weight:    bold;*/
}
tbody tr > td:last-child {
    border-right:   0;
}
.experiment-not-registered {
    color:  red;
}
.control-button {
    font-size:  10px;
}
</style>

<link type="text/css" href="/jquery/css/custom-theme-1.9.1/jquery-ui.custom.css" rel="Stylesheet" />

<script type="text/javascript" src="/jquery/js/jquery.min.js"></script>
<script type="text/javascript" src="/jquery/js/jquery-ui-1.9.1.custom.min.js"></script>

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

$(function () {
    $('#tabs').tabs() ;
}) ;

</script>

</head>
<body>

<?php

/*
 * 
 * AUTHORIZATION: the PI of the proposal
 */
require_once 'dataportal/dataportal.inc.php' ;

/**
 * Custom error handler
 *
 * @param string $msg
 * @param arrey $stack
 */
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
    
    $proposalNo = $SVC->required_str('proposal') ;

    $info = $SVC->safe_assign(
        $SVC->urawi()->proposalInfo($proposalNo) ,
        "No such proposal found: {$proposalNo}. Did yoy miss the letter 'L' at the begining of the proposal?") ;

    $experimentName = $info->posix_group() ;
    $exper = $SVC->safe_assign(
        $SVC->regdb()->find_experiment_by_unique_name($info->posix_group()) ,
        "We're sorry - this proposal is not found in our system") ;

    $SVC->assert(
        $SVC->authdb()->hasRole($SVC->authdb()->authName(), $exper->id(), 'ExperimentInfo', 'Editor') ,
        "We're sorry - you're not authorized to view/modify this document") ;
    
?>

<div id="proposal" style="float:left;" >

    <div class="key" >Proposal:</div>
    <div class="val" ><?=$proposalNo?></div>
    <div class="end" ></div>

    <div class="key" >Spokesperson:</div>
    <div class="val" ><?=$info->contact()->name()?></div>
    <div class="end" ></div>

    <div class="key" >Instrument:</div>
    <div class="val" ><?=$info->instrument()?></div>
    <div class="end" ></div>

</div>

<div id="comments"  style="float:left;">
  <b>FORM INSTRUCTION:</b>
  <ul>
    <li>This Web form represents a final questionnaire for the Run 13 LCLS proposals</li>
    <li>All your modifications will be automatically recorded in a database as you'll be making them</li>
    <li>The input must be provided before <span class="important">2016-01-15</span></li>
  </ul>
</div>

<div style="clear:both;" ></div>

<?php
    $thead =<<<HERE
<thead>
  <tr>
    <td> Item         </td>
    <td> Priority     </td>
    <td> Value        </td>
    <td> Units        </td>
    <td> Instructions </td>
  </tr>
</thead>
HERE;
    
    $instr_select = "Select from Pull-down Menu" ;
    $instr_manual = "Enter manually" ;
    
    $yes_no =<<<HERE
<select>
  <option                     > Yes </option>
  <option selected="selected" > No </option>
</select>
HERE;
    
    $xray_mode =<<<HERE
<select>
  <option> Continuous                           </option>
  <option> Shots on demand with event sequencer </option>
  <option> Burst Mode                           </option>
</select>
HERE;
    
    $xray_rate =<<<HERE
<select>
  <option> 120   </option>
  <option>  60   </option>
  <option>  30   </option>
  <option>  10   </option>
  <option>   5   </option>
  <option>   0.5 </option>
</select>
HERE;

    $xray_pulse_energy =<<<HERE
<select>
  <option> Maximum   </option>
  <option> Stability </option>
</select>
HERE;

    $xray_operating_mode =<<<HERE
<select>
  <option> </option>
  <option> SASE </option>
  <option> Low-charge </option>
  <option> Seeded </option>
  <option> Two-color </option>
  <option> Two-pulse </option>
  <option> Two-color seeded </option>
  <option> Delta Undulator </option>
  <option> Energy Scanning </option>
  <option> Other </option>
</select>
HERE;

    $xray_energy_scanning =<<<HERE
<select>
  <option> </option>
  <option> Scanning within the SASE bandwidth </option>
  <option> Scanning larger than the SASE bandwidth </option>
</select>
HERE;

    $xray_split_and_delay =<<<HERE
<select>
  <option                     > AMO Split-and-Delay </option>
  <option selected="selected" > Other </option>
</select>
HERE;

    $xray_techniques =<<<HERE
<select>
  <option> </option>
  <option> X-ray Absorption Spectroscopy (XAS) </option>
  <option> X-ray Emission Spectroscopy (XES) </option>
  <option> Inelastic X-ray Scattering (IXS) </option>
  <option> Wide Angle X-ray Scattering (WAXS) </option>
  <option> Small Angle X-ray Scattering (SAXS) </option>
  <option> Diffraction </option>
  <option> Crystallography </option>
  <option> Time of Flight </option>
  <option> Resonant Scattering/Diffraction </option>
  <option> Grazing Incidence SAXS/WAXS </option>
  <option> Coherent Scattering/Diffraction </option>
  <option> Single Particle Imaging </option>
  <option> Other </option>
</select>
HERE;

    $xray_detectors=<<<HERE
<select>
  <option> </option>
  <option> CSPAD (1516x1516 pixels) </option>
  <option> CSPAD 560K (Quad) </option>
  <option> CSPAD-140K (180x256 pixels) </option>
  <option> pnCCD (Front Plane) </option>
  <option> pnCCD (Back Plane) </option>
  <option> pnCCD (Side) </option>
  <option> Rayonix MX-170 </option>
  <option> Princeton CCD (1340x1300 pixels) </option>
  <option> ePix100 </option>
  <option> Diode </option>
  <option> Yag Screen </option>
  <option> OPAL Cameras </option>
  <option> Other </option>
</select>
HERE;

    $input =<<<HERE
<input type="text" width="32" ></input>
HERE;
    
    $textarea =<<<HERE
<textarea rows="4" cols="64" ></textarea>
HERE;
?>

<div id="tabs" >

  <ul>
    <li><a href="#xray"  >X-rays</a></li>
    <li><a href="#laser" >Pump Laser</a></li>
    <li><a href="#env"   >Endstation & Sample Environment</a></li>
    <li><a href="#data"  >Controls & DAQ</a></li>
  </ul>

  <div id="xray" >
    <table>
      <?=$thead?>
      <tbody>
        <tr>
          <td class="item"  >Standard Configuration</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$yes_no?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item"  >Multiplexed</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$yes_no?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item"  >X-ray Mode</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$xray_mode?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item"  >X-ray Repetition Rate</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$xray_rate?></td>
          <td class="unit"  >Hz</td>
          <td class="instr" ><?=$instr_select?></td>
        </tr>
<?php for ($prio = 1; $prio <= 5; $prio++) { $extra = $prio < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($prio === 1 ? "Photon Energies" : "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$prio?></td>
          <td class="val"               ><?=$input?></td>
          <td class="unit  <?=$extra?>" ><?=($prio === 1 ? "eV" : "&nbsp;")?></td>
          <td class="instr <?=$extra?>" ><?=($prio === 1 ? $instr_manual : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >Describe reason for multiple energies and prioritization</td>
        </tr>
<?php for ($prio = 1; $prio <= 5; $prio++) { $extra = $prio < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($prio === 1 ? "Pulse Durations" : "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$prio?></td>
          <td class="val"               ><?=$input?></td>
          <td class="unit  <?=$extra?>" ><?=($prio === 1 ? "fs" : "&nbsp;")?></td>
          <td class="instr <?=$extra?>" ><?=($prio === 1 ? $instr_manual : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >Describe reason for multiple pulse durations and prioritization</td>
        </tr>
        <tr>
          <td class="item"  >Pulse Energy</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$xray_pulse_energy?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" ><?=$instr_select?></td>
        </tr>
<?php for ($prio = 1; $prio <= 5; $prio++) { $extra = $prio < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($prio === 1 ? "FWHM X-ray Focal Spot Sizes" : "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$prio?></td>
          <td class="val"               ><?=$input?></td>
          <td class="unit  <?=$extra?>" ><?=($prio === 1 ? "um" : "&nbsp;")?></td>
          <td class="instr <?=$extra?>" ><?=($prio === 1 ? $instr_manual : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >Describe reason for multiple x-ray spot sizes and prioritization</td>
        </tr>
<?php for ($prio = 1; $prio <= 5; $prio++) { $extra = $prio < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($prio === 1 ? "Operating Mode" : "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$prio?></td>
          <td class="val"               ><?=$xray_operating_mode?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($prio === 1 ? $instr_select : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >	If Other, describe</td>
        </tr>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >Describe reason for multiple operating modes and prioritization</td>
        </tr>
        <tr>
          <td class="item"  >Monochromator</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$yes_no?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item"  >Bandwidth</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$input?></td>
          <td class="unit"  >eV</td>
          <td class="instr" ><?=$instr_manual?></td>
        </tr>
        <tr>
          <td class="item noborder"  >Energy Scanning</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$yes_no?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item"  >↳</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$xray_energy_scanning?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" ><?=$instr_select?> (please, provide if the previous was <b>Yes</b>)</td>
        </tr>
<?php for ($prio = 1; $prio <= 2; $prio++) { $extra = $prio < 2 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($prio === 1 ? "Scanning range" : "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$prio?></td>
          <td class="val"               ><?=$input?></td>
          <td class="unit  <?=$extra?>" ><?=($prio === 1 ? "eV" : "&nbsp;")?></td>
          <td class="instr <?=$extra?>" ><?=($prio === 1 ? $instr_manual : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder"  >Split-and-Delay</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$xray_split_and_delay?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >If Other, describe</td>
        </tr>
<?php for ($prio = 1; $prio <= 5; $prio++) { $extra = $prio < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($prio === 1 ? "X-ray Techniques to be Used" : "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$prio?></td>
          <td class="val"               ><?=$xray_techniques?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($prio === 1 ? $instr_select : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder"  >↳ Coincidence Required?</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$yes_no?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item noborder"  >&nbsp;</td>
          <td class="prio noborder"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >If Other, describe</td>
        </tr>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >Describe reason for multiple techniques, coincidence and prioritization</td>
        </tr>
        <tr>
          <td class="item  noborder" >Desired Minimum Resolution</td>
          <td class="prio  noborder" >&nbsp;</td>
          <td class="val"            ><?=$input?></td>
          <td class="unit  noborder" >A</td>
          <td class="instr noborder" ><?=$instr_manual?></td>
        </tr>
        <tr>
          <td class="item"  >↳ Maximum Resolution</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$input?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >&nbsp;</td>
        </tr>
        <tr>
          <td class="item  noborder" >Desired Minimum Q-value</td>
          <td class="prio  noborder" >&nbsp;</td>
          <td class="val"            ><?=$input?></td>
          <td class="unit  noborder" >A<sup>-1</sup></td>
          <td class="instr noborder" ><?=$instr_manual?></td>
        </tr>
        <tr>
          <td class="item"  >↳ Maximum Q-value</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$input?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >&nbsp;</td>
        </tr>
        <tr>
          <td class="item  noborder" >Minimum Sample-Detector Distance</td>
          <td class="prio  noborder" >&nbsp;</td>
          <td class="val"            ><?=$input?></td>
          <td class="unit  noborder" >mm</td>
          <td class="instr noborder" ><?=$instr_manual?></td>
        </tr>
        <tr>
          <td class="item"  >↳ Maximum Distance</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$input?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >&nbsp;</td>
        </tr>
<?php for ($prio = 1; $prio <= 6; $prio++) { $extra = $prio < 6 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($prio === 1 ? "Desired Detectors" : "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$prio?></td>
          <td class="val"               ><?=$xray_detectors?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($prio === 1 ? $instr_select : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder"  >&nbsp;</td>
          <td class="prio noborder"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >If Other, describe</td>
        </tr>
        <tr>
          <td class="item"  &nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >If multiple detectors of the same kind are desired, state how many are desired and explain prioritization</td>
        </tr>
<?php for ($i = 1; $i <= 9; $i++) { ?>
        <tr>
          <td class="item  noborder" ><?=($i === 1 ? "Other X-ray Requirements" : "&nbsp;")?></td>
          <td class="prio  noborder" >&nbsp;</td>
          <td class="val"            ><?=$input?></td>
          <td class="unit  noborder" >&nbsp;</td>
          <td class="instr noborder" ><?=($i === 1 ? "Describe (1 item or system per line). Add Lines if needed." : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$input?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >&nbsp;</td>
        </tr>
<?php for ($i = 1; $i <= 9; $i++) { ?>
        <tr>
          <td class="item  noborder" ><?=($i === 1 ? "User-supplied Equipment" : "&nbsp;")?></td>
          <td class="prio  noborder" >&nbsp;</td>
          <td class="val"            ><?=$input?></td>
          <td class="unit  noborder" >&nbsp;</td>
          <td class="instr noborder" ><?=($i === 1 ? "Describe (1 item or system per line). Add Lines if needed." : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$input?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >&nbsp;</td>
        </tr>
      </tbody>
    </table>
  </div>

<?php
    $pump_laser_needed =<<<HERE
<select>
  <option> </option>
  <option> 100 fs compressed (Ti:S) </option>
  <option> 50 fs compressed (Ti:S) </option>
  <option> Uncompressed (Ti:S) </option>
  <option> Nanosecond </option>
  <option> MEC ns Laser (Glass) </option>
  <option> MEC fs Laser (Ti:S) </option>
  <option> Other </option>
</select>
HERE;

        $pump_laser_advanced =<<<HERE
<select>
  <option> </option>
  <option> HE-TOPAS (High energy OPA) </option>
  <option> Hollow Fiber (&lt;10fs, 800nm) </option>
  <option> ns OPO (Tunable 210-2200nm) </option>
  <option> ns Evolution (527nm) </option>
</select>
HERE;

        $pump_laser_wavelength =<<<HERE
<select>
  <option> </option>
  <option> 800 nm (Ti:S) </option>
  <option> 400 nm (Ti:S) </option>
  <option> 266 nm (Ti:S) </option>
  <option> Mid-IR (2.6-18 um) </option>
  <option> OPA (1.2-2.6um) </option>
  <option> OPA SH (600-1200nm) </option>
  <option> OPA SF (475-600nm) </option>
  <option> OPA vis-UV (240-475nm) </option>
  <option> THz </option>
  <option> Other </option>
</select>
HERE;

        $pump_laser_pulse_duration =<<<HERE
<select>
  <option> </option>
  <option> 50 fs </option>
  <option> 100 fs </option>
  <option> 1 ps </option>
  <option> 150 ps </option>
  <option> 8 ns</option>
  <option> 100 ns </option>
  <option> Other </option>
</select>
HERE;
?>
    
  <div id="laser" >
    <table>
      <?=$thead?>
      <tbody>
        <tr>
          <td class="item noborder" >Pump Laser Needed </td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=$pump_laser_needed?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >If Other, describe</td>
        </tr>
        <tr>
          <td class="item noborder" >Advanced </td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=$pump_laser_advanced?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item noborder" >Multiple Simultaneous Beams? </td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=$yes_no?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item noborder" >↳</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=$input?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         >If Yes, How Many?</td>
        </tr>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >Describe Needs and State Priorities</td>
        </tr>
<?php for ($prio = 1; $prio <= 5; $prio++) { $extra = $prio < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($prio === 1 ? "Wavelength" : "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$prio?></td>
          <td class="val"               ><?=$pump_laser_wavelength?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($prio === 1 ? $instr_select : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder" >↳</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=$input?></td>
          <td class="unit"          >nm</td>
          <td class="instr"         >If Other or in a range, specify</td>
        </tr>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >Describe reason for multiple wavelengths and prioritization</td>
        </tr>
<?php for ($prio = 1; $prio <= 5; $prio++) { $extra = $prio < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item <?=$extra?>"  ><?=($prio === 1 ? "Pulse Energy Min-Max" : "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$prio?></td>
          <td class="val"               ><?=$input?></td>
          <td class="unit  <?=$extra?>" ><?=($prio === 1 ? "mJ" : "&nbsp;")?></td>
          <td class="instr <?=$extra?>" ><?=($prio === 1 ? "{$instr_manual}, e.g. 0.1-3" : "&nbsp;")?></td>
        </tr>
<?php } ?>
<?php for ($prio = 1; $prio <= 5; $prio++) { $extra = $prio < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($prio === 1 ? "FWHM Spot Size" : "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$prio?></td>
          <td class="val"               ><?=$input?></td>
          <td class="unit  <?=$extra?>" ><?=($prio === 1 ? "um" : "&nbsp;")?></td>
          <td class="instr <?=$extra?>" ><?=($prio === 1 ? $instr_manual: "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >Describe reason for multiple spot sizes and prioritization</td>
        </tr>
<?php for ($prio = 1; $prio <= 5; $prio++) { $extra = $prio < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($prio === 1 ? "Fluence" : "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$prio?></td>
          <td class="val"               ><?=$input?></td>
          <td class="unit  <?=$extra?>" ><?=($prio === 1 ? "mJ/cm<sup>2</sup>" : "&nbsp;")?></td>
          <td class="instr <?=$extra?>" ><?=($prio === 1 ? $instr_manual: "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >Describe reason for multiple fluence and prioritization</td>
        </tr>
<?php for ($prio = 1; $prio <= 5; $prio++) { $extra = $prio < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($prio === 1 ? "Pulse Duration (approx)" : "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$prio?></td>
          <td class="val"               ><?=$pump_laser_pulse_duration?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($prio === 1 ? $instr_select: "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder" >↳</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=$textarea?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         >If Other, specify</td>
        </tr>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >Describe reason for multiple pulse durations and prioritization</td>
        </tr>
<?php for ($prio = 1; $prio <= 5; $prio++) { $extra = $prio < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($prio === 1 ? "Temporal profile" : ($prio === 2 ? "(particularly MEC glass laser)" : "&nbsp;"))?></td>
          <td class="prio  <?=$extra?>" ><?=$prio?></td>
          <td class="val"               ><?=$input?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($prio === 1 ? "{$instr_manual} (e.g.top-hat)": "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >Describe reason for multiple temporal profiles and prioritization</td>
        </tr>
<?php for ($prio = 1; $prio <= 5; $prio++) { $extra = $prio < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($prio === 1 ? "Desired Intensity on Target" :  "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$prio?></td>
          <td class="val"               ><?=$input?></td>
          <td class="unit  <?=$extra?>" ><?=($prio === 1 ? "W/cm<sup>2</sup>" :  "&nbsp;")?></td>
          <td class="instr <?=$extra?>" ><?=($prio === 1 ? $instr_manual: "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >Describe reason for multiple intensities and prioritization</td>
        </tr>
        <tr>
          <td class="item"  >Desired Time Resolution</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$input?></td>
          <td class="unit"  >fs</td>
          <td class="instr" ><?=$instr_manual?></td>
        </tr>
        <tr>
          <td class="item noborder" >Time Tool Needed?</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=$yes_no?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         ><?=$instr_select?></td>
        </tr>

<?php
        $pump_laser_geometry =<<<HERE
<select>
  <option> </option>
  <option> Collinear with FEL (&lt;1 degree) </option>
  <option> Off axis from FEL </option>
  <option> Other </option>
</select>
HERE;
?>

<?php for ($prio = 1; $prio <= 5; $prio++) { $extra = $prio < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($prio === 1 ? "Pump Laser Geometry" :  "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$prio?></td>
          <td class="val"               ><?=$pump_laser_geometry?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($prio === 1 ? $instr_select : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder" >↳</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=$textarea?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         >If Other, specify</td>
        </tr>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >Describe reason for multiple geometries and prioritization</td>
        </tr>
<?php for ($i = 1; $i <= 9; $i++) { ?>
        <tr>
          <td class="item  noborder" ><?=($i === 1 ? "User-supplied Laser Equipment" : "&nbsp;")?></td>
          <td class="prio  noborder" >&nbsp;</td>
          <td class="val"            ><?=$input?></td>
          <td class="unit  noborder" >&nbsp;</td>
          <td class="instr noborder" ><?=($i === 1 ? "Describe (1 item or system per line). Add Lines if needed." : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$input?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >&nbsp;</td>
        </tr>
<?php for ($i = 1; $i <= 9; $i++) { ?>
        <tr>
          <td class="item  noborder" ><?=($i === 1 ? "Other Laser Requirements" : "&nbsp;")?></td>
          <td class="prio  noborder" >&nbsp;</td>
          <td class="val"            ><?=$input?></td>
          <td class="unit  noborder" >&nbsp;</td>
          <td class="instr noborder" ><?=($i === 1 ? "Describe (1 item or system per line). Add Lines if needed." : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$input?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >&nbsp;</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div id="env" >
    <table>
      <?=$thead?>
      <tbody>

<?php
        $env_endstation =<<<HERE
<select>
  <option> </option>
  <option> AMO - LAMP </option>
  <option> AMO - HFP </option>
  <option> AMO - DIA </option>
  <option> SXR - Pre-Mono Transmission Sample Chamber </option>
  <option> SXR - Resonant Soft X-ray Scattering Station </option>
  <option> SXR - Resonant Imaging End Station </option>
  <option> SXR - Liquid Jet End Station </option>
  <option> SXR - Momentum Resolved Resonant Inelastic Scattering End Station </option>
  <option> SXR - Electron Beam Ion Trap (EBIT) End Station </option>
  <option> SXR - Surface Chemistry End Station </option>
  <option> XPP - Vacuum Chamber </option>
  <option> XPP - Diffractometer </option>
  <option> XPP - Kappa </option>
  <option> XPP - Phi Stage </option>
  <option> XCS - Diffractometer </option>
  <option> MFX - Goniometer </option>
  <option> CXI - 1 micron Chamber </option>
  <option> CXI - 100 nm Chamber </option>
  <option> User Supplied Sample environment </option>
  <option> Other </option>
</select>
HERE;
        
        $env_sample_env =<<<HERE
<select>
  <option> </option>
  <option> Vacuum chamber </option>
  <option> Atmosphere </option>
  <option> Helium </option>
  <option> Other </option>
</select>
HERE;

        $env_liquid_sample =<<<HERE
<select>
  <option> </option>
  <option> GDVN </option>
  <option> Rayleigh Jet </option>
  <option> Sheet Jet </option>
  <option> High Viscosity </option>
  <option> Drop on Demand </option>
  <option> MESH </option>
  <option> Other </option>
</select>
HERE;
        
        $env_gas_jet=<<<HERE
<select>
  <option> </option>
  <option> Piezo valve </option>
  <option> Even-Lawrie valve </option>
  <option> Supersonic Jet </option>
  <option> Gas Needle </option>
  <option> Other </option>
</select>
HERE;

?>

<?php for ($prio = 1; $prio <= 3; $prio++) { $extra = $prio < 3 ? "noborder" : "" ; ?>
        <tr>
          <td class="item item_group noborder"     ><?=($prio === 1 ? "Specific Endstation" :  "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$prio?></td>
          <td class="val"               ><?=$env_endstation?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($prio === 1 ? $instr_select : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder" >↳</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=$textarea?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         >If Other or User supplied, describe</td>
        </tr>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >Describe reason for multiple needs and prioritization</td>
        </tr>
        <tr>
          <td class="item item_group noborder" >Sample Environment</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=$env_sample_env?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item"  >↳</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$textarea?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >If Other, describe</td>
        </tr>
        <tr>
          <td class="item item_group"  >Sample Temperature</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=$input?></td>
          <td class="unit"  >K</td>
          <td class="instr" ><?=$instr_manual?></td>
        </tr>
        <tr>
          <td class="item item_group noborder" >Sample Handling and Delivery</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           >&nbsp;</td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         >&nbsp;</td>
        </tr>
        <tr>
          <td class="item noborder" >Fixed Target Needs</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=$yes_no?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item noborder" >Scanning rate</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=$input?></td>
          <td class="unit"          >Hz</td>
          <td class="instr"         ><?=$instr_manual?></td>
        </tr>
        <tr>
          <td class="item noborder" >Scanning speed</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=$input?></td>
          <td class="unit"          >mm/sec</td>
          <td class="instr"         ><?=$instr_manual?></td>
        </tr>
<?php for ($prio = 1; $prio <= 3; $prio++) { $extra = $prio < 3 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($prio === 1 ? "Liquid Sample Needs" :  "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$prio?></td>
          <td class="val"               ><?=$env_liquid_sample?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($prio === 1 ? $instr_select : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder" >↳</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=$textarea?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         >If Other, describe</td>
        </tr>
<?php for ($prio = 1; $prio <= 4; $prio++) { $extra = $prio < 4 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($prio === 1 ? "Gas Jet Needs" :  "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$prio?></td>
          <td class="val"               ><?=$env_gas_jet?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($prio === 1 ? $instr_select : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder" >↳</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=$textarea?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         >If Other, describe</td>
        </tr>
        <tr>
          <td class="item noborder" >Aerosol Injector Needs</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=$yes_no?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         ><?=$instr_select?></td>
        </tr>
<?php for ($prio = 1; $prio <= 5; $prio++) { $extra = $prio < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($prio === 1 ? "Other Injector Needs" : "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$prio?></td>
          <td class="val"               ><?=$input?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($prio === 1 ? "Describe (1 item or system per line)" : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder" >&nbsp;</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=$textarea?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         >Describe reason for multiple sample handling and delivery methods and prioritization</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div id="data" >
    <table>
      <?=$thead?>
      <tbody>
      </tbody>
    </table>
  </div>

</div>

<?php
}) ;
?>

</body>
</html>