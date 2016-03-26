<?php

/*
 * The summary of data provided for the Run 14 proposals
 *
 * AUTHORIZATION: LCLS crew
 */
require_once 'dataportal/dataportal.inc.php' ;

class TableGenerator1 {

    private $proposals = null ;
    private $contacts  = null ;
    private $infos     = null ;
    private $params    = null ;
    private $colnames  = array(
        'Proposal' ,
        'Instr' ,
        'POC'
    ) ;
    private $rowdefs = array() ;
    private $rows = null ;

    public function __construct ($proposals, $contacts, $infos, $params, $tabledef) {
        $this->proposals = $proposals ;
        $this->contacts  = $contacts ;
        $this->infos     = $infos ;
        $this->params    = $params ;
        foreach ($tabledef as $def) {
            if (is_array($def[1])) {
                $colnames = array() ;
                foreach ($def[1] as $subcoldef) {
                    array_push($colnames, $subcoldef[0]) ;
                    array_push($this->rowdefs, array($subcoldef[1], $subcoldef[2])) ;
                }
                array_push($this->colnames, array($def[0], $colnames)) ;
            } else {
                array_push($this->colnames, $def[0]) ;
                array_push($this->rowdefs, array($def[1], $def[2])) ;
            }
        }
    }
    private function populateRows() {
        if (is_null($this->rows)) {
            $this->rows = array() ;
            foreach ($this->proposals as $proposalNo) {
                if (array_key_exists($proposalNo, $this->params)) {
                    $this->addRow($proposalNo) ;
                }
            }
        }
    }
    private function addRow($proposalNo) {
        $row = array (
            "<a class=\"link\" href=\"run14_proposal_questionnaire?proposal={$proposalNo}\" target=\"_blank\">{$proposalNo}</a>" ,
            $this->infos[$proposalNo]->instrument() ,
            substr($this->contacts[$proposalNo], 0, strpos($this->contacts[$proposalNo], '('))
        ) ;
        foreach ($this->rowdefs as $def) {
            $id      = $def[0] ;
            $default = $def[1] ;
            array_push($row, $this->id2val($proposalNo, $id, $default)) ;
        }
        array_push($this->rows, $row) ;
    }
    private function id2val ($proposalNo, $id, $default='') {
        $dict = $this->params[$proposalNo] ;
        $val = array_key_exists($id, $dict) ? $dict[$id] : $default ;
        return $val ;
    }   
    public function toHtml() {

        $this->populateRows() ;

        $str  = "<table>" ;
        $str .= "<thead>" ;
        $str .= "<tr>" ;
        $second_tr = '' ;
        foreach ($this->colnames as $coldef) {
            if (is_array($coldef)) {
                if ($second_tr === '') $second_tr .= '<tr>' ;
            }
        }
        foreach ($this->colnames as $coldef) {
            if (is_array($coldef)) {
                $name    = $coldef[0] ;
                $colspan = count($coldef[1]) ;
                $str .= "<td colspan=\"{$colspan}\">{$name}</td>" ;
                if ($second_tr === '') $second_tr .= '<tr>' ;
                foreach ($coldef[1] as $subcolname) {
                    $second_tr .= "<td>{$subcolname}</td>" ;
                }
            } else {
                $name = $coldef ;
                if ($second_tr !== '') {
                    $str .= "<td rowspan=\"2\">{$name}</td>" ;
                } else {
                    $str .= "<td>{$name}</td>" ;
                }
            }
        }
        $str .= "</tr>" ;
        if ($second_tr !== '') {
            $second_tr .= '</tr>' ;
            $str .= $second_tr ;
        }
        $str .= "</thead>" ;
        $str .= "<tbody>" ;
        foreach ($this->rows as $row) {
            $str .= "<tr>" ;
            foreach ($row as $val) {
                $str .= "<td>{$val}</td>" ;
            }
            $str .= "</tr>" ;
        }
        $str .= "</tbody>" ;
        $str .= "</table>" ;
        return $str ;
    }
}

function subcoldefs($num, $idFmt, $default) {
    $result = array() ;
    for ($i = 1; $i <= $num; $i++)
        array_push($result ,
            array("{$i}", $idFmt, $default)) ;
    return $result ;
}

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
    
    $debug = $SVC->optional_int('debug', 0) ;
    if ($debug === 0) unset($debug) ;

    // Page access is restricted to the LCLS personell logged via the WebAuth
    // authentication system.
    $is_editor = $SVC->authdb()->hasRole($SVC->authdb()->authName(), null, 'ExperimentInfo', 'Editor') ;
    $is_reader = $SVC->authdb()->hasRole($SVC->authdb()->authName(), null, 'ExperimentInfo', 'Reader') ;
    $SVC->assert(
        $is_editor || $is_reader ,
        "We're sorry - you're not authorized to view this document") ;

    $infos     = array() ;
    $params    = array() ;
    $proposals = array() ;
    $contacts  = $SVC->regdb()->getProposalContacts_Run14() ;

    foreach ($contacts as $proposalNo => $contact) {

        $info = $SVC->safe_assign(
            $SVC->urawi()->proposalInfo($proposalNo, "proposal_basic.php") ,
            "No such proposal found: {$proposalNo}." ) ;

        $id2param = array() ;
        foreach ($SVC->regdb()->getProposalParams_Run14($proposalNo) as $param) {
            $id2param[$param['id']] = $param['val'] ;
        }
        $infos  [$proposalNo] = $info ;
        $params [$proposalNo] = $id2param ;

        array_push($proposals, $proposalNo) ;

        if (isset($debug) && count($params) >= $debug) break ;
    }

?>

<!doctype html>
<html>

<head>

<title>Run 14 Proposal Questionnaires: Reports</title>

<style>

body {
    margin:     0;
    padding:    0;
}
body * {
    font-family:    'Segoe UI',Tahoma,Helvetica,Arial,Verdana,sans-serif;
    font-size:      14px;
}
#title {
    padding:    20px;
    text-align: center;

    font-weight:    bold;
    font-size:      24px;
}
#tabs {
    padding:    0;
    background: 0;
    font-size:  11px;
}

/**************************************
 * Override JQuery UI styles for tabs *
 **************************************/

#tabs.ui-tabs .ui-tabs-nav li {
    border-bottom-width: 1px !important;
}
#tabs > ul {
    background: #f5f8f9 url(/jquery/css/custom-theme-1.9.1/images/ui-bg_inset-hard_100_f5f8f9_1x100.png) 50% 50% repeat-y;
}
#tabs > ul > li {
    border-radius:  0 !important;
}
#tabs > div {
    padding-top:    30px;
    padding-left:   30px;
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

/* Inner tabs should appear a bit small */
#tabs.inner > div {
    padding:    30px;
}
#tabs.inner > ul > li > a.ui-tabs-anchor {
    font-size:      12px;
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
    padding:        4px 12px;
    border-right:   solid 1px #b0b0b0;
    font-family:    verdana, sans-serif;
    font-size:      12px;
}
table > thead > tr:first-child > td:nth-child(1) ,
table > thead > tr:first-child > td:nth-child(2) ,
table > thead > tr:first-child > td:nth-child(3) ,
table > tbody                    td:nth-child(1) ,
table > tbody                    td:nth-child(2) ,
table > tbody                    td:nth-child(3) {
    background: #f5f8f9 url(/jquery/css/custom-theme-1.9.1/images/ui-bg_inset-hard_100_f5f8f9_1x100.png) 50% 50% repeat-y;
}
table > thead > tr:first-child > td:nth-child(3) ,
table > tbody                    td:nth-child(3) {
    border-right:   solid 1px #000000;
}
table > thead td {
    border-bottom:  solid 1px #000000;
    font-weight:    bold;
    white-space:    nowrap;
}
table > thead > tr:first-child td:last-child {
    border-right:   0;
}
table > tbody td:last-child {
    border-right:   0;
}
table > tbody td {
    border-bottom:  solid 1px #e0e0e0;
    padding-top:    4px;
    padding-bottom: 4px;
    vertical-align: top;
}
table > tbody td:nth-child(3) {
    white-space:    nowrap;
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

<link type="text/css" href="/jquery/css/custom-theme-1.9.1/jquery-ui.custom.css" rel="Stylesheet" />

<script type="text/javascript" src="/jquery/js/jquery.min.js"></script>
<script type="text/javascript" src="/jquery/js/jquery-ui-1.9.1.custom.min.js"></script>

<script>

$(function () {
    var tabs = $('body').children('#tabs').tabs() ;

    tabs.children('#p-xray')        .children('#tabs').tabs() ;
    tabs.children('#p-xraytech')    .children('#tabs').tabs() ;
    tabs.children('#p-laser')       .children('#tabs').tabs() ;
    tabs.children('#p-sample')      .children('#tabs').tabs() ;
    tabs.children('#p-detector')    .children('#tabs').tabs() ;
    tabs.children('#p-daq')         .children('#tabs').tabs() ;
//    tabs.children('#p-data-online') .children('#tabs').tabs() ;
//    tabs.children('#p-data-offline').children('#tabs').tabs() ;
    tabs.children('#p-user')        .children('#tabs').tabs() ;
//    tabs.children('#p-pre')         .children('#tabs').tabs() ;
//    tabs.children('#p-post')        .children('#tabs').tabs() ;
}) ;

</script>

</head>
<body>

<div id="title" >Run 14 Proposal Questionnaires: Reports</div>

<div id="tabs" >

  <ul>
    <li><a href="#p-xray"         >X-rays</a></li>
    <li><a href="#p-xraytech"     >X-Ray Technique & Endstation</a></li>
    <li><a href="#p-laser"        >Optical Laser</a></li>
    <li><a href="#p-sample"       >Sample Delivery and Environment</a></li>
    <li><a href="#p-detector"     >Detectors</a></li>
    <li><a href="#p-daq"          >DAQ</a></li>
    <li><a href="#p-data-online"  >Online Analysis</a></li>
    <li><a href="#p-data-offline" >Offline Analysis</a></li>
    <li><a href="#p-user"         >User Supplied Equipment</a></li>
    <li><a href="#p-pre"          >Pre-experiment & off-shift hutch activities</a></li>
    <li><a href="#p-post"         >Post-experiment needs</a></li>
  </ul>
  
  <div id="p-xray" >
    <div id="tabs" class="inner" >
      <ul>
        <li><a href="#p-xray-standard-config"     >Standard Configuration</a></li>
        <li><a href="#p-xray-photon-energies"     >Photon Energies</a></li>
        <li><a href="#p-xray-pulse-durations"     >Pulse Durations</a></li>
        <li><a href="#p-xray-pulse-energy"        >Pulse Energy</a></li>
        <li><a href="#p-xray-spot-size"           >X-ray Spot Size (FWHM)</a></li>
        <li><a href="#p-xray-operating-mode"      >Operating Mode</a></li>
        <li><a href="#p-xray-monochromator"       >Monochromator</a></li>
        <li><a href="#p-xray-energy-scanning"     >Energy Scanning</a></li>
        <li><a href="#p-xray-split-and-delay"     >Split-and-Delay</a></li>
        <li><a href="#p-xray-other-requirementrs" >Other X-ray Requirements</a></li>
      </ul>

      <div id="p-xray-standard-config" >
<?php
    $tabledef = array (
        array('Standard Configuration', 'xray-standard',  'Yes') ,
        array('Multiplexed',            'xray-multiplex', 'Yes') ,
        array('X-ray Mode',             'xray-mode',      'Continuous') ,
        array('X-ray Repetition Rate',  'xray-reprate',   '120')) ;

    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-xray-photon-energies" >
<?php
    $tabledef = array() ;
    
    for ($i = 1; $i <= 5; $i++)
        array_push($tabledef ,
            array("{$i}", "xray-energy-{$i}", '')) ;

    array_push($tabledef ,
        array('Reasons for prioritization', "xray-energy-descr", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-xray-pulse-durations" >
<?php
    $tabledef = array() ;
    
    for ($i = 1; $i <= 5; $i++)
        array_push($tabledef ,
            array("{$i}", "xray-pulse-{$i}", '')) ;

    array_push($tabledef ,
        array('Reasons for prioritization', "xray-pulse-descr", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-xray-pulse-energy" >
<?php
    $tabledef = array (
        array('Pulse Energy', 'xray-pulseenergy', 'Maximum')) ;

    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-xray-spot-size" >
<?php
    $tabledef = array () ;

    for ($i = 1; $i <= 5; $i++)
        array_push($tabledef ,
            array("{$i}", "xray-focal-{$i}", '')) ;

    array_push($tabledef ,
        array('Reasons for prioritization', "xray-focal-descr", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-xray-operating-mode" >
<?php
    $tabledef  = array() ;

    for ($i = 1; $i <= 5; $i++)
        array_push($tabledef ,
            array("{$i}", "xray-opmode-{$i}", '')) ;

    array_push($tabledef ,
        array('Other',                      "xray-opmode-other", '') ,
        array('Reasons for prioritization', "xray-opmode-descr", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-xray-monochromator" >
<?php
    $tabledef  = array (
        array('Monochromator', 'xray-monochrom', 'Yes') ,
        array('Bandwidth',     'xray-bandwidth', '')) ;

    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-xray-energy-scanning" >
<?php
    $tabledef  = array (
        array('Required', 'xray-energyscan',      '') ,
        array('Type',     'xray-energyscan-type', '') ,
        array('Range 1',  'xray-energyscan-1',    '') ,
        array('Range 2',  'xray-energyscan-2',    '')) ;

    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>
 
      <div id="p-xray-split-and-delay" >
<?php
    $tabledef  = array (
        array('Type',     'xray-split',       'Not Needed') ,
        array('If other', 'xray-split-other', '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-xray-other-requirementrs" >
<?php
    $tabledef  = array (
        array('Requirements', 'xray-other', '')) ;

    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>
    </div>
  </div>

  <div id="p-xraytech" >

    <div id="tabs" class="inner" >
    <ul>
      <li><a href="#p-xraytech-used"         >X-ray Techniques to be Used</a></li>
      <li><a href="#p-xraytech-endstation"   >Specific Endstation</a></li>
      <li><a href="#p-xraytech-spectrometer" >X-ray Spectrometers</a></li>
      <li><a href="#p-xraytech-mecdiag"      >MEC Diagnostics</a></li>
    </ul>

    <div id="p-xraytech-used" >
<?php
    $tabledef = array() ;

    for ($i = 1; $i <= 5; $i++)
        array_push ($tabledef ,
            array("{$i}", "xraytech-tech-{$i}", '')) ;

    array_push ($tabledef ,
        array('Other',                         "xraytech-tech-other", '') ,
        array('Reasons for prioritization',    "xraytech-tech-descr", '') ,
        array('Spatial Resol.',                "xraytech-spatialres", '') ,
        array('Energy Resol.',                 "xraytech-energyres",  '') ,
        array('Q-value', array(
            array('Min',                       "xraytech-minqval",    '') ,
            array('Max',                       "xraytech-maxqval",    ''))) ,
        array('Sample-Detector Distance: Min', "xraytech-mindetdist", '') ,
        array('Max',                           "xraytech-maxdetdist", '')) ;

    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-xraytech-endstation" >
<?php
    $tabledef = array() ;

    for ($i = 1; $i <= 3; $i++)
        array_push($tabledef ,
            array("{$i}", "env-endstation-{$i}", '')) ;

    array_push($tabledef ,
        array('Other',                      "xraytech-endstation-other", '') ,
        array('Reasons for prioritization', "xraytech-endstation-descr", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-xraytech-spectrometer" >
<?php
    $tabledef = array() ;

    for ($i = 1; $i <= 3; $i++)
        array_push($tabledef ,
            array("{$i}", "xraytech-spectro-{$i}", '')) ;

    array_push($tabledef ,
        array('Other',                      "xraytech-spectro-other", '') ,
        array('Reasons for prioritization', "xraytech-spectro-descr", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-xraytech-mecdiag" >
<?php
    $tabledef = array() ;

    for ($i = 1; $i <= 5; $i++)
        array_push($tabledef ,
            array("{$i}", "xraytech-mecdiag-{$i}", '')) ;

    array_push($tabledef ,
        array('Other',                      "xraytech-mecdiag-other", '') ,
        array('Reasons for prioritization', "xraytech-mecdiag-descr", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

    </div>
  </div>

  <div id="p-laser" >

    <div id="tabs" class="inner" >
      <ul>
        <li><a href="#p-laser-general"      >General Requirements</a></li>
        <li><a href="#p-laser-advanced"     >Advanced</a></li>
        <li><a href="#p-laser-wavelength"   >Wavelength</a></li>
        <li><a href="#p-laser-pulse-energy" >Pulse Energy Min-Max</a></li>
        <li><a href="#p-laser-spot-size"    >Laser Spot Size (FWHM)</a></li>
        <li><a href="#p-laser-fluence"      >Fluence</a></li>
        <li><a href="#p-laser-duration"     >Pulse Duration</a></li>
        <li><a href="#p-laser-temporal"     >Temporal Profile</a></li>
        <li><a href="#p-laser-intensity"    >Desired Intensity on Target</a></li>
        <li><a href="#p-laser-timeres"      >Time Tool</a></li>
        <li><a href="#p-laser-geom"         >Pump Laser Geometry</a></li>
      </ul>

      <div id="p-laser-general" >
<?php
    $tabledef = array (
        array('Mode',  'laser-mode',       '') ,
        array('Other', 'laser-mode-other', '')) ;

    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-laser-advanced" >
<?php
    $tabledef = array (
        array('Mode',                        'laser-adv',                 '') ,
        array('Multiple Simultaneous Beams', 'laser-adv-simbeams',        'No') ,
        array('# of beams',                  'laser-adv-simbeams-number', '') ,
        array('Needs and priorities',        'laser-adv-descr',           '')) ;

    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-laser-wavelength" >
<?php
    $tabledef = array() ;

    for ($i = 1; $i <= 5; $i++)
        array_push ($tabledef ,
            array("{$i}", "laser-wavelength-{$i}", '')) ;

    array_push ($tabledef ,
        array('Other',                      "laser-wavelength-other", '') ,
        array('Reasons for prioritization', "laser-wavelength-descr", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-laser-pulse-energy" >
<?php
    $tabledef = array() ;

    for ($i = 1; $i <= 5; $i++)
        array_push ($tabledef ,
            array("{$i}", "laser-energy-{$i}", '')) ;

    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-laser-spot-size" >
<?php
    $tabledef = array() ;

    for ($i = 1; $i <= 5; $i++)
        array_push ($tabledef ,
            array("{$i}", "laser-spot-{$i}", '')) ;

    array_push ($tabledef ,
        array('Reasons for prioritization', "laser-spot-descr", '')) ;

    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-laser-fluence" >
<?php
    $tabledef = array() ;

    for ($i = 1; $i <= 5; $i++)
        array_push ($tabledef ,
            array("{$i}", "laser-fluence-{$i}", '')) ;

    array_push ($tabledef ,
        array('Reasons for prioritization', "laser-fluence-descr", '')) ;

    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-laser-duration" >
<?php
    $tabledef = array() ;

    for ($i = 1; $i <= 5; $i++)
        array_push ($tabledef ,
            array("{$i}", "laser-duration-{$i}", '')) ;

    array_push ($tabledef ,
        array('Other',                      "laser-duration-other", '') ,
        array('Reasons for prioritization', "laser-duration-descr", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-laser-temporal" >
<?php
    $tabledef = array() ;

    for ($i = 1; $i <= 5; $i++)
        array_push ($tabledef ,
            array("{$i}", "laser-temporal-{$i}", '')) ;

    array_push ($tabledef ,
        array('Reasons for prioritization', "laser-temporal-descr", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-laser-intensity" >
<?php
    $tabledef = array() ;

    for ($i = 1; $i <= 5; $i++)
        array_push ($tabledef ,
            array("{$i}", "laser-intensity-{$i}", '')) ;

    array_push ($tabledef ,
        array('Reasons for prioritization', "laser-intensity-descr", '')) ;

    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-laser-timeres" >
<?php    
    $tabledef = array (
        array("Desired Time Resolution", "laser-timeres",  '') ,
        array("Time Tool Needed?",       "laser-timetool", 'No')) ;

    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-laser-geom" >
<?php
    $tabledef = array() ;

    for ($i = 1; $i <= 5; $i++)
        array_push ($tabledef ,
            array("{$i}", "laser-geom-{$i}", '')) ;

    array_push ($tabledef ,
        array('Other',                      "laser-geom-other", '') ,
        array('Reasons for prioritization', "laser-geom-descr", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

    </div>
  </div>   


  <div id="p-sample" >

    <div id="tabs" class="inner" >
      <ul>
        <li><a href="#p-sample-samples"        >Samples</a></li>
        <li><a href="#p-sample-env"            >Environment</a></li>
        <li><a href="#p-sample-delivery"       >Delivery Method</a></li>
        <li><a href="#p-sample-userequipment"  >User-supplied Equipment</a></li>
        <li><a href="#p-sample-reservoir"      >Reservoir</a></li>
        <li><a href="#p-sample-temperature"    >Temperature Control</a></li>
        <li><a href="#p-sample-pershift"       >Reservoirs per shift</a></li>
        <li><a href="#p-sample-provided"       >Delivery equipment provided by LCLS</a></li>
        <li><a href="#p-sample-amount"         >Sample amount expected</a></li>
        <li><a href="#p-sample-reservoir-temp" >Reservoir temperature</a></li>
        <li><a href="#p-sample-nozzle"         >Desired nozzle diameter</a></li>
        <li><a href="#p-sample-special"        >Special requirements</a></li>
        <li><a href="#p-sample-reserve"        >Reserve Injector Characterization Lab</a></li>
      </ul>

      <div id="p-sample-samples" >
<?php
    $tabledef = array () ;

    for ($i = 1; $i <= 10; $i++)
        array_push($tabledef ,
            array("{$i}", "sample-samples-{$i}", '')) ;

    array_push($tabledef ,
        array('Additional sample info', "sample-samples-descr", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?> 
      </div>

      <div id="p-sample-env" >
<?php
    $tabledef = array () ;

    array_push($tabledef ,
        array("Sample Environment", "sample-sampleenv",       '') ,
        array('Other',              "sample-sampleenv-other", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?> 
      </div>

      <div id="p-sample-delivery" >
<?php
    $tabledef = array () ;

    for ($i = 1; $i <= 10; $i++)
        array_push($tabledef ,
            array("{$i}", "sample-deliverymethod-{$i}", '')) ;

    array_push($tabledef ,
        array('Other Method', "sample-deliverymethod-other", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?> 
      </div>

      <div id="p-sample-userequipment" >
<?php
    $tabledef = array () ;

    array_push($tabledef ,
        array("Status", "sample-userequipment", 'No')) ;

    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?> 
      </div>

      <div id="p-sample-reservoir" >
<?php
    $tabledef = array () ;

    for ($i = 1; $i <= 10; $i++)
        array_push($tabledef ,
            array("{$i}", "sample-reservoir-{$i}", '')) ;

    array_push($tabledef ,
        array('Other Reservoir', "sample-reservoir-other", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?> 
      </div>

      <div id="p-sample-temperature" >
<?php
    $tabledef = array () ;

    array_push($tabledef ,
        array("Min", "sample-min-temperature", '') ,
        array('Max', "sample-max-temperature", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?> 
      </div>
      
      <div id="p-sample-pershift" >
<?php
    $tabledef = array () ;

    array_push($tabledef ,
        array("Estimated number", "sample-reservoir-pershift", '0')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?> 
      </div>

      <div id="p-sample-provided" >
<?php
    $tabledef = array () ;

    for ($i = 1; $i <= 10; $i++)
        array_push($tabledef ,
            array("{$i}", "sample-provided-{$i}", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?> 
      </div>

      <div id="p-sample-amount" >
<?php
    $tabledef = array () ;

    array_push($tabledef ,
        array("Notes", "sample-sample-amount", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?> 
      </div>

      <div id="p-sample-reservoir-temp" >
<?php
    $tabledef = array () ;

    array_push($tabledef ,
        array("Temperature", "sample-reservoir-temp", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?> 
      </div>

      <div id="p-sample-nozzle" >
<?php
    $tabledef = array () ;

    array_push($tabledef ,
        array("Diameter", "sample-nozzle",       '') ,
        array("Other",    "sample-nozzle-other", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?> 
      </div>

      <div id="p-sample-special" >
<?php
    $tabledef = array () ;

    array_push($tabledef ,
        array("Notes", "sample-special", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?> 
      </div>

      <div id="p-sample-reserve" >
<?php
    $tabledef = array () ;

    array_push($tabledef ,
        array("Notes, dates, etc.", "sample-reserve", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?> 
      </div>

    </div>
  </div>

  <div id="p-detector" >

    <div id="tabs" class="inner" >
      <ul>
        <li><a href="#p-detector-device"      >Devices</a></li>
        <li><a href="#p-detector-other"       >Other Detector Environment</a></li>
        <li><a href="#p-detector-filter"      >Environment Filters</a></li>
        <li><a href="#p-detector-orientation" >Non-standard Orientation</a></li>
      </ul>

      <div id="p-detector-device" >
<?php
    $devices = array() ;
    
    for ($i = 1; $i <= 9; $i++) {
        array_push($devices ,
            array("Type",   "detector-slac-{$i}-type",  '') ,
            array("#",      "detector-slac-{$i}-qty",    '0') ,
            array("Env",    "detector-slac-{$i}-env",    '') ,
            array("Orient", "detector-slac-{$i}-orient", '')) ;
    }
    $tabledef = array(
        array('Device', $devices)) ;

    $tgen = new TableGenerator1 (
        $proposals ,
        $contacts ,
        $infos ,
        $params ,
        $tabledef
    ) ;
    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-detector-other" >
<?php

    $tabledef = array(
        array('Comments', "detector-slac-other", '')) ;

    $tgen = new TableGenerator1 (
        $proposals ,
        $contacts ,
        $infos ,
        $params ,
        $tabledef
    ) ;
    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-detector-filter" >
<?php

    $tabledef = array(
        array('Comments', "detector-slac-filters", '')) ;

    $tgen = new TableGenerator1 (
        $proposals ,
        $contacts ,
        $infos ,
        $params ,
        $tabledef
    ) ;
    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-detector-orientation" >
<?php

    $tabledef = array(
        array('Comments', "detector-slac-orient", '')) ;

    $tgen = new TableGenerator1 (
        $proposals ,
        $contacts ,
        $infos ,
        $params ,
        $tabledef
    ) ;
    print $tgen->toHtml() ;
?>
      </div>

    </div>
  </div>

  <div id="p-daq" >

    <div id="tabs" class="inner" >
      <ul>
        <li><a href="#p-daq-device"  >Devices</a></li>
        <li><a href="#p-daq-comment" >Comments</a></li>
      </ul>
      
      <div id="p-daq-device" >
<?php
    $devices = array() ;
    
    for ($i = 1; $i <= 20; $i++)
        array_push($devices ,
            array("#",    "data-dev-{$i}-qty",   '0') ,
            array("Name", "data-dev-{$i}-descr", '')) ;

    $tabledef = array(
        array('Device', $devices)) ;

    $tgen = new TableGenerator1 (
        $proposals ,
        $contacts ,
        $infos ,
        $params ,
        $tabledef
    ) ;
    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-daq-comment" >
<?php

    $tabledef = array(
        array('Comments', "data-comment", '')) ;

    $tgen = new TableGenerator1 (
        $proposals ,
        $contacts ,
        $infos ,
        $params ,
        $tabledef
    ) ;
    print $tgen->toHtml() ;
?>
        </div>
      </div>
  </div>

  <div id="p-data-online" >
<?php
    $tabledef = array(
        array("Shared memory Analysis?",        "data-online-shmem",          'No') ,
        array("Comments or other requirements", "data-online-shmem-comments", '')) ;

    $tgen = new TableGenerator1 (
        $proposals ,
        $contacts ,
        $infos ,
        $params ,
        $tabledef
    ) ;
    print $tgen->toHtml() ;
?>
  </div>

  <div id="p-data-offline" >
<?php
    $tabledef = array(
        array("Assistance is needed", "data-offline-ana-assist",   'No') ,
        array("Computing Resources",  "data-offline-ana-location", 'LCLS/SLAC') ,
        array("Comments",             "data-offline-ana-other",    '')) ;

    $tgen = new TableGenerator1 (
        $proposals ,
        $contacts ,
        $infos ,
        $params ,
        $tabledef
    ) ;
    print $tgen->toHtml() ;
?>
  </div>

  <div id="p-user" >

    <div id="tabs" class="inner" >
      <ul>
        <li><a href="#p-user-laser"     >Laser Equipment</a></li>
        <li><a href="#p-user-sampleenv" >Sample injector/environment</a></li>
        <li><a href="#p-user-detectors" >Detectors</a></li>
        <li><a href="#p-user-controls"  >Controls Equipment</a></li>
        <li><a href="#p-user-misc"      >Miscellaneous</a></li>
      </ul>
      
      <div id="p-user-laser" >
<?php
    $tabledef = array() ;
    
    for ($i = 1; $i <= 10; $i++)
        array_push($tabledef ,
            array("{$i}", "user-laser-{$i}", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals ,
        $contacts ,
        $infos ,
        $params ,
        $tabledef
    ) ;
    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-user-sampleenv" >
<?php
    $tabledef = array() ;
    
    for ($i = 1; $i <= 10; $i++)
        array_push($tabledef ,
            array("{$i}", "user-sampleenv-{$i}", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals ,
        $contacts ,
        $infos ,
        $params ,
        $tabledef
    ) ;
    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-user-detectors" >
<?php
    $tabledef = array() ;
    
    for ($i = 1; $i <= 10; $i++)
        array_push($tabledef ,
            array("{$i}", "user-detectors-{$i}", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals ,
        $contacts ,
        $infos ,
        $params ,
        $tabledef
    ) ;
    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-user-controls" >
<?php
    $tabledef = array() ;
    
    for ($i = 1; $i <= 10; $i++)
        array_push($tabledef ,
            array("{$i}", "user-controls-{$i}", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals ,
        $contacts ,
        $infos ,
        $params ,
        $tabledef
    ) ;
    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-user-misc" >
<?php
    $tabledef = array() ;
    
    for ($i = 1; $i <= 10; $i++)
        array_push($tabledef ,
            array("{$i}", "user-misc-{$i}", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals ,
        $contacts ,
        $infos ,
        $params ,
        $tabledef
    ) ;
    print $tgen->toHtml() ;
?>
      </div>

    </div>
  </div>

  <div id="p-pre" >
<?php
    $tabledef = array() ;
    
    array_push($tabledef ,
        array("Notes", "pre-notes", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals ,
        $contacts ,
        $infos ,
        $params ,
        $tabledef
    ) ;
    print $tgen->toHtml() ;
?>
  </div>

  <div id="p-post" >
<?php
    $tabledef = array() ;
    
    array_push($tabledef ,
        array("Notes", "post-notes", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals ,
        $contacts ,
        $infos ,
        $params ,
        $tabledef
    ) ;
    print $tgen->toHtml() ;
?>
  </div>

</div>

<?php
}) ;
?>

</body>
</html>