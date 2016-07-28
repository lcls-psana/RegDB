<?php

namespace RegDB ;

require_once 'regdb.inc.php' ;
require_once 'lusitime/lusitime.inc.php' ;

use LusiTime\LusiTime ;

class URAWIUser {

    /// Data members

    private $info = null ;
    
    private $fullName = null ;

    private $hasAccount = true ;
    private $account = null ;

    /* Constructor
     *
     * Construct the API object. Specific properties would be extracted from
     * the stdClass object passed as a parameter of the c-tor
     */
    public function __construct ($info) {
        $this->info = $info ;
    }
    public function name () {
        if (is_null($this->fullName)) $this->fullName = "{$this->info->firstName} {$this->info->lastName} " ;
        return $this->fullName ;
    }

    public function sid  () { return $this->info->sid ; }
    public function email() { return $this->info->email ; }

    public function uid () {
        if (is_null($this->account)) {
            if (isset($this->info->account)) {
                foreach ($this->info->account as $account) {
                    if ($account->unixGroup !== 'xs') {
                        $this->account = $account->unixName ;
                        break ;
                    }
                }
            }
            // Well, at least we've tried it once
            if (is_null($this->account)) $this->hasAccount = false ;
        }
        return $this->account ;
    }
    
    public function personId () {
        return intval($this->info->personId) ;
    }
}

class URAWIProposal {

    /// Data members

    private $info = null ;
    
    private $startDate = null ;
    private $stopDate  = null ;
    private $spokesPerson = null ;
    private $spokesPersonEmailCombined = null ;
    private $collaborators = null ;
    private $group = null ;
    private $shiftsScheduled = null ;

    /* Constructor
     *
     * Construct the API object. Specific properties would be extracted from
     * the stdClass object passed as a parameter of the c-tor
     */
    public function __construct ($info) {
        $this->info = $info ;
    }
    
    public function number     () { return $this->info->proposalNo ; }
    public function status     () { return $this->info->status ; }
    public function title      () { return $this->info->proposalTitle ; }
    public function proposalAbstract () { return $this->info->proposalAbstract ; }
    public function approved   () { return $this->info->approved === 'yes' ; }
    public function instrument () { return $this->info->instrument ; }

    public function start () {
        if (is_null($this->startDate)) {
            if (isset($this->info->startDate)) {
                $this->startDate = LusiTime::parse($this->info->startDate) ;
            } else {
                $this->startDate = LusiTime::parse('2016-03-24') ;
            }
        }
        return $this->startDate ;
    }
    public function stop () {
        if (is_null($this->stopDate)) {
            if (isset($this->info->stopDate)) {
                $this->stopDate = LusiTime::parse($this->info->stopDate) ;
            } else {
                $this->stopDate = LusiTime::parse('2016-03-25') ;
            }
        }
        return $this->stopDate ;
    }
    public function contact () {
        if (is_null($this->spokesPerson)) {
            if (isset($this->info->spokesPerson)) {
                $this->spokesPerson = new URAWIUser($this->info->spokesPerson) ;
            }
        }
        return $this->spokesPerson ;
    }
    public function contact_email () {
        if (is_null($this->spokesPersonEmailCombined)) $this->spokesPersonEmailCombined =  $this->info->spokesPerson->firstName." ".$this->info->spokesPerson->lastName." (".$this->info->spokesPerson->email.")" ;
        return $this->spokesPersonEmailCombined ;
    }
    public function members () {
        if (is_null($this->collaborators)) {
            $this->collaborators = array() ;
            if (isset($this->info->collaborators)) {
                foreach ($this->info->collaborators as $collaborator)
                    array_push($this->collaborators, new URAWIUser($collaborator)) ;
            }
        }
        return $this->collaborators ;
    }
    public function posix_group ($startDate=null) {
        if (is_null($this->group)) {

            // ATTENTION: making a temporary correction for this pseudo-instrument name
            //            which is just aanother camera (SSC) at the parasytic (second)
            //            station of CXI.
            $instr_name = $this->info->instrument == 'SSC' ? 'CXI' : $this->info->instrument ;

            $this->group = strtolower($instr_name.substr($this->info->proposalNo, 1, 3)) ;
            $startDate = $this->info->startDate ? $this->info->startDate : $startDate ;
            if ($startDate) {
                $this->group .= substr($startDate, 2, 2) ;
            } else {
                $this->group .= '??' ;
            }
        }
        return $this->group ;
    }
    
    public function numShifts () {
        if (is_null($this->shiftsScheduled)) {
            if ($this->start() && $this->stop()) {
                $ival = new \LusiTime\LusiInterval($this->start(), $this->stop()) ;
                $hours = $this->shiftsScheduled = $ival->toHours() ;
                $this->shiftsScheduled = intval($hours / 24) + ($hours % 24 ? 1 : 0) ;
            }
            // Make sure at least 1 shift is reported
            if (!$this->shiftsScheduled) $this->shiftsScheduled = 1 ;
        }
        return $this->shiftsScheduled ;
    }
}

class URAWI {

    private static $instance = null ;

    /**
     * Return an instance of the object initialzied with default version
     * of parameters.
     */
    public static function instance () {
        if( is_null(URAWI::$instance))
            URAWI::$instance = new URAWI(REGDB_DEFAULT_URAWI_URL_BASE) ;
        return URAWI::$instance ;
    }

    // Data members

    private $url_base = null ;

    /* Constructor
     *
     * Construct the top-level API object using the specified connection
     * parameters. Put null to envorce default values of parameters.
     */
    public function __construct ($url_base) {
        $this->url_base = $url_base ;
    }

    public function authenticate ($username, $password) {

        $username_encoded = urlencode($username) ;
        $password_encoded = urlencode($password) ;

        $url = "urawi_auth.php?username={$username_encoded}&password={$password_encoded}" ;
        $result_json = $this->request($url, null) ;
        if ($result_json->status === 'success') {
            return intval($result_json->personId) ;
        }
        return null ;
    }

    /**
     * Return a sorted (by the start date) array of proposal numbers for
     * the specified (if any) period of time.
     *
     * @param LusiTime $startDate
     * @param LusiTime $stopDate
     * @return array
     * @throws RegDBException
     */
    public function proposals ($startDate=null, $stopDate=null) {

        $parameters = '' ;
        if (!is_null($startDate)) {
            if ($startDate instanceof LusiTime)
                $parameters = ($parameters === '' ? '?' : '&') . "startDate={$startDate->toStringDay()}" ;
            else
                throw new RegDBException(__METHOD__, "invalid type of parameter 'startDate'") ;
        }
        if (!is_null($stopDate)) {
            if ($stopDate instanceof LusiTime)
                $parameters = ($parameters === '' ? '?' : '&') . "stopDate={$stopDate->toStringDay()}" ;
            else
                throw new RegDBException(__METHOD__, "invalid type of parameter 'stopDate'") ;
        }

        $result = array() ;
        $url = "proposal_info{$parameters}" ;
        $proposals_json = $this->request($url, null) ;
        foreach ($proposals_json->proposals as $p) array_push($result, $p) ;

        // sort by the start date
        usort($result, function ($a, $b) {
            return strcmp($a->startDate, $b->startDate) ;
        }) ;
        $proposalNumbers = array() ;
        foreach ($result as $p) array_push($proposalNumbers, $p->proposalNo) ;

        return $proposalNumbers ;
    }
    
    public function proposalInfo ($number, $fetchProposalInfoMethod='btsr_info') {

        if (!is_string($number)) throw new RegDBException(__METHOD__, "invalid type of parameter 'number'") ;
        $number = strtoupper($number) ;

        $url = "{$fetchProposalInfoMethod}?proposalNo={$number}" ;
        $data_json = $this->request (
            $url ,
            function ($message) {
                throw $message === 'No such proposalNo' ?
                    new URAWINoProposalException() :
                    new RegDBException('URAWI::proposalInfo()', "Web service request failed because of: '{$message}'") ;
            }) ;
        return new URAWIProposal($data_json) ;
    }

    private function request ($path, $on_error) {

        // create a new cURL resource
        $ch = curl_init() ;

        // set URL and other appropriate options

        $request_url = "{$this->url_base}/{$path}" ;

        curl_setopt($ch, CURLOPT_URL,            $request_url) ;
        curl_setopt($ch, CURLOPT_HEADER,         0) ;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;  // to return result as a data object

        // grab URL and pass it to the browser
        $data = curl_exec($ch) ;

        // close cURL resource, and free up system resources
        curl_close($ch) ;

        $data_json = json_decode($data) ;
        if (!$data_json) {
            throw new RegDBException (
                    __METHOD__ ,
                    "Web service request {$request_url} failed with JSON data: ".$data_json) ;
        }
        if (($data_json->status !== "success") && ($data_json->status !== "ok")) {
            if (!is_null($on_error)) $on_error($data_json->message) ;
            else
                throw new RegDBException (
                    __METHOD__ ,
                    "Web service request {$request_url} failed with status: '{$data_json->status}' and message: '{$data_json->message}'") ;
        }
        return $data_json ;
    }
}