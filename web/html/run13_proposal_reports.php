<?php

/*
 * 
 * AUTHORIZATION: LCLS crew
 */
require_once 'dataportal/dataportal.inc.php' ;

//class TableGenerator1_original {
//
//    private $proposals = null ;
//    private $contacts  = null ;
//    private $infos     = null ;
//    private $params    = null ;
//    private $colnames  = array(
//        'Proposal' ,
//        'Instr' ,
//        'POC'
//    ) ;
//    private $rowdefs = array() ;
//    private $rows = null ;
//
//    public function __construct ($proposals, $contacts, $infos, $params, $tabledef) {
//        $this->proposals = $proposals ;
//        $this->contacts  = $contacts ;
//        $this->infos     = $infos ;
//        $this->params    = $params ;
//        foreach ($tabledef as $def) {
//            array_push($this->colnames,      $def[0]) ;
//            array_push($this->rowdefs, array($def[1], $def[2])) ;
//        }
//    }
//    private function populateRows() {
//        if (is_null($this->rows)) {
//            $this->rows = array() ;
//            foreach ($this->proposals as $proposalNo) {
//                if (array_key_exists($proposalNo, $this->params)) {
//                    $this->addRow($proposalNo) ;
//                }
//            }
//        }
//    }
//    private function addRow($proposalNo) {
//        $row = array (
//            "<a class=\"link\" href=\"run13_proposal_questionnaire?proposal={$proposalNo}\" target=\"_blank\">{$proposalNo}</a>" ,
//            $this->infos[$proposalNo]->instrument() ,
//            substr($this->contacts[$proposalNo], 0, strpos($this->contacts[$proposalNo], '('))
//        ) ;
//        foreach ($this->rowdefs as $def) {
//            $id      = $def[0] ;
//            $default = $def[1] ;
//            array_push($row, $this->id2val($proposalNo, $id, $default)) ;
//        }
//        array_push($this->rows, $row) ;
//    }
//    private function id2val ($proposalNo, $id, $default='') {
//        $dict = $this->params[$proposalNo] ;
//        $val = array_key_exists($id, $dict) ? $dict[$id] : $default ;
//        return $val ;
//    }   
//    public function toHtml() {
//
//        $this->populateRows() ;
//
//        $str  = "<table>" ;
//        $str .= "<thead>" ;
//        $str .= "<tr>" ;
//        foreach ($this->colnames as $name) {
//            $str .= "<td>{$name}</td>" ;
//        }
//        $str .= "</tr>" ;
//        $str .= "</thead>" ;
//        $str .= "<tbody>" ;
//        foreach ($this->rows as $row) {
//            $str .= "<tr>" ;
//            foreach ($row as $val) {
//                $str .= "<td>{$val}</td>" ;
//            }
//            $str .= "</tr>" ;
//        }
//        $str .= "</tbody>" ;
//        $str .= "</table>" ;
//        return $str ;
//    }
//}

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
            "<a class=\"link\" href=\"run13_proposal_questionnaire?proposal={$proposalNo}\" target=\"_blank\">{$proposalNo}</a>" ,
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
    $contacts = array (
        "LK85" => 'Roberto Alonso-Mori (robertoa@slac.stanford.edu)' ,
        "LK86" => 'Galtier, Eric Christophe (egaltier@slac.stanford.edu)' ,
        "LK88" => 'Aquila, Andy (aquila@slac.stanford.edu)' ,
        "LK89" => 'Ray, Dipanwita (dray@slac.stanford.edu)' ,
        "LK96" => 'Boutet, Sebastien (sboutet@slac.stanford.edu)' ,
        "LK99" => 'Ray, Dipanwita (dray@slac.stanford.edu)' ,
        "LL02" => 'Hunter, Mark Steven (mhunter2@slac.stanford.edu)' ,
        "LL04" => 'Marcin Sikorski(sikorski@slac.stanford.edu)' ,
        "LL05" => 'Boutet, Sebastien (sboutet@slac.stanford.edu)' ,
        "LL09" => 'Osipov, Timur (tyosipov@slac.stanford.edu)' ,
        "LL13" => 'Roberto Alonso-Mori (robertoa@slac.stanford.edu)' ,
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
        "LL71" => 'Boutet, Sebastien (sboutet@slac.stanford.edu)' ,
        "LL72" => 'Dakovski, Georgi L. (dakovski@slac.stanford.edu)' ,
        "LL74" => 'Roberto Alonso-Mori (robertoa@slac.stanford.edu)' ,
        "LL78" => 'Galtier, Eric Christophe (egaltier@slac.stanford.edu)' ,
        "LL82" => 'Nagler, Bob (bnagler@slac.stanford.edu)' ,
        "LL84" => 'Boutet, Sebastien (sboutet@slac.stanford.edu)' ,
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
        "LM47" => 'Boutet, Sebastien (sboutet@slac.stanford.edu)' ,
        "LM48" => 'Sanghoon Song (sanghoon@slac.stanford.edu)' ,
        "LM51" => 'Boutet, Sebastien (sboutet@slac.stanford.edu)' ,
        "LM52" => 'Boutet, Sebastien (sboutet@slac.stanford.edu)'
    ) ;
    $infos  = array() ;
    $params = array() ;

    foreach ($proposals as $proposalNo) {

        $info = $SVC->safe_assign(
            $SVC->urawi()->proposalInfo($proposalNo) ,
            "No such proposal found: {$proposalNo}." ) ;

        $experimentName = $info->posix_group('2016-03-24') ;
        $exper = $SVC->safe_assign(
            $SVC->regdb()->find_experiment_by_unique_name($experimentName) ,
            "We're sorry - this proposal is not found in our system") ;

        if ($SVC->authdb()->hasRole($SVC->authdb()->authName(), $exper->id(), 'ExperimentInfo', 'Editor') ||
            $SVC->authdb()->hasRole($SVC->authdb()->authName(), $exper->id(), 'ExperimentInfo', 'Reader')) {
            $id2param = array() ;
            foreach ($exper->getProposalParams_Run13() as $param) {
                $id2param[$param['id']] = $param['val'] ;
            }
            $infos  [$proposalNo] = $info ;
            $params [$proposalNo] = $id2param ;
        }

        if (isset($debug) && count($params) >= $debug) break ;
    }

?>

<!doctype html>
<html>

<head>

<title>Run 13 Proposal Questionnaires: Reports</title>

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
    tabs.children('#p-xray') .children('#tabs').tabs() ;
    tabs.children('#p-laser').children('#tabs').tabs() ;
    tabs.children('#p-env')  .children('#tabs').tabs() ;
    tabs.children('#p-data') .children('#tabs').tabs() ;
}) ;

</script>

</head>
<body>

<div id="title" >Run 13 Proposal Questionnaires: Reports</div>

<div id="tabs" >

  <ul>
    <li><a href="#p-xray"  >X-rays</a></li>
    <li><a href="#p-laser" >Optical Laser</a></li>
    <li><a href="#p-env"   >Setup and Sample</a></li>
    <li><a href="#p-contr" >Controls</a></li>
    <li><a href="#p-data"  >DAQ & Analysis</a></li>
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
        <li><a href="#p-laser-userequip"    >User-supplied Laser Equipment</a></li>
        <li><a href="#p-laser-other"        >Other Laser Requirements</a></li>

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
        array('Multiple Simultaneous Beams', 'laser-adv-simbeams',        '') ,
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
        array("Time Tool Needed?",       "laser-timetool", '')) ;

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

      <div id="p-laser-userequip" >
<?php
    $tabledef = array() ;

    for ($i = 1; $i <= 10; $i++)
        array_push ($tabledef ,
            array("{$i}", "laser-userequip-{$i}", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-laser-other" >
<?php    
    $tabledef = array (
        array("Requirements", "laser-other", '')) ;

    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

    </div>
  </div>   

  <div id="p-env" >

    <div id="tabs" class="inner" >
      <ul>
        <li><a href="#p-env-tech"           >X-ray Techniques to be Used</a></li>
        <li><a href="#p-env-endstation"     >Specific Endstation</a></li>
        <li><a href="#p-env-samples"        >Samples</a></li>
        <li><a href="#p-env-sampleenv"      >Sample Environment</a></li>
        <li><a href="#p-env-temperature"    >Temperature Control Min</a></li>
        <li><a href="#p-env-samplehandling" >Sample Handling and Delivery</a></li>
        <li><a href="#p-env-spectro"        >X-ray Spectrometers</a></li>
        <li><a href="#p-env-diag"           >MEC Diagnostics</a></li>
        <li><a href="#p-env-userenv"        >User-Supplied Equipment</a></li>
        <li><a href="#p-env-lab"            >Laboratory Space Needed</a></li>
        <li><a href="#p-env-other"          >Other Setup and Sample Information</a></li>
      </ul>

      <div id="p-env-tech" >
<?php
    $tabledef = array() ;

    for ($i = 1; $i <= 3; $i++)
        array_push($tabledef ,
            array("{$i}", "env-tech-{$i}", '')) ;

    array_push($tabledef ,
        array('Other',                         "env-tech-other", '') ,
        array('Reasons for prioritization',    "env-tech-descr", '') ,
        array('Spatial Resol.',                "env-spatialres", '') ,
        array('Energy Resol.',                 "env-energyres",  '') ,
        array('Q-value', array(
            array('Min',                       "env-minqval",    '') ,
            array('Max',                       "env-maxqval",    ''))) ,

//        array('Q-value: Min',                  "env-minqval",    '') ,
//        array('Max',                           "env-maxqval",    '') ,
        array('Sample-Detector Distance: Min', "env-mindetdist", '') ,    
        array('Max',                           "env-maxdetdist", '')) ;

    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-env-endstation" >
<?php
    $tabledef = array() ;

    for ($i = 1; $i <= 3; $i++)
        array_push($tabledef ,
            array("{$i}", "env-endstation-{$i}", '')) ;

    array_push($tabledef ,
        array('Other',                      "env-endstation-other", '') ,
        array('Reasons for prioritization', "env-endstation-descr", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-env-samples" >
<?php
    $tabledef = array() ;

    for ($i = 1; $i <= 10; $i++)
        array_push($tabledef ,
            array("{$i}", "env-samples-{$i}", '')) ;

    array_push($tabledef ,
        array('Extra', "env-samples-descr", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-env-sampleenv" >
<?php
    $tabledef = array (
        array("Sample Environment", "env-sampleenv",       '') ,
        array('Other',              "env-sampleenv-other", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-env-temperature" >
<?php
    $tabledef = array (
        array("Min", "env-min-temperature", '') ,
        array('Max', "env-max-temperature", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-env-samplehandling" >
<?php
            
    $tabledef = array (
        array("Fixed Target",                   "env-fixtarget",               '') ,
        array("Scanning rate",                  "env-scanrate",                '') ,
        array("Scanning speed",                 "env-scanspeed",               '') ,
        array("Liquid Sample",    subcoldefs(3, "env-liquid-{$i}",             '')) ,
        array("Gas Jet",          subcoldefs(4, "env-gas-{$i}",                '')) ,
        array("Aerosol Injector",               "env-aerosol",                 '') ,
        array("Other Injector",   subcoldefs(5, "env-injector-other-{$i}",     '')) ,
        array("Reasons for prioritization",     "env-samplehandl-other-descr", '')) ;

    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-env-spectro" >
<?php
    $tabledef = array() ;

    for ($i = 1; $i <= 3; $i++)
        array_push($tabledef ,
            array("{$i}", "env-spectro-{$i}", '')) ;

    array_push($tabledef ,
        array('Other', "env-spectro-other", '') ,
        array('Reasons for prioritization', "env-spectro-descr", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-env-diag" >
<?php
    $tabledef = array() ;

    for ($i = 1; $i <= 5; $i++)
        array_push($tabledef ,
            array("{$i}", "env-diag-{$i}", '')) ;

    array_push($tabledef ,
        array('Other',                      "env-diag-other", '') ,
        array('Reasons for prioritization', "env-diag-descr", '')) ;
    
    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-env-userenv" >
<?php
    $tabledef = array() ;

    for ($i = 1; $i <= 10; $i++)
        array_push($tabledef ,
            array("{$i}", "env-userenv-{$i}", '')) ;

    $tgen = new TableGenerator1 (
        $proposals, $contacts, $infos, $params, $tabledef) ;

    print $tgen->toHtml() ;
?>
      </div>

      <div id="p-env-lab" >
<?php
    $tabledef = array() ;
    
    for ($i = 1; $i <= 5; $i++)
        array_push($tabledef ,
            array("{$i}", "env-lab-{$i}", '')) ;

    array_push($tabledef ,
        array('Other', "env-lab-other", '')) ;
    
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

      <div id="p-env-other" >
<?php    
    $tabledef = array(
        array("Information", "env-other", '')) ;

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

  <div id="p-contr" >
  </div>

  <div id="p-data" >

    <div id="tabs" class="inner" >
      <ul>
        <li><a href="#p-data-dev-cam"     >Cameras</a></li>
        <li><a href="#p-data-dev-digi"    >Digitizers</a></li>
        <li><a href="#p-data-dev-encod"   >Encoders</a></li>
        <li><a href="#p-data-dev-other"   >Other Device Needs</a></li>
        <li><a href="#p-data-ana-online"  >Online (real-time) Analysis</a></li>
        <li><a href="#p-data-ana-offline" >Offline Analysis</a></li>
      </ul>
      
      <div id="p-data-dev-cam" >
<?php
    $cameras = array() ;
    
    for ($i = 1; $i <= 9; $i++)
        array_push($cameras ,
            array("#",  "data-dev-cam-{$i}-qty",  '') ,
            array("Type", "data-dev-cam-{$i}-type", '')) ;

    $tabledef = array(
        array('Cameras', $cameras) ,
        array('Binning',             "data-dev-cam-binning",       'Standard') ,
        array('Binning description', "data-dev-cam-binning-descr", '')) ;

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

      <div id="p-data-dev-digi" >
<?php
    $digitizers = array() ;
    for ($i = 1; $i <= 4; $i++)
        array_push($digitizers ,
            array("#",  "data-dev-digi-{$i}-qty",  '') ,
            array("Type", "data-dev-digi-{$i}-type", '')) ;
 
    $tabledef = array(
        array("Digitizers", $digitizers) ,
        array("Channels", array(
            array("@ 2 GS/s", "data-dev-digi-chan-2gs-qty",  '') ,
            array("@ 4 GS/s", "data-dev-digi-chan-4gs-qty",  '') ,
            array("@ 8 GS/s",  "data-dev-digi-chan-8gs-qty", ''))) ,
        array("Comments",      "data-dev-digi-comments",     '')) ;

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

      <div id="p-data-dev-encod" >
<?php
    $encoders = array() ;

    for ($i = 1; $i <= 2; $i++)
        array_push($encoders ,
            array("#",  "data-dev-encod-{$i}-qty",  '') ,
            array("Type", "data-dev-encod-{$i}-type", '')) ;

    $tabledef = array(
        array("Encoders", $encoders) ,
        array("Comments", "data-dev-encod-comments", '')) ;

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

      <div id="p-data-dev-other" >
<?php
    $devices = array() ;

    for ($i = 1; $i <= 5; $i++)
        array_push($devices ,
            array("#",  "data-dev-other-{$i}-qty",   '') ,
            array("Type", "data-dev-other-{$i}-descr", '')) ;

    $tabledef = array(
        array("Devices", $devices) ,
        array("Comments", "data-dev-other-comments", '')) ;

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

      
      <div id="p-data-ana-online" >
<?php
    $tabledef = array(
        array("Number of Monitoring nodes", array(
            array("AMI",          "data-ana-ami",      '') ,
            array("psana-python", "data-ana-psana",    '') ,
            array("user code",    "data-ana-user",     ''))) ,
        array("Comments",         "data-ana-comments", '')) ;

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

      <div id="p-data-ana-offline" >
<?php
    $tabledef = array(
        array("Assistance is needed", "data-ana-assist",   '') ,
        array("Computing Resources",  "data-ana-location", 'LCLS/SLAC') ,
        array("Comments",             "data-ana-other",    '')) ;

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

</div>

<?php
}) ;
?>

</body>
</html>