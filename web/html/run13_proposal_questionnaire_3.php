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

    $experimentName = $info->posix_group('2016-03-24') ;
    $exper = $SVC->safe_assign(
        $SVC->regdb()->find_experiment_by_unique_name($info->posix_group()) ,
        "We're sorry - this proposal is not found in our system") ;

    $SVC->assert(
        $SVC->authdb()->hasRole($SVC->authdb()->authName(), $exper->id(), 'ExperimentInfo', 'Editor') ,
        "We're sorry - you're not authorized to view/modify this document") ;

    
    $contacts = array (
        "LK85" => 'Roberto Alonso-Mori (robertoa@slac.stanford.edu' ,
        "LK86" => 'Galtier, Eric Christophe (egaltier@slac.stanford.edu)' ,
        "LK88" => 'Aquila, Andy (aquila@slac.stanford.edu)' ,
        "LK89" => 'Ray, Dipanwita (dray@slac.stanford.edu)' ,
        "LK96" => 'Boutet, Sebastie (sboutet@slac.stanford.edu)' ,
        "LK99" => 'Ray, Dipanwita (dray@slac.stanford.edu)' ,
        "LL02" => 'Hunter, Mark Steven (mhunter2@slac.stanford.edu)' ,
        "LL04" => 'Marcin Sikorski(sikorski@slac.stanford.edu)' ,
        "LL05" => 'Boutet, Sebastie (sboutet@slac.stanford.edu)' ,
        "LL09" => 'Osipov, Timur (tyosipov@slac.stanford.edu)' ,
        "LL13" => 'Diling Zhu (dlzhu@slac.stanford.edu)' ,
        "LL14" => 'Liang, Mengning (mliang@slac.stanford.edu)' ,
        "LL20" => 'Galtier, Eric Christophe (egaltier@slac.stanford.edu)' ,
        "LL22" => 'Dakovski, Georgi L. (dakovski@slac.stanford.edu)' ,
        "LL23" => 'Aquila, Andy (aquila@slac.stanford.edu)' ,
        "LL25" => 'Marcin Sikorski(sikorski@slac.stanford.edu)' ,
        "LL28" => 'James M. Glownia (jglownia@slac.stanford.edu)' ,
        "LL29" => 'Lee, Hae Ja (haelee@slac.stanford.edu)' ,
        "LL31" => 'Ray, Dipanwita (dray@slac.stanford.edu)' ,
        "LL33" => 'MacKinnon, Andy (andymack@slac.stanford.edu)' ,
        "LL34" => 'Osipov, Timur (tyosipov@slac.stanford.edu)' ,
        "LL36" => 'Galtier, Eric Christophe (egaltier@slac.stanford.edu)' ,
        "LL37" => 'Diling Zhu (dlzhu@slac.stanford.edu)' ,
        "LL38" => 'Marcin Sikorski(sikorski@slac.stanford.edu)' ,
        "LL41" => 'Sanghoon Song (sanghoon@slac.stanford.edu)' ,
        "LL44" => 'Matthieu Chollet (mchollet@slac.stanford.edu)' ,
        "LL48" => 'Sanghoon Song (sanghoon@slac.stanford.edu)' ,
        "LL58" => 'Galtier, Eric Christophe (egaltier@slac.stanford.edu)' ,
        "LL71" => 'Boutet, Sebastie (sboutet@slac.stanford.edu)' ,
        "LL72" => 'Dakovski, Georgi L. (dakovski@slac.stanford.edu)' ,
        "LL78" => 'Galtier, Eric Christophe (egaltier@slac.stanford.edu)' ,
        "LL82" => 'Nagler, Bob (bnagler@slac.stanford.edu)' ,
        "LL84" => 'Boutet, Sebastie (sboutet@slac.stanford.edu)' ,
        "LL86" => 'Liang, Mengning (mliang@slac.stanford.edu)' ,
        "LL94" => 'Osipov, Timur (tyosipov@slac.stanford.edu)' ,
        "LM01" => 'Diling Zhu (dlzhu@slac.stanford.edu)' ,
        "LM04" => 'Sanghoon Song (sanghoon@slac.stanford.edu)' ,
        "LM08" => 'Diling Zhu (dlzhu@slac.stanford.edu)' ,
        "LM09" => 'Hunter, Mark Steven (mhunter2@slac.stanford.edu)' ,
        "LM11" => 'Matthieu Chollet (mchollet@slac.stanford.edu)' ,
        "LM14" => 'Liang, Mengning (mliang@slac.stanford.edu)' ,
        "LM16" => 'Liang, Mengning (mliang@slac.stanford.edu)' ,
        "LM18" => 'Aquila, Andy (aquila@slac.stanford.edu)' ,
        "LM20" => 'Moeller, Stefan P. (smoeller@slac.stanford.edu)' ,
        "LM23" => 'Osipov, Timur (tyosipov@slac.stanford.edu)' ,
        "LM27" => 'Liang, Mengning (mliang@slac.stanford.edu)' ,
        "LM38" => 'Liang, Mengning (mliang@slac.stanford.edu)' ,
        "LM47" => 'Boutet, Sebastie (sboutet@slac.stanford.edu)' ,
        "LM48" => 'Sanghoon Song (sanghoon@slac.stanford.edu)' ,
        "LM51" => 'Boutet, Sebastie (sboutet@slac.stanford.edu)' ,
        "LM52" => 'Boutet, Sebastie (sboutet@slac.stanford.edu)'
    ) ;
        
?>

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
    padding:        20px;
    padding-left:   30px;
    padding-bottom:  5px;
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
    padding:        20px;
    padding-bottom:  5px;
    max-width:      640px;
/*    border-top:     solid 1px #c0c0c0;*/
/*    border-left:    solid 1px #c0c0c0;*/

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
#status {
    margin-top:     10px;
    padding:        5px;
    padding-right:  10px;
    font-family:    Lucida Grande, Lucida Sans, Arial, sans-serif;
    font-size:      13px;
    color:          maroon;
}
#tabs {
    margin-top: 5px;
    padding:    0;
    background: 0;

    font-family:    'Segoe UI',Tahoma,Helvetica,Arial,Verdana,sans-serif;
    font-size:      11px;
}

/**************************************
 * Override JQuery UI styles for tabs *
 **************************************/

#tabs > ul {
    background: #f5f8f9 url(/jquery/css/custom-theme-1.9.1/images/ui-bg_inset-hard_100_f5f8f9_1x100.png) 50% 50% repeat-x;
}
#tabs > ul > li {
    border-radius:  0 !important;
}
#tabs > div {
    padding-left:   5px;
    padding-top:    10px;
}
#tabs > ul > li > a.ui-tabs-anchor {
    font-size:      14px;
    font-weight:    nomal;
    color:          #000000 !important;
}
#tabs > ul > li.ui-tabs-active > a.ui-tabs-anchor {
    font-weight:    bold !important;
}
#tabs > ul .ui-corner-all {
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
    border-spacing:     0;
    border-collapse:    separate;
}
table td {
    padding:        2px 6px;
    border-right:   solid 1px #b0b0b0;
    font-family:    verdana, sans-serif;
    font-size:      12px;
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
table > tbody td.noborder {
    border-bottom-color:  #ffffff;
}
table td.border1 {
    border-bottom-color:  #b0b0b0;
}
table > tbody tr:hover {
    background-color:   aliceblue;
}
table > tbody tr:last-child td {
    border-bottom:   0;
}
table > tbody td > select,
table > tbody td > input,
table > tbody td > textarea {
    width:  100%;
    border: 1px solid #ffffff;
}

table > tbody td > select:hover,
table > tbody td > input:hover,
table > tbody td > textarea:hover {
    border-color:   default;
}

/******************************************
 * Customizations for the Standard tables *
 ******************************************/

table.standard {
    padding:            20px;
}
table.standard td.item {
    text-align:     right;
}
table.standard td.prio {
    text-align:     center;
}
table.standard td.unit {
    text-align:     center;
}
table.standard td.instr {
    width:  40%;
}
table.standard > tbody td.item {
    color:  #0071bc;
    font-size:  13px;
}
table.standard > tbody td.item_group {
    font-weight:    bold;
}
table.standard > tbody td.instr {
    font-style: italic;
}
table.standard > tbody td.item {
    white-space:    nowrap;
}
table.standard > tbody td.prio {
    font-weight:    bold;
}
table.standard > tbody td.val > input {
    text-align:     left;
}
table.standard > tbody td.unit {
    font-weight:    bold;
}


.xray-sect,
.laser-sect,
.control-sect,
.data-sect {
    margin-bottom:  5px;
    padding-left:   25px;
    padding-right:  20px;
    padding-top:    15px;
}
.control-sect > h1,
.data-sect    > h1 {
    paddding:       0px;
    border-bottom:  1px solid #0071bc;
    font-weight:    bold;
    font-size:      16px;
    color:          #0071bc;
}
.xray-sect    > .comments,
.laser-sect   > .comments,
.control-sect > .comments,
.data-sect    > .comments {
    margin-left:    20px;
    margin-top:     15px;
    max-width:      720px;
    font-size:      13px;
}
.control-sect table,
.data-sect    table {
    margin-top:     20px;
    margin-left:    30px;
    margin-bottom:  10px;
}
.control-sect > textarea {
    width:          700px;
    margin-top:     20px;
    margin-left:    30px;
    margin-bottom:  10px;
}
.control-subsect {
    padding-left:   25px;
}
.control-subsect > h2 {
    paddding:       0px;
    border-bottom:  1px solid #0071bc;
    font-weight:    bold;
    font-size:      14px;
    color:          #0071bc;
}
.control-subsect > textarea {
    width:          700px;
}

/******************************************
 * Customizations for the Controls tables *
 ******************************************/

.control-sect table > tbody td.connection  {
    padding:    0px;
}
.control-sect table > tbody td.connection > textarea {
    width:  95%;
    margin: 0;
    padding:    4px;
}

.control-sect table > tbody td.purpose {
    padding:    0px;
}
.control-sect table > tbody td.purpose > textarea {
    width:  98%;
    margin: 0;
    padding:    4px;
}
.control-sect table > thead td.interface {
    max-width:      72px;
    white-space:    normal;
}
.control-sect table > thead td.quantity {
    min-width:  40px;
}
.control-sect .quote {
    padding-left:   20px;
    padding-top:    10px;
    padding-right:  20px;
    font-style:     italic;
}


/******************************************
 * Customizations for the Analysis tables *
 ******************************************/

.data-sect table {
    padding:    0;
}
table.analysis td.item {
    text-align:     right;
}
table.analysis td.instr {
    width:  40%;
}
table.analysis > tbody td.item {
    color:          #0071bc;
    font-size:      13px;
    font-weight:    bold;
    white-space:    nowrap;
}
table.analysis > tbody td.val > input {
    text-align:     left;
}
table.analysis > tbody td.instr {
    font-style: italic;
}


</style>

<link type="text/css" href="/jquery/css/custom-theme-1.9.1/jquery-ui.custom.css" rel="Stylesheet" />

<script type="text/javascript" src="/jquery/js/jquery.min.js"></script>
<script type="text/javascript" src="/jquery/js/jquery-ui-1.9.1.custom.min.js"></script>

<script>

var proposal = '<?=$proposalNo?>' ;
var exper_id = <?=$exper->id()?> ;


function _pad (n) {
    return (n < 10 ? '0' : '') + n ;
}
function time2htmlLocal (t) {
    return  t.getFullYear() +
        '-' + _pad(t.getMonth() + 1) + 
        '-' + _pad(t.getDate()) +
        '&nbsp;' +
        _pad(t.getHours()) +
        ':' +
        _pad(t.getMinutes()) +
        ':' +
        _pad(t.getSeconds()) ;
}

function updateStatus (s) {
    $('#status').html(s) ;
}

function saveProposalParameter (id, val) {
    var url    = '../regdb/ws/run13_proposal_save.php' ;
    var params = {
        proposal: proposal ,
        exper_id: exper_id ,
        id:       id ,
        val:      val
    } ;
    var jqXHR  = $.post (
        url ,
        params ,
        function(data) {
            var result = eval(data) ;
            if (result.status != 'success') {
                alert('the Web service reported the following problem with the request: '+result.message) ;
                return ;
            }
            var p = data.proposal ;
            var msec = p.modified_time / 1000000 ;
            var t = new Date(msec) ;
            var s = time2htmlLocal(t) ;
            updateStatus('[ Last update: <b>'+s+'</b> &nbsp;by user: <b>'+p.modified_uid+'</b> ]') ;
        } ,
        'JSON'
    ).error(function () {
        alert('operation failed because of: '+jqXHR.statusText) ;
    }) ;
}
function getProposalParameters (on_success) {
    var url    = '../regdb/ws/run13_proposal_get.php' ;
    var params = {
        proposal: proposal ,
        exper_id: exper_id
    } ;
    var jqXHR  = $.get (
        url ,
        params ,
        function(data) {
            var result = eval(data) ;
            if (result.status != 'success') {
                alert('the Web service reported the following problem with the request: '+result.message) ;
                return ;
            }
            on_success(data.params) ;
        } ,
        'JSON'
    ).error(function () {
        alert('operation failed because of: '+jqXHR.statusText) ;
    }) ;
}    
$(function () {
    $('#tabs').tabs() ;

    $('select').change(function () {
        var elem = $(this) ,
            id   = elem.attr('id') ,
            val  = elem.val() ;
        saveProposalParameter(id, val) ;
    }) ;
    $('input').change(function () {
        var elem = $(this) ,
            id   = elem.attr('id') ,
            val  = elem.val() ;
        saveProposalParameter(id, val) ;
    }) ;
    $('textarea').change(function () {
        var elem = $(this) ,
            id   = elem.attr('id') ,
            val  = elem.val() ;
        saveProposalParameter(id, val) ;
    }) ;
    getProposalParameters(function (params) {
        var modified_time = 0 ,
            modified_uid  = '' ;
        for (var i in params) {
            var p = params[i] ;
            var elem = $('#'+p.id) ;
            if (elem.length) {
                elem.val(p.val) ;
            }
            if (p.modified_time > modified_time) {
                modified_time  = p.modified_time ;
                modified_uid      = p.modified_uid ;
            }
        }
        if (modified_time) {
            var msec = modified_time / 1000000 ;
            var t = new Date(msec) ;
            var s = time2htmlLocal(t) ;
            updateStatus('[ Last update: <b>'+s+'</b> &nbsp;by user: <b>'+modified_uid+'</b> ]') ;
        }
    }) ;
    updateStatus('[ No data submitted for the proposal ]') ;
}) ;

</script>

</head>
<body>

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

    <div class="key" >LCLS Contact:</div>
    <div class="val" ><?=$contacts[$proposalNo]?></div>
    <div class="end" ></div>

</div>

<div id="comments"  style="float:right;">
  <b>INSTRUCTIONS:</b>
  <ul>
    <li>Please fill this form in collaboration with the LCLS point of contact for your experiment.</li>
    <li>All your modifications will be automatically recorded in a database as you'll be making them</li>
    <li>The input must be provided before a deadline of <span class="important">2016-01-15</span> after
        which further changes of the experimental requirements will require LCLC Management approval</li>
  </ul>
</div>

<div style="clear:both;" ></div>

<div id="status" style="float:right;" ></div>
<div style="clear:both;" ></div>

<?php
    $thead =<<<HERE
<thead>
  <tr>
    <td class="item"  > Item         </td>
    <td class="prio"  > Priority     </td>
    <td class="val"   > Value        </td>
    <td class="unit"  > Units        </td>
    <td class="instr" > Instructions </td>
  </tr>
</thead>
HERE;
    
    $instr_select = "Select from Pull-down Menu" ;
    $instr_manual = "Enter manually" ;

    function yes_no ($id, $yes=false) {
        $selected = 'selected="selected"' ;
        $yes_selected = $yes ? $selected : '' ;
        $no_selected  = $yes ? ''        : $selected ;
        $str =<<<HERE
<select id="{$id}" >
  <option {$yes_selected} > Yes </option>
  <option {$no_selected} > No </option>
</select>
HERE;
        return $str ;
    }
    function select_quantity ($id) {
        $str =<<<HERE
<select id="{$id}" >
HERE;
        for ($i = 0; $i < 20; $i++) $str .=<<<HERE
  <option> {$i} </option>
HERE;
        $str .=<<<HERE
</select>
HERE;
        return $str ;
    }
    function xray_mode ($id) {
        $str =<<<HERE
<select id="{$id}" >
  <option> Continuous                           </option>
  <option> Shots on demand with event sequencer </option>
  <option> Burst Mode                           </option>
</select>
HERE;
        return $str ;
    }

    function xray_rate ($id) {
        $str =<<<HERE
<select id="{$id}" >
  <option> 120   </option>
  <option>  60   </option>
  <option>  30   </option>
  <option>  10   </option>
  <option>   5   </option>
  <option>   1   </option>
  <option>   0.5 </option>
</select>
HERE;
        return $str ;
    }
    function xray_pulse_energy ($id) {
        $str =<<<HERE
<select id="{$id}" >
  <option> Maximum   </option>
  <option> Stability </option>
</select>
HERE;
        return $str ;
    }
    function xray_operating_mode ($id) {
        $str =<<<HERE
<select id="{$id}" >
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
        return $str ;
    }
    function xray_energy_scanning ($id) {
        $str =<<<HERE
<select id="{$id}" >
  <option> </option>
  <option> Scanning within the SASE bandwidth </option>
  <option> Scanning larger than the SASE bandwidth </option>
</select>
HERE;
        return $str ;
    }
    function xray_split_and_delay ($id) {
        $str =<<<HERE
<select id="{$id}" >
  <option                     > AMO Split-and-Delay </option>
  <option selected="selected" > Other </option>
</select>
HERE;
        return $str ;
    }
    function xray_techniques ($id) {
        $str =<<<HERE
<select id="{$id}" >
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
        return $str ;
    }

    function input ($id) {
        $str =<<<HERE
<input id="{$id}" type="text" width="32" />
HERE;
        return $str ;
    }
    function textarea ($id, $cols=54) {
        $str = <<<HERE
<textarea id="{$id}" rows="4" cols="{$cols}" ></textarea>
HERE;
        return $str ;
    }    
?>

<div id="tabs" >

  <ul>
    <li><a href="#xray"  >X-rays</a></li>
    <li><a href="#laser" >Optical Laser</a></li>
    <li><a href="#env"   >Setup and Sample</a></li>
    <li><a href="#contr" >Controls</a></li>
    <li><a href="#data"  >DAQ & Analysis</a></li>
  </ul>

<?php
    $sect = 'xray' ;
?>
  
  <div id="xray" >
    <div class="xray-sect" >
      <div class="comments">
        This section covers the basic X-ray FEL operation mode, beam parameters,
        X-ray beamline optics requirement. Most standard parameters can be chosen
        from a drop down menu.  If the desired configurations are not list, please
        describe with more details in the ‘additional information’ field.
      </div>
    </div>
    <table class="standard" >
      <?=$thead?>
      <tbody>
        <tr>
          <td class="item item_group noborder"  >Standard Configuration</td>
          <td class="prio            noborder"  >&nbsp;</td>
          <td class="val"   ><?=yes_no($sect.'-standard', true)?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item item_group noborder"  >Multiplexed</td>
          <td class="prio            noborder"  >&nbsp;</td>
          <td class="val"   ><?=yes_no($sect.'-multiplex', true)?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item item_group noborder"  >X-ray Mode</td>
          <td class="prio            noborder"  >&nbsp;</td>
          <td class="val"   ><?=xray_mode($sect.'-mode')?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item item_group border1"  >X-ray Repetition Rate</td>
          <td class="prio            border1"  >&nbsp;</td>
          <td class="val             border1"   ><?=xray_rate($sect.'-reprate')?></td>
          <td class="unit            border1"  >Hz</td>
          <td class="instr           border1" ><?=$instr_select?></td>
        </tr>
<?php for ($i = 1; $i <= 5; $i++) { $extra = $i < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item item_group noborder" ><?=($i === 1 ? "Photon Energies" : "&nbsp;")?></td>
          <td class="prio         <?=$extra?>" ><?=$i?></td>
          <td class="val                     " ><?=input($sect.'-energy-'.$i)?></td>
          <td class="unit         <?=$extra?>" ><?=($i === 1 ? "eV" : "&nbsp;")?></td>
          <td class="instr        <?=$extra?>" ><?=($i === 1 ? $instr_manual.' in the order of priority' : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item  border1"  >&nbsp;</td>
          <td class="prio  border1"  >&nbsp;</td>
          <td class="val   border1"   ><?=textarea($sect.'-energy-descr')?></td>
          <td class="unit  border1"  >&nbsp;</td>
          <td class="instr border1" >Describe reason for multiple energies and prioritization</td>
        </tr>
<?php for ($i = 1; $i <= 5; $i++) { $extra = $i < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item item_group noborder"     ><?=($i === 1 ? "Pulse Durations" : "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$i?></td>
          <td class="val"               ><?=input($sect.'-pulse-'.$i)?></td>
          <td class="unit  <?=$extra?>" ><?=($i === 1 ? "fs" : "&nbsp;")?></td>
          <td class="instr <?=$extra?>" ><?=($i === 1 ? $instr_manual." in the order of priority" : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item  border1"  >&nbsp;</td>
          <td class="prio  border1"  >&nbsp;</td>
          <td class="val   border1"   ><?=textarea($sect.'-pulse-descr')?></td>
          <td class="unit  border1"  >&nbsp;</td>
          <td class="instr border1" >Describe reason for multiple pulse durations and prioritization</td>
        </tr>
        <tr>
          <td class="item item_group border1"  >Pulse Energy</td>
          <td class="prio            border1"  >&nbsp;</td>
          <td class="val             border1"   ><?=xray_pulse_energy($sect.'-pulseenergy')?></td>
          <td class="unit            border1"  >&nbsp;</td>
          <td class="instr           border1" ><?=$instr_select?></td>
        </tr>
<?php for ($i = 1; $i <= 5; $i++) { $extra = $i < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item item_group noborder"     ><?=($i === 1 ? "X-ray spot size (FWHM)" : "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$i?></td>
          <td class="val"               ><?=input($sect.'-focal-'.$i)?></td>
          <td class="unit  <?=$extra?>" ><?=($i === 1 ? "um" : "&nbsp;")?></td>
          <td class="instr <?=$extra?>" ><?=($i === 1 ? $instr_manual : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item  border1"  >&nbsp;</td>
          <td class="prio  border1"  >&nbsp;</td>
          <td class="val   border1"   ><?=textarea($sect.'-focal-descr')?></td>
          <td class="unit  border1"  >&nbsp;</td>
          <td class="instr border1" >Describe reason for multiple x-ray spot sizes and prioritization</td>
        </tr>
<?php for ($i = 1; $i <= 5; $i++) { $extra = $i < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item item_group noborder"     ><?=($i === 1 ? "Operating Mode" : "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$i?></td>
          <td class="val"               ><?=xray_operating_mode($sect.'-opmode-'.$i)?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($i === 1 ? $instr_select : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder"  >&nbsp;</td>
          <td class="prio noborder"  >&nbsp;</td>
          <td class="val"   ><?=textarea($sect.'-opmode-other')?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >	If Other, describe</td>
        </tr>
        <tr>
          <td class="item  border1"  >&nbsp;</td>
          <td class="prio  border1"  >&nbsp;</td>
          <td class="val   border1"   ><?=textarea($sect.'-opmode-descr')?></td>
          <td class="unit  border1"  >&nbsp;</td>
          <td class="instr border1" >Describe reason for multiple operating modes and prioritization</td>
        </tr>
        <tr>
          <td class="item item_group noborder"  >Monochromator</td>
          <td class="prio            noborder"  >&nbsp;</td>
          <td class="val"   ><?=yes_no($sect.'-monochrom', true)?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item  border1"  >Bandwidth</td>
          <td class="prio  border1"  >&nbsp;</td>
          <td class="val   border1"   ><?=input($sect.'-bandwidth')?></td>
          <td class="unit  border1"  >eV</td>
          <td class="instr border1" ><?=$instr_manual.' if a mono is needed.'?></td>
        </tr>
        <tr>
          <td class="item item_group noborder"  >Energy Scanning</td>
          <td class="prio            noborder"  >&nbsp;</td>
          <td class="val"   ><?=yes_no($sect.'-energyscan')?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item noborder "  >↳</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=xray_energy_scanning($sect.'-energyscan-type')?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" ><?=$instr_select?> (please, provide if the previous was <b>Yes</b>)</td>
        </tr>
<?php for ($i = 1; $i <= 2; $i++) { $extra   = $i  < 2 ? "noborder" : "" ;
                                             $border1 = $i == 2 ? "border1" : "" ; ?>
        <tr>
          <td class="item  <?=$extra?> <?=$border1?>" ><?=($i === 1 ? "Scanning range" : "&nbsp;")?></td>
          <td class="prio  <?=$extra?> <?=$border1?>" ><?=$i?></td>
          <td class="val               <?=$border1?>" ><?=input($sect.'-energyscan-'.$i)?></td>
          <td class="unit  <?=$extra?> <?=$border1?>" ><?=($i === 1 ? "eV" : "&nbsp;")?></td>
          <td class="instr <?=$extra?> <?=$border1?>" ><?=($i === 1 ? $instr_manual.'. E.g., 7000-7050 eV' : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item item_group noborder" >Split-and-Delay</td>
          <td class="prio            noborder"  >&nbsp;</td>
          <td class="val"   ><?=xray_split_and_delay($sect.'-split')?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item  border1"  >&nbsp;</td>
          <td class="prio  border1"  >&nbsp;</td>
          <td class="val   border1"   ><?=textarea($sect.'-split-other')?></td>
          <td class="unit  border1"  >&nbsp;</td>
          <td class="instr border1" >If Other, describe</td>
        </tr>
        <tr>
          <td class="item  item_group border1" >Other X-ray Requirements</td>
          <td class="prio             border1" >&nbsp;</td>
          <td class="val              border1" ><?=textarea($sect.'-other')?></td>
          <td class="unit             border1" >&nbsp;</td>
          <td class="instr            border1" >Please list and describe additional requirements for
                                                the x-ray parameters and x-ray optics, etc.</td>
        </tr>
      </tbody>
    </table>
  </div>

<?php
        function optical_laser_needed ($id) {
            $str =<<<HERE
<select id="{$id}" >
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
            return $str ;
        }
        function optical_laser_advanced ($id) {
            $str =<<<HERE
<select id="{$id}" >
  <option> </option>
  <option> HE-TOPAS (High energy OPA) </option>
  <option> Hollow Fiber (&lt;10fs, 800nm) </option>
  <option> ns OPO (Tunable 210-2200nm) </option>
  <option> ns Evolution (527nm) </option>
</select>
HERE;
            return $str ;
        }
        function optical_laser_wavelength ($id) {
            $str =<<<HERE
<select id="{$id}" >
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
            return $str ;
        }
        function optical_laser_pulse_duration ($id) {
            $str =<<<HERE
<select id="{$id}" >
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
            return $str ;
        }

        $sect = 'laser' ;
?>
    
  <div id="laser" >
    <div class="laser-sect" >
      <div class="comments">
        This page covers basic optical laser parameters. Most standard options
        can be chosen from the drop down menu. If the desired configurations
        or parameters are not listed, please describe with more details in
        the ‘additional information’ field.
      </div>
    </div>
    <table class="standard" >
      <?=$thead?>
      <tbody>
        <tr>
          <td class="item item_group noborder" >Optical Laser Needed </td>
          <td class="prio noborder"            >&nbsp;</td>
          <td class="val"                      ><?=optical_laser_needed($sect.'-mode')?></td>
          <td class="unit"                     >&nbsp;</td>
          <td class="instr"                    ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item  border1"  >&nbsp;</td>
          <td class="prio  border1"  >&nbsp;</td>
          <td class="val   border1"   ><?=textarea($sect.'-mode-other')?></td>
          <td class="unit  border1"  >&nbsp;</td>
          <td class="instr border1" >If Other, describe</td>
        </tr>
        <tr>
          <td class="item item_group noborder" >Advanced </td>
          <td class="prio noborder"            >&nbsp;</td>
          <td class="val"                      ><?=optical_laser_advanced($sect.'-adv')?></td>
          <td class="unit"                     >&nbsp;</td>
          <td class="instr"                    ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item noborder" >Multiple Simultaneous Beams? </td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=yes_no($sect.'-adv-simbeams')?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item noborder" >↳</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=input($sect.'-adv-simbeams-number')?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         >If Yes, How Many?</td>
        </tr>
        <tr>
          <td class="item border1"  >&nbsp;</td>
          <td class="prio  border1" >&nbsp;</td>
          <td class="val   border1" ><?=textarea($sect.'-adv-descr')?></td>
          <td class="unit  border1" >&nbsp;</td>
          <td class="instr border1" >Describe Needs and State Priorities</td>
        </tr>
<?php for ($i = 1; $i <= 5; $i++) { $extra = $i < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item item_group noborder" ><?=($i === 1 ? "Wavelength" : "&nbsp;")?></td>
          <td class="prio         <?=$extra?>" ><?=$i?></td>
          <td class="val"                      ><?=optical_laser_wavelength($sect.'-wavelength-'.$i)?></td>
          <td class="unit         <?=$extra?>" >&nbsp;</td>
          <td class="instr        <?=$extra?>" ><?=($i === 1 ? $instr_select : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder" >↳</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=input($sect.'-wavelength-other')?></td>
          <td class="unit"          >nm</td>
          <td class="instr"         >If Other or in a range, specify</td>
        </tr>
        <tr>
          <td class="item  border1" >&nbsp;</td>
          <td class="prio  border1" >&nbsp;</td>
          <td class="val   border1" ><?=textarea($sect.'-wavelength-descr')?></td>
          <td class="unit  border1" >&nbsp;</td>
          <td class="instr border1" >Describe reason for multiple wavelengths and prioritization</td>
        </tr>
<?php for ($i = 1; $i <= 5; $i++) { $extra   = $i  < 5 ? "noborder" : "" ;
                                    $border1 = $i == 5 ? "border1" : "" ; ?>
        <tr>
          <td class="item item_group <?=$extra?> <?=$border1?>" ><?=($i === 1 ? "Pulse Energy Min-Max" : "&nbsp;")?></td>
          <td class="prio            <?=$extra?> <?=$border1?>" ><?=$i?></td>
          <td class="val                         <?=$border1?>" ><?=input($sect.'-energy-'.$i)?></td>
          <td class="unit            <?=$extra?> <?=$border1?>" ><?=($i === 1 ? "mJ" : "&nbsp;")?></td>
          <td class="instr           <?=$extra?> <?=$border1?>" ><?=($i === 1 ? "{$instr_manual}, e.g. 0.1-3" : "&nbsp;")?></td>
        </tr>
<?php } ?>
<?php for ($i = 1; $i <= 5; $i++) { $extra = $i < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item item_group noborder" ><?=($i === 1 ? "X-ray spot size (FWHM)" : "&nbsp;")?></td>
          <td class="prio         <?=$extra?>" ><?=$i?></td>
          <td class="val"                      ><?=input($sect.'-spot-'.$i)?></td>
          <td class="unit         <?=$extra?>" ><?=($i === 1 ? "um" : "&nbsp;")?></td>
          <td class="instr        <?=$extra?>" ><?=($i === 1 ? $instr_manual: "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item  border1"  >&nbsp;</td>
          <td class="prio  border1"  >&nbsp;</td>
          <td class="val   border1"  ><?=textarea($sect.'-spot-descr')?></td>
          <td class="unit  border1"  >&nbsp;</td>
          <td class="instr border1 " >Describe reason for multiple spot sizes and prioritization</td>
        </tr>
<?php for ($i = 1; $i <= 5; $i++) { $extra = $i < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item item_group noborder"  ><?=($i === 1 ? "Fluence" : "&nbsp;")?></td>
          <td class="prio          <?=$extra?>" ><?=$i?></td>
          <td class="val"                       ><?=input($sect.'-fluence-'.$i)?></td>
          <td class="unit          <?=$extra?>" ><?=($i === 1 ? "mJ/cm<sup>2</sup>" : "&nbsp;")?></td>
          <td class="instr         <?=$extra?>" ><?=($i === 1 ? $instr_manual: "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item  border1" >&nbsp;</td>
          <td class="prio  border1" >&nbsp;</td>
          <td class="val   border1" ><?=textarea($sect.'-fluence-descr')?></td>
          <td class="unit  border1" >&nbsp;</td>
          <td class="instr border1" >Describe reason for multiple fluence and prioritization</td>
        </tr>
<?php for ($i = 1; $i <= 5; $i++) { $extra = $i < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item item_group noborder" ><?=($i === 1 ? "Pulse Duration (approx)" : "&nbsp;")?></td>
          <td class="prio         <?=$extra?>" ><?=$i?></td>
          <td class="val"                      ><?=optical_laser_pulse_duration($sect.'-duration-'.$i)?></td>
          <td class="unit         <?=$extra?>" >&nbsp;</td>
          <td class="instr        <?=$extra?>" ><?=($i === 1 ? $instr_select: "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder" >↳</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=textarea($sect.'-duration-other')?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         >If Other, specify</td>
        </tr>
        <tr>
          <td class="item  border1" >&nbsp;</td>
          <td class="prio  border1" >&nbsp;</td>
          <td class="val   border1" ><?=textarea($sect.'-duration-descr')?></td>
          <td class="unit  border1" >&nbsp;</td>
          <td class="instr border1" >Describe reason for multiple pulse durations and prioritization</td>
        </tr>
<?php for ($i = 1; $i <= 5; $i++) { $extra = $i < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item item_group noborder" ><?=($i === 1 ? "Temporal profile" : ($i === 2 ? "(particularly MEC glass laser)" : "&nbsp;"))?></td>
          <td class="prio            <?=$extra?>" ><?=$i?></td>
          <td class="val"                      ><?=input($sect.'-temporal-'.$i)?></td>
          <td class="unit         <?=$extra?>" >&nbsp;</td>
          <td class="instr        <?=$extra?>" ><?=($i === 1 ? "{$instr_manual} (e.g.top-hat)": "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item  border1" >&nbsp;</td>
          <td class="prio  border1" >&nbsp;</td>
          <td class="val   border1" ><?=textarea($sect.'-temporal-descr')?></td>
          <td class="unit  border1" >&nbsp;</td>
          <td class="instr border1" >Describe reason for multiple temporal profiles and prioritization</td>
        </tr>
<?php for ($i = 1; $i <= 5; $i++) { $extra = $i < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item item_group noborder" ><?=($i === 1 ? "Desired Intensity on Target" :  "&nbsp;")?></td>
          <td class="prio         <?=$extra?>" ><?=$i?></td>
          <td class="val"                      ><?=input($sect.'-intensity-'.$i)?></td>
          <td class="unit         <?=$extra?>" ><?=($i === 1 ? "W/cm<sup>2</sup>" :  "&nbsp;")?></td>
          <td class="instr        <?=$extra?>" ><?=($i === 1 ? $instr_manual: "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item  border1" >&nbsp;</td>
          <td class="prio  border1" >&nbsp;</td>
          <td class="val   border1" ><?=textarea($sect.'-intensity-descr')?></td>
          <td class="unit  border1" >&nbsp;</td>
          <td class="instr border1" >Describe reason for multiple intensities and prioritization</td>
        </tr>
        <tr>
          <td class="item item_group noborder" >Desired Time Resolution</td>
          <td class="prio            noborder" >&nbsp;</td>
          <td class="val"                      ><?=input($sect.'-timeres')?></td>
          <td class="unit"                     >fs</td>
          <td class="instr"                    ><?=$instr_manual?></td>
        </tr>
        <tr>
          <td class="item item_group border1" >Time Tool Needed?</td>
          <td class="prio            border1" >&nbsp;</td>
          <td class="val             border1" ><?=yes_no($sect.'-timetool')?></td>
          <td class="unit            border1" >&nbsp;</td>
          <td class="instr           border1" ><?=$instr_select?></td>
        </tr>

<?php
        function optical_laser_geometry ($id) {
            $str =<<<HERE
<select id="{$id}" >
  <option> </option>
  <option> Collinear with FEL (&lt;1 degree) </option>
  <option> Off axis from FEL </option>
  <option> Other </option>
</select>
HERE;
            return $str ;
        }
?>

<?php for ($i = 1; $i <= 5; $i++) { $extra = $i < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item item_group noborder" ><?=($i === 1 ? "Pump Laser Geometry" :  "&nbsp;")?></td>
          <td class="prio         <?=$extra?>" ><?=$i?></td>
          <td class="val"                      ><?=optical_laser_geometry($sect.'-geom-'.$i)?></td>
          <td class="unit         <?=$extra?>" >&nbsp;</td>
          <td class="instr        <?=$extra?>" ><?=($i === 1 ? $instr_select : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder" >↳</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=textarea($sect.'-geom-other')?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         >If Other, specify</td>
        </tr>
        <tr>
          <td class="item  border1" >&nbsp;</td>
          <td class="prio  border1" >&nbsp;</td>
          <td class="val   border1" ><?=textarea($sect.'-geom-descr')?></td>
          <td class="unit  border1" >&nbsp;</td>
          <td class="instr border1" >Describe reason for multiple geometries and prioritization</td>
        </tr>
<?php for ($i = 1; $i <= 9; $i++) { ?>
        <tr>
          <td class="item  item_group noborder" ><?=($i === 1 ? "User-supplied Laser Equipment" : "&nbsp;")?></td>
          <td class="prio  noborder"            >&nbsp;</td>
          <td class="val"                       ><?=input($sect.'-userequip-'.$i)?></td>
          <td class="unit  noborder"            >&nbsp;</td>
          <td class="instr noborder"            ><?=($i === 1 ? "Describe (1 item or system per line)." : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item  border1" >&nbsp;</td>
          <td class="prio  border1" >&nbsp;</td>
          <td class="val   border1" ><?=input($sect.'-userequip-10')?></td>
          <td class="unit  border1" >&nbsp;</td>
          <td class="instr border1" >&nbsp;</td>
        </tr>
<?php for ($i = 1; $i <= 9; $i++) { ?>
        <tr>
          <td class="item  item_group noborder" ><?=($i === 1 ? "Other Laser Requirements" : "&nbsp;")?></td>
          <td class="prio  noborder"            >&nbsp;</td>
          <td class="val"                       ><?=input($sect.'-other-'.$i)?></td>
          <td class="unit  noborder"            >&nbsp;</td>
          <td class="instr noborder"            ><?=($i === 1 ? "Describe (1 item or system per line)." : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=input($sect.'-other-10')?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >&nbsp;</td>
        </tr>
      </tbody>
    </table>
  </div>

<?php
        $sect = 'env' ;
?>

  <div id="env" >
    <table class="standard" >
      <?=$thead?>
      <tbody>

<?php
        function env_endstation ($id) {
            $str =<<<HERE
<select id="{$id}" >
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
            return $str ;
        }
        function env_sample_env ($id) {
            $str =<<<HERE
<select id="{$id}" >
  <option> </option>
  <option> Vacuum chamber </option>
  <option> Atmosphere </option>
  <option> Helium </option>
  <option> Other </option>
</select>
HERE;
            return $str ;
        }
        function env_liquid_sample ($id) {
            $str =<<<HERE
<select id="{$id}" >
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
            return $str ;
        }

        function env_gas_jet ($id) {
            $str =<<<HERE
<select id="{$id}" >
  <option> </option>
  <option> Piezo valve </option>
  <option> Even-Lawrie valve </option>
  <option> Supersonic Jet </option>
  <option> Gas Needle </option>
  <option> Other </option>
</select>
HERE;
            return $str ;
        }

        function env_spectrometer ($id) {
            $str =<<<HERE
<select id="{$id}" >
  <option> </option>
  <option> Electron TOF </option>
  <option> Ion TOF </option>
  <option> von Hamos (4 crystals) </option>
  <option> von Hamos (16 crystals) </option>
  <option> Rowland Circle </option>
  <option> Other </option>
</select>
HERE;
            return $str ;
        }

        function env_mec_diagnostics ($id) {
            $str =<<<HERE
<select id="{$id}" >
  <option> </option>
  <option> XRTS spectrometer in backward direction </option>
  <option> XRTS spectrometer in forward direction </option>
  <option> VISAR </option>
  <option> XUV spectrometer </option>
  <option> Other </option>
</select>
HERE;
            return $str ;
        }

        function env_lab_space ($id) {
            $str =<<<HERE
<select id="{$id}" >
  <option> </option>
  <option> Cleanroom </option>
  <option> Assmbly Space </option>
  <option> Sample Prep </option>
  <option> Wetlab </option>
  <option> Dark Room </option>
  <option> Other </option>
</select>
HERE;
            return $str ;
        }

?>
<?php for ($i = 1; $i <= 5; $i++) { $extra = $i < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item item_group noborder"     ><?=($i === 1 ? "X-ray Techniques to be Used" : "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$i?></td>
          <td class="val"               ><?=xray_techniques($sect.'-tech-'.$i)?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($i === 1 ? $instr_select : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder"  >&nbsp;</td>
          <td class="prio noborder"  >&nbsp;</td>
          <td class="val"   ><?=textarea($sect.'-tech-other')?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >If Other, describe</td>
        </tr>
        <tr>
          <td class="item  border1" >&nbsp;</td>
          <td class="prio  border1" >&nbsp;</td>
          <td class="val   border1" ><?=textarea($sect.'-tech-descr')?></td>
          <td class="unit  border1" >&nbsp;</td>
          <td class="instr border1" >Describe reason for multiple techniques and prioritization</td>
        </tr>
        <tr>
          <td class="item item_group noborder" >Scattering geometry</td>
          <td class="prio            "  >&nbsp;</td>
          <td class="val"   >&nbsp;</td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >&nbsp;</td>
        </tr>        <tr>
          <td class="item   noborder" >Desired Minimum Resolution</td>
          <td class="prio             noborder" >&nbsp;</td>
          <td class="val"                       ><?=input($sect.'-minres')?></td>
          <td class="unit             noborder" >A</td>
          <td class="instr            noborder" ><?=$instr_manual?></td>
        </tr>
        <tr>
          <td class="item  noborder" >↳ Maximum Resolution</td>
          <td class="prio  " >&nbsp;</td>
          <td class="val   " ><?=input($sect.'-maxres')?></td>
          <td class="unit  " >&nbsp;</td>
          <td class="instr " >&nbsp;</td>
        </tr>
        <tr>
          <td class="item  noborder" >Desired Minimum Q-value</td>
          <td class="prio  noborder" >&nbsp;</td>
          <td class="val"            ><?=input($sect.'-minqval')?></td>
          <td class="unit  noborder" >A<sup>-1</sup></td>
          <td class="instr noborder" ><?=$instr_manual?></td>
        </tr>
        <tr>
          <td class="item  noborder" >↳ Maximum Q-value</td>
          <td class="prio  " >&nbsp;</td>
          <td class="val   " ><?=input($sect.'-maxqval')?></td>
          <td class="unit  " >&nbsp;</td>
          <td class="instr border1" >&nbsp;</td>
        </tr>
        <tr>
          <td class="item  noborder" >Desired Minimum Sample-Detector Distance</td>
          <td class="prio  noborder" >&nbsp;</td>
          <td class="val"            ><?=input($sect.'-mindist')?></td>
          <td class="unit  noborder" >mm</td>
          <td class="instr noborder" ><?=$instr_manual?></td>
        </tr>
        <tr>
          <td class="item  border1" >↳ Maximum Distance</td>
          <td class="prio  border1" >&nbsp;</td>
          <td class="val   border1" ><?=input($sect.'-maxdist')?></td>
          <td class="unit  border1" >&nbsp;</td>
          <td class="instr border1" >&nbsp;</td>
        </tr>
<?php for ($i = 1; $i <= 3; $i++) { $extra = $i < 3 ? "noborder" : "" ; ?>
        <tr>
          <td class="item item_group noborder"     ><?=($i === 1 ? "Specific Endstation" :  "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$i?></td>
          <td class="val"               ><?=env_endstation($sect.'-endstation-'.$i)?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($i === 1 ? $instr_select : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder" >↳</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=textarea($sect.'-endstation-other')?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         >If Other or User supplied, describe</td>
        </tr>
        <tr>
          <td class="item  border1" >&nbsp;</td>
          <td class="prio  border1" >&nbsp;</td>
          <td class="val   border1" ><?=textarea($sect.'-endstation-decsr')?></td>
          <td class="unit  border1" >&nbsp;</td>
          <td class="instr border1" >Describe reason for multiple needs and prioritization</td>
        </tr>
<?php for ($i = 1; $i <= 9; $i++) { ?>
        <tr>
          <td class="item  item_group noborder" ><?=($i === 1 ? "Samples" : "&nbsp;")?></td>
          <td class="prio             noborder" >&nbsp;</td>
          <td class="val"                       ><?=input($sect.'-userequip-'.$i)?></td>
          <td class="unit             noborder" >&nbsp;</td>
          <td class="instr            noborder" ><?=($i === 1 ? "List all samples you intent to measure during your beamline" : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item"  >&nbsp;</td>
          <td class="prio"  >&nbsp;</td>
          <td class="val"   ><?=input($sect.'-userequip-10')?></td>
          <td class="unit"  >&nbsp;</td>
          <td class="instr" >&nbsp;</td>
        </tr>
        <tr>
          <td class="item item_group noborder" >Sample Environment</td>
          <td class="prio            noborder" >&nbsp;</td>
          <td class="val"           ><?=env_sample_env($sect.'-sampleenv')?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item  border1"  >↳</td>
          <td class="prio  border1"  >&nbsp;</td>
          <td class="val   border1"   ><?=textarea($sect.'-sampleenv-other')?></td>
          <td class="unit  border1"  >&nbsp;</td>
          <td class="instr border1" >If Other, describe</td>
        </tr>
        <tr>
          <td class="item item_group border1"  >Sample Temperature</td>
          <td class="prio            border1"  >&nbsp;</td>
          <td class="val             border1"   ><?=input($sect.'-temperature-')?></td>
          <td class="unit            border1"  >K</td>
          <td class="instr           border1" ><?=$instr_manual?></td>
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
          <td class="val"           ><?=yes_no($sect.'-fixtarget')?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         ><?=$instr_select?></td>
        </tr>
        <tr>
          <td class="item noborder" >Scanning rate</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=input($sect.'-scanrate')?></td>
          <td class="unit"          >Hz</td>
          <td class="instr"         ><?=$instr_manual?></td>
        </tr>
        <tr>
          <td class="item noborder" >Scanning speed</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=input($sect.'-scanspeed')?></td>
          <td class="unit"          >mm/sec</td>
          <td class="instr"         ><?=$instr_manual?></td>
        </tr>
<?php for ($i = 1; $i <= 3; $i++) { $extra = $i < 3 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($i === 1 ? "Liquid Sample Needs" :  "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$i?></td>
          <td class="val"               ><?=env_liquid_sample($sect.'-liquid-'.$i)?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($i === 1 ? $instr_select : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder"  >↳</td>
          <td class="prio   border1" >&nbsp;</td>
          <td class="val    border1" ><?=textarea($sect.'-liquid-other')?></td>
          <td class="unit   border1" >&nbsp;</td>
          <td class="instr  border1" >If Other, describe</td>
        </tr>
<?php for ($i = 1; $i <= 4; $i++) { $extra = $i < 4 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($i === 1 ? "Gas Jet Needs" :  "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$i?></td>
          <td class="val"               ><?=env_gas_jet($sect.'-gas-'.$i)?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($i === 1 ? $instr_select : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder" >↳</td>
          <td class="prio  border1" >&nbsp;</td>
          <td class="val   border1" ><?=textarea($sect.'-gas-other')?></td>
          <td class="unit  border1" >&nbsp;</td>
          <td class="instr border1" >If Other, describe</td>
        </tr>
        <tr>
          <td class="item noborder" >Aerosol Injector Needs</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=yes_no($sect.'-aerosol')?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         ><?=$instr_select?></td>
        </tr>
<?php for ($i = 1; $i <= 5; $i++) { $extra = $i < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item noborder"     ><?=($i === 1 ? "Other Injector Needs" : "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$i?></td>
          <td class="val"               ><?=input($sect.'-sample-other-'.$i)?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($i === 1 ? "Describe (1 item or system per line)" : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item  border1" >&nbsp;</td>
          <td class="prio  border1" >&nbsp;</td>
          <td class="val   border1" ><?=textarea($sect.'-sample-other-descr')?></td>
          <td class="unit  border1" >&nbsp;</td>
          <td class="instr border1" >Describe reason for multiple sample handling and delivery methods and prioritization</td>
        </tr>
<?php for ($i = 1; $i <= 3; $i++) { $extra = $i < 3 ? "noborder" : "" ; ?>
        <tr>
          <td class="item item_group noborder"     ><?=($i === 1 ? "Spectrometers" :  "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$i?></td>
          <td class="val"               ><?=env_spectrometer($sect.'-spectro-'.$i)?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($i === 1 ? $instr_select : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder" >↳</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=textarea($sect.'-spectro-other')?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         >If Other, describe</td>
        </tr>
        <tr>
          <td class="item  border1" >&nbsp;</td>
          <td class="prio  border1" >&nbsp;</td>
          <td class="val   border1" ><?=textarea($sect.'-spectro-descr')?></td>
          <td class="unit  border1" >&nbsp;</td>
          <td class="instr border1" >Describe reason for multiple spectrometers and prioritization</td>
        </tr>
<?php for ($i = 1; $i <= 5; $i++) { $extra = $i < 5 ? "noborder" : "" ; ?>
        <tr>
          <td class="item item_group noborder"     ><?=($i === 1 ? "MEC Diagnostics" :  "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$i?></td>
          <td class="val"               ><?=env_mec_diagnostics($sect.'-diag-'.$i)?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($i === 1 ? $instr_select : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder" >↳</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=textarea($sect.'-diag-other')?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         >If Other, describe</td>
        </tr>
        <tr>
          <td class="item  border1" >&nbsp;</td>
          <td class="prio  border1" >&nbsp;</td>
          <td class="val   border1" ><?=textarea($sect.'-diag-decsr')?></td>
          <td class="unit  border1" >&nbsp;</td>
          <td class="instr border1" >Describe reason for multiple diagnostics and prioritization</td>
        </tr>
<?php for ($i = 1; $i <= 9; $i++) { ?>
        <tr>
          <td class="item  item_group noborder" ><?=($i === 1 ? "User-Supplied Equipment" : "&nbsp;")?></td>
          <td class="prio  noborder" >&nbsp;</td>
          <td class="val"            ><?=input($sect.'-userenv-'.$i)?></td>
          <td class="unit  noborder" >&nbsp;</td>
          <td class="instr noborder" ><?=($i === 1 ? "Describe (1 item or system per line)." : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item  border1"  >&nbsp;</td>
          <td class="prio  border1"  >&nbsp;</td>
          <td class="val   border1"   ><?=input($sect.'-userenv-10')?></td>
          <td class="unit  border1"  >&nbsp;</td>
          <td class="instr border1" >&nbsp;</td>
        </tr>
<?php for ($i = 1; $i <= 4; $i++) { $extra = $i < 4 ? "noborder" : "" ; ?>
        <tr>
          <td class="item item_group noborder"     ><?=($i === 1 ? "Laboratory Space Needed" :  "&nbsp;")?></td>
          <td class="prio  <?=$extra?>" ><?=$i?></td>
          <td class="val"               ><?=env_lab_space($sect.'-lab-'.$i)?></td>
          <td class="unit  <?=$extra?>" >&nbsp;</td>
          <td class="instr <?=$extra?>" ><?=($i === 1 ? $instr_select : "&nbsp;")?></td>
        </tr>
<?php } ?>
        <tr>
          <td class="item noborder" >↳</td>
          <td class="prio"          >&nbsp;</td>
          <td class="val"           ><?=textarea($sect.'-lab-other')?></td>
          <td class="unit"          >&nbsp;</td>
          <td class="instr"         >If Other, describe</td>
        </tr>
      </tbody>
    </table>
  </div>

<?php

    $sect = 'contr' ;

    function contr_comp_os ($id) {
        $str =<<<HERE
<select id="{$id}" >
  <option> </option>
  <option> Windows </option>
  <option> Mac OS </option>
  <option> iOS </option>
  <option> Linux </option>
  <option> Crome OS </option>
  <option> Android </option>
</select>
HERE;
        return $str ;
    }
    function contr_comp_location ($id) {
        $str =<<<HERE
<select id="{$id}" >
  <option> </option>
  <option> Hutch </option>
  <option> Control Room </option>
</select>
HERE;
        return $str ;
    }
?>
  
  <div id="contr" >
    
    <div class="control-sect" >
      <h1>1. USER Supplied Computers</h1>
      <div class="comments">
        Do you have computers, requiring networking or other connections to LCLS,
        required to control devices or read out detectors?  If so, please give
        the following details.
         <div class="quote" >
           Instructions to follow...
         </div>
      </div>
      <table>
        <thead>
          <tr>
            <td class="os"         > Operating System </td>
            <td class="location"   > Location     </td>
            <td class="connection" > Connections supplied by LCLS </td>
            <td class="purpose"    > Purpose      </td>
          </tr>
        </thead>
        <tbody>
<?php for ($i = 1; $i <= 6; $i++) { $extra = $i < 6 ? "noborder" : "" ; ?>
          <tr>
            <td class="os"         ><?=contr_comp_os      ($sect.'-usercomp-'.$i.'-os'      )?></td>
            <td class="location"   ><?=contr_comp_location($sect.'-usercomp-'.$i.'-location')?></td>
            <td class="connection" ><?=textarea           ($sect.'-usercomp-'.$i.'-conn', 16)?></td>
            <td class="purpose"    ><?=textarea           ($sect.'-usercomp-'.$i.'-purpose' )?></td>
          </tr>
<?php } ?>
        </tbody>
      </table>
    </div>

    <div class="control-sect" >
      <h1>2. Vacuum</h1>
      <div class="comments">
         Do you have vacuum needs outside of what is supplied in the LCLS beamline and
         end-station / chamber, including gauges, pumps, valves, etc. which require
         control and/or readback into the data stream, or integration (interlocks,
         pumping-routines, etc.) into the LCLS control system?  Please describe, noting
         which equipment you expect LCLS to provide, and that which you intend to bring.
      </div>
      <textarea id="<?=$sect.'-vacuum'?>" rows="12" cols="72"></textarea>
    </div>

    <div class="control-sect" >
      <h1>3. Cameras</h1>
      <div class="comments">
         LCLS supports a number of cameras (Web-cams, GigE, as well as camera-link: Pulnix,
         Opal-[1k,2k,4k], ...) for viewing and control.  Please list all viewing cameras you
         will need as well as those you intend to bring which will need support.  Give details
         such as frame-rate (free run or triggered), resolution, B&W/Color, bit-depth, etc.
         Describe any machine-intelligence (feedback and control applications) requirements
         for a camera.
         <div class="quote" >Note that for viewing applications, where beam-synchronous triggered
         acquisition isn’t necessary, webcams or GigE cameras are generally sufficient;
         for beam-synchronous, triggered viewing, an Opal camera will be required.
         If recording to the data-stream is necessary, the camera should be included in
         the DAQ section of this planning questionnaire.
         </div>
      </div>
      <textarea id="<?=$sect.'-cameras'?>" rows="12" cols="72"></textarea>
    </div>

<?php
    function contr_motor_type ($id) {
        $str =<<<HERE
<select id="{$id}" >
  <option> </option>
  <option> stepper </option>
  <option> newport </option>
  <option> pico </option>
  <option> piezo </option>
  <option> Other (specify in Purpose) </option>
</select>
HERE;
        return $str ;
    }
    function contr_motor_location ($id) {
        $str =<<<HERE
<select id="{$id}" >
  <option> </option>
  <option> Vacuum </option>
  <option> Air </option>
</select>
HERE;
        return $str ;
    }
    function contr_motor_provider ($id) {
        $str =<<<HERE
<select id="{$id}" >
  <option> </option>
  <option> LCLS </option>
  <option> USER </option>
</select>
HERE;
        return $str ;
    }
?>

    <div class="control-sect" >
      <h1>4. Motors</h1>
      <div class="comments">
          LCLS supports various flavors of 2-phase stepper motors, Newport motors, Pico motors,
          and Piezo motors.  Please fill out as much information as possible about your motion
          needs in the table below.  Note that stages which are part of fixed beamline hardware
          (ie: XPP detector robot arm stages) need not be quantified here; please list those that
          are part of an ad hoc experimental configuration.
      </div>
      <table>
        <thead>
          <tr>
            <td class="quantity"  > QTY </td>
            <td class="type"      > Type  </td>
            <td class="range"     > Approx. Range [mm] </td>
            <td class="precision" > Precision [um] </td>
            <td class="location"  > Location      </td>
            <td class="provider"  > Who provides </td>
            <td class="purpose"   > Purpose      </td>
          </tr>
        </thead>
        <tbody>
<?php for ($i = 1; $i <= 10; $i++) { $extra = $i < 10 ? "noborder" : "" ; ?>
          <tr>
            <td class="quantity"  ><?=select_quantity     ($sect.'-motors-'.$i.'-qty'     )?></td>
            <td class="type"      ><?=contr_motor_type    ($sect.'-motors-'.$i.'-type'    )?></td>
            <td class="range"     ><?=input               ($sect.'-motors-'.$i.'-range'   )?></td>
            <td class="precision" ><?=input               ($sect.'-motors-'.$i.'-prec'    )?></td>
            <td class="location"  ><?=contr_motor_location($sect.'-motors-'.$i.'-location')?></td>
            <td class="provider"  ><?=contr_motor_provider($sect.'-motors-'.$i.'-provider')?></td>
            <td class="purpose"   ><?=textarea            ($sect.'-motors-'.$i.'-purpose' )?></td>
          </tr> 
<?php } ?>
        </tbody>
      </table>
    </div>

    <div class="control-sect" >
      <h1>5. Power supplies</h1>
      <div class="comments">
          LCLS can provide several ranges of controlled high and low voltage DC power.
          Please list your requirements.  If you have your own supplies which you will bring,
          please list make and model, and a link to documentation & approving electrical
          standards bodies.  Note that user-supplied power supplies likely require electrical
          inspection by a SLAC official, please plan this into your pre-experiment availability.
      </div>

      <div class="control-subsect" >
        <h2>5.1 Supplied by LCLS:</h2>
        <table>
          <thead>
            <tr>
              <td class="quantity" > QTY     </td>
              <td class="voltage"  > Voltage (or range) </td>
              <td class="purpose"  > Purpose      </td>
            </tr>
          </thead>
          <tbody>
<?php for ($i = 1; $i <= 10; $i++) { $extra = $i < 10 ? "noborder" : "" ; ?>
            <tr>
              <td class="quantity" ><?=select_quantity($sect.'-ps-lcls-'.$i.'-qty'    )?></td>
              <td class="voltage"  ><?=input          ($sect.'-ps-lcls-'.$i.'-volt'   )?></td>
              <td class="purpose"  ><?=textarea       ($sect.'-ps-lcls-'.$i.'-purpose')?></td>
            </tr>
<?php } ?>
          </tbody>
        </table>
      </div>

      <div class="control-subsect" >
        <h2>5.2 Power supplies to be brought by USER</h2>
        <table>
          <thead>
            <tr>
              <td class="quantity" > QTY     </td>
              <td class="voltage"  > Voltage (or range), AC/DC </td>
              <td class="input"    > Input Voltage & Current </td>
              <td class="model"    > Make/Model </td>
              <td class="rating"   > Rating Agency  </td>
              <td class="link"     > Documentation Link </td>
              <td class="purpose"  > Purpose      </td>
            </tr>
          </thead>
          <tbody>
<?php for ($i = 1; $i <= 10; $i++) { $extra = $i < 10 ? "noborder" : "" ; ?>
            <tr>
              <td class="quantity" ><?=select_quantity($sect.'-ps-user-'.$i.'-qty'    )?></td>
              <td class="voltage"  ><?=input          ($sect.'-ps-user-'.$i.'-volt'   )?></td>
              <td class="input"    ><?=input          ($sect.'-ps-user-'.$i.'-input'  )?></td>
              <td class="model"    ><?=input          ($sect.'-ps-user-'.$i.'-model'  )?></td>
              <td class="rating"   ><?=input          ($sect.'-ps-user-'.$i.'-rating' )?></td>
              <td class="link"     ><?=input          ($sect.'-ps-user-'.$i.'-link'   )?></td>
              <td class="purpose"  ><?=textarea       ($sect.'-ps-user-'.$i.'-purpose')?></td>
            </tr>
<?php } ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="control-sect" >
      <h1>6. Temperature Control & Readback</h1>
      <div class="comments">
          List any temperature control or readback needs for your experiment.  Include
          a list of any equipment you will bring which requires integration into the LCLS
          controls or data acquisition system for control, monitoring, or data logging.
      </div>
      <textarea id="<?=$sect.'-temperature'?>" rows="12" cols="72"></textarea>
    </div>

    <div class="control-sect" >
      <h1>7. Other Outside Controlled Devices</h1>
      <div class="comments">
          Please enumerate all other devices you will bring which require remotely controlled
          interfaces or readback into a monitoring interface or the data acquisition system.
          Also any devices which are not NRTL listed and require inspection by a SLAC electrical
          safety officer.
      </div>
      <table>
        <thead>
          <tr>
            <td class="quantity"  > QTY </td>
            <td class="name"      > Name or description </td>
            <td class="model"     > Make/Model </td>
            <td class="interface" > Interfaces </td>
            <td class="used"      > Used previously at LCLS? </td>
            <td class="link"      > Documentation Link </td>
            <td class="purpose"   > Purpose, special requests      </td>
          </tr>
        </thead>
        <tbody>
<?php for ($i = 1; $i <= 10; $i++) { $extra = $i < 10 ? "noborder" : "" ; ?>
          <tr>
            <td class="quantity"  ><?=select_quantity($sect.'-devs-other-'.$i.'-qty'      )?></td>
            <td class="name"      ><?=input          ($sect.'-devs-other-'.$i.'-name'     )?></td>
            <td class="model"     ><?=input          ($sect.'-devs-other-'.$i.'-model'    )?></td>
            <td class="interface" ><?=input          ($sect.'-devs-other-'.$i.'-interface')?></td>
            <td class="used"      ><?=yes_no         ($sect.'-devs-other-'.$i.'-used'     )?></td>
            <td class="link"      ><?=input          ($sect.'-devs-other-'.$i.'-link'     )?></td>
            <td class="purpose"   ><?=textarea       ($sect.'-devs-other-'.$i.'-purpose'  )?></td>
          </tr>
<?php } ?>
        </tbody>
      </table>
    </div>

    <div class="control-sect" >
      <h1>8. Special Requests</h1>
      <div class="comments">
          If you have other controls needs, or requests for equipment provided by LCLS which do
          not fit into the areas above, please describe them here.
      </div>
      <textarea id="<?=$sect.'-special'?>" rows="12" cols="72"></textarea>
    </div>

  </div>

<?php

    $sect = 'data' ;

    function data_detectors ($id) {
        $str =<<<HERE
<select id="{$id}" >
  <option> </option>
  <option> SLAC-CsPAD </option>
  <option> SLAC-CsPAD Quad </option>
  <option> SLAC-CsPAD 2x2 </option>
  <option> SLAC Epix </option>
  <option> SLAC Epix10k </option>
  <option> SLAC EpixSampler </option>
  <option> MPI-pnCCD </option>
  <option> LBNL-FCCD </option>
  <option> LBNL-FCCD960 </option>
  <option> Adimec-Opal1000 </option>
  <option> Adimec-Opal2000 </option>
  <option> Adimec-Opal4000 </option>
  <option> Adimec-Opal8000 </option>
  <option> Adimec-Quartz4A150 </option>
  <option> Hamamatsu-ORCAFlash4.0 </option>
  <option> OceanOptics-HR4000 </option>
  <option> OceanOptics-USB4000 </option>
  <option> ASI-Timepix </option>
  <option> Rayonix-MX170HS </option>
  <option> Princeton-PI-MTE1300B </option>
  <option> Princeton-PI-MTE2048 </option>
  <option> Princeton-PIXIS </option>
  <option> Princeton-PI-MAX3 </option>
  <option> Andor-iKon 936L </option>
  <option> Andor-Newton 940P </option>
  <option> FLI-ML 16803-CCD </option>
  <option> Pulnix-TM4200 </option>
  <option> Phasics-SID4 HR </option>
  <option> Timetool </option>
</select>
HERE;
        return $str ;
    }
    function data_camera_binning ($id) {
        $str =<<<HERE
<select id="{$id}" >
  <option> Standard </option>
  <option> Non-standard </option>
</select>
HERE;
        return $str ;
    }
    function data_digitizers ($id) {
        $str =<<<HERE
<select id="{$id}" >
  <option> </option>
  <option> Agilent U1065A 8 GHz 100bit cPCI ADC </option>
  <option> Agilent U1051A 6-channel cPCI TDC </option>
  <option> SLAC IPIMB 4-channel Charge Amplifier/Peak Digitizer </option>
  <option> SLAC IMP 4-channel Charge Amplifier/Peak Digitizer </option>
  <option> GSC16AI32SSC 16 channel PCIe ADC </option>
</select>
HERE;
        return $str ;
    }
    function data_sampling_rate ($id) {
        $str =<<<HERE
<select id="{$id}" >
  <option> </option>
  <option> 2 Gs/s </option>
  <option> 4 GS/s </option>
  <option> 8 Gs/s </option>
</select>
HERE;
        return $str ;
    }
    function data_encoders ($id) {
        $str =<<<HERE
<select id="{$id}" >
  <option> </option>
  <option> SLE motor position encoder </option>
  <option> Encoder-US Digital USB4 </option>
</select>
HERE;
        return $str ;
    }

    function data_primary_location ($id) {
        $str =<<<HERE
<select id="{$id}" >
  <option> LCLS/SLAC </option>
  <option> NERSC </option>
  <option> Your home institution </option>
</select>
HERE;
        return $str ;
    }
?>

  <div id="data" >
  
    <div class="data-sect" >
      <h1>1. DAQ devices</h1>
      <div class="comments">
        <p>Please fill this section in collaboration with the LCLS point of contact 
        for your experiment.</p>
      </div>

      <table class="standard" >
        <thead>
          <tr>
            <td class="item"  > Item         </td>
            <td class="prio"  > Quantity     </td>
            <td class="val"   > Value        </td>
            <td class="instr" > Instructions </td>
          </tr>
        </thead>
        <tbody>
<?php for ($i = 1; $i <= 9; $i++) { $extra = $i < 9 ? "noborder" : "" ; ?>
          <tr>
            <td class="item item_group  noborder" ><?=($i === 1 ? "Cameras" : "&nbsp;")?></td>
            <td class="prio          <?=$extra?>" ><?=select_quantity($sect.'-dev-cam-'.$i.'-qty' )?></td>
            <td class="val"                       ><?=data_detectors ($sect.'-dev-cam-'.$i.'-type')?></td>
            <td class="instr         <?=$extra?>" ><?=($i === 1 ? $instr_select : "&nbsp;")?></td>
          </tr>
<?php } ?>
          <tr>
            <td class="item noborder" >↳ Camera binning requirements</td>
            <td class="prio noborder" >&nbsp;</td>
            <td class="val"           ><?=data_camera_binning($sect.'-dev-cam-binning')?></td>
            <td class="instr"         ><?="Hamamatsu, OceanOptics, Rayonix, Princeton, Andor, FLI-ML"?></td>
          </tr>
          <tr>
            <td class="item noborder" >&nbsp;</td>
            <td class="prio  border1" >&nbsp;</td>
            <td class="val   border1" ><?=textarea($sect.'-dev-cam-binning-descr')?></td>
            <td class="instr border1" >Please specify camera binning if binning will be used to increase
                                       the acquisition rate</td>
          </tr>
<?php for ($i = 1; $i <= 4; $i++) { $extra = $i < 4 ? "noborder" : "" ; ?>
          <tr>
            <td class="item item_group noborder" ><?=($i === 1 ? "Digitizers" : "&nbsp;")?></td>
            <td class="prio         <?=$extra?>" ><?=select_quantity($sect.'-dev-digi-'.$i.'-qty' )?></td>
            <td class="val"                      ><?=data_digitizers($sect.'-dev-digi-'.$i.'-type')?></td>
            <td class="instr        <?=$extra?>" ><?=($i === 1 ? $instr_select : "&nbsp;")?></td>
          </tr>
<?php } ?>
          <tr>
            <td class="item  noborder" >↳ Number of channels required?</td>
            <td class="prio  noborder" ><?=select_quantity($sect.'-dev-digi-chan-2gs-qty')?></td>
            <td class="val"            >@ 2 GS/s</td>
            <td class="instr noborder" >Agilent ADC</td>
          </tr>
          <tr>
            <td class="item  noborder" >&nbsp;</td>
            <td class="prio  noborder" ><?=select_quantity($sect.'-dev-digi-chan-4gs-qty')?></td>
            <td class="val"            >@ 4 GS/s</td>
            <td class="instr noborder" >&nbsp;</td>
          </tr>
          <tr>
            <td class="item  noborder" >&nbsp;</td>
            <td class="prio"           ><?=select_quantity($sect.'-dev-digi-chan-8gs-qty')?></td>
            <td class="val"            >@ 8 GS/s</td>
            <td class="instr"          >&nbsp;</td>
          </tr>
          <tr>
            <td class="item noborder"  >&nbsp;</td>
            <td class="prio   border1" >&nbsp;</td>
            <td class="val    border1" ><?=textarea($sect.'-dev-digi-comments')?></td>
            <td class="instr  border1" >Please, put your comments for Digitizers</td>
          </tr>
<?php for ($i = 1; $i <= 2; $i++) { $extra = $i < 2 ? "noborder" : "" ; ?>
          <tr>
            <td class="item item_group noborder" ><?=($i === 1 ? "Encoders" : "&nbsp;")?></td>
            <td class="prio         <?=$extra?>" ><?=select_quantity($sect.'-dev-encod-'.$i.'-qty' )?></td>
            <td class="val"                      ><?=data_encoders  ($sect.'-dev-encod-'.$i.'-type')?></td>
            <td class="instr        <?=$extra?>" ><?=($i === 1 ? $instr_select : "&nbsp;")?></td>
          </tr>
<?php } ?>
          <tr>
            <td class="item noborder"  >&nbsp;</td>
            <td class="prio   border1" >&nbsp;</td>
            <td class="val    border1" ><?=textarea($sect.'-dev-encod-comments')?></td>
            <td class="instr  border1" >Please, put your comments for Encoders</td>
          </tr>
<?php for ($i = 1; $i <= 5; $i++) { $extra = $i < 5 ? "noborder" : "" ; ?>
          <tr>
            <td class="item item_group noborder" ><?=($i === 1 ? "Other Device Needs" : "&nbsp;")?></td>
            <td class="prio         <?=$extra?>" ><?=select_quantity($sect.'-dev-other-'.$i.'-qty'  )?></td>
            <td class="val"                      ><?=input          ($sect.'-dev-other-'.$i.'-descr')?></td>
            <td class="instr        <?=$extra?>" ><?=($i === 1 ? "Describe (1 item or system per line)" : "&nbsp;")?></td>
          </tr>
<?php } ?>
          <tr>
            <td class="item   border1" >&nbsp;</td>
            <td class="prio   border1" >&nbsp;</td>
            <td class="val    border1" ><?=textarea($sect.'-dev-other-comments')?></td>
            <td class="instr  border1" >Please, put your comments for Other Devices</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="data-sect" >
      <h1>2. Online (real-time) Analysis</h1>
      <div class="comments">
        <p>Because critical experimental decisions must be made while an experiment 
        is in progress, it is important to analyze your data while data are 
        being taken.  The <b>DAQ</b> supports an online GUI called <b>AMI</b> that handles 
        common tasks using a click and play interface.  If a more specialized 
        analysis is necessary for your experiment, <b>psana-python</b> allows you to 
        perform real-time analysis using your own python analysis code.  In 
        addition, some users have written their own software to run against data 
        in shared memory, for example <b>CASS</b> and <b>onda</b>.  Please specify if you are 
        using user-supplied online analysis software and describe it in the 
        comments below.</p>

        <p>Note that the total number of monitoring nodes is not the same for all 
        instruments: <b>AMO</b>, <b>SXR</b>, <b>XPP</b> and <b>MFX</b> have 6 monitoring
        nodes, <b>CXI</b> has 12, <b>MEC</b> has 4 and <b>XCS</b> has 3.</p>
      </div>

      <table class="analysis" >
        <thead>
          <tr>
            <td class="item"  > Tool </td>
            <td class="val"   > Number of Monitoring Nodes </td>
            <td class="instr" > Instructions </td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="item noborder" >AMI</td>
            <td class="val"           ><?=input($sect.'-ana-ami')?></td>
            <td class="instr"         >Please, provide the number of nodes assigned to AMI</td>
          </tr>
          <tr>
            <td class="item noborder" >psana-python</td>
            <td class="val"           ><?=input($sect.'-ana-psana')?></td>
            <td class="instr"         >Please, provide the number of nodes assigned to psana-pyton</td>
          </tr>
          <tr>
            <td class="item noborder" >user code</td>
            <td class="val"           ><?=input($sect.'-ana-user')?></td>
            <td class="instr"         >Please, provide the number of nodes assigned to user code</td>
          </tr>
          <tr>
            <td class="item  border1" >&nbsp;</td>
            <td class="val   border1" ><?=textarea($sect.'-ana-comments')?></td>
            <td class="instr border1" >Please, put your comments for the Analysis</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="data-sect" >
      <h1>3. Offline Analysis</h1>
      <div class="comments">
        <p>The <b>psana-python</b> toolkit may also be used to analyze the data once
        they have been written to disk.  It has the advantage that the same code works 
        for online/offline analysis allowing you to do your entire analysis 
        chain with one tool.   It allows access to calibrated image and supports 
        fast parallel processing of data using <b>MPI</b>-parallelization.  If you wish 
        to learn more or get local support at <b>SLAC</b> for your analysis, please 
        indicate that you would like assistance and someone will contact you 
        prior to your experiment. It is now possible to transfer data to <b>NERSC</b> 
        and make use of <b>NERSC</b> supercomputers.</p>

        <p>Further instructions are found here:</p>
        <ul>
          <li><a class="link" href="https://confluence.slac.stanford.edu/display/PCDS/Computing"             target="_blank" >LCLS Computing Resources  </a></li>
          <li><a class="link" href="https://confluence.slac.stanford.edu/display/PCDS/Data+Retention+Policy" target="_blank" >LCLS Data Retention Policy</a></li>
          <li><a class="link" href="https://confluence.slac.stanford.edu/display/PSDM/LCLS+Data+Analysis"    target="_blank" >The psana-python toolkit  </a></li>
        </ul>
      </div>

      <table class="analysis" >
        <thead>
          <tr>
            <td class="item"  > Question </td>
            <td class="val"   > Answer </td>
            <td class="instr" > Instructions </td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="item noborder" >Assistance is needed?</td>
            <td class="val"           ><?=yes_no($sect.'-ana-assist')?></td>
            <td class="instr"         >Please, indicate <b>Yes</b> if you like someone from the analysis group
                                       to contact you before your experiment (highly recommended for
                                       first-time LCLS users)</td>
          <tr>
            <td class="item noborder" >Computing Resources</td>
            <td class="val"           ><?=data_primary_location($sect.'-ana-location')?></td>
            <td class="instr"         >Please, indicate where you're planning to analyze your data.
                                       If you're planing to use NERSC supercomputers to do your
                                       analysis then you may need to contact us ahead of time to
                                       get help with setting up a computer account at NERSC.</td>
          </tr>
            <tr>
            <td class="item  noborder" >&nbsp;</td>
            <td class="val"            ><?=textarea($sect.'-ana-other')?></td>
            <td class="instr"          >Please, put your other requirements for the Analysis</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php
}) ;
?>

</body>
</html>