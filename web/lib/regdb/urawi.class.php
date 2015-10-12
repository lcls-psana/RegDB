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
        if (is_null($this->fullName)) $this->fullName = "$this->info->firstName $this->info->lastName " ;
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
    public function approved   () { return $this->info->approved === 'yes' ; }
    public function instrument () { return $this->info->instrument ; }

    public function start () {
        if (is_null($this->startDate)) $this->startDate = LusiTime::parse($this->info->startDate) ;
        return $this->startDate ;
    }
    public function stop () {
        if (is_null($this->stopDate)) $this->stopDate = LusiTime::parse($this->info->stopDate) ;
        return $this->stopDate ;
    }
    public function contact () {
        if (is_null($this->spokesPerson)) $this->spokesPerson = new URAWIUser($this->info->spokesPerson) ;
        return $this->spokesPerson ;
    }
    public function contact_email () {
        if (is_null($this->spokesPersonEmailCombined)) $this->spokesPersonEmailCombined =  $this->info->spokesPerson->firstName." ".$this->info->spokesPerson->lastName." (".$this->info->spokesPerson->email.")" ;
        return $this->spokesPersonEmailCombined ;
    }
    public function members () {
        if (is_null($this->collaborators)) {
            $this->collaborators = array() ;
            foreach ($this->info->collaborators as $collaborator)
                array_push($this->collaborators, new URAWIUser($collaborator)) ;
        }
        return $this->collaborators ;
    }
    public function posix_group () {
        if (is_null($this->group)) $this->group = strtolower($this->info->instrument.substr($this->info->proposalNo, 1, 3).substr($this->info->startDate, 2, 2)) ;
        return $this->group ;
    }
    
    public function numShifts () {
        if (is_null($this->shiftsScheduled)) {
            $ival = new \LusiTime\LusiInterval($this->start(), $this->stop()) ;
            $hours = $this->shiftsScheduled = $ival->toHours() ;
            $this->shiftsScheduled = intval($hours / 24) + ($hours % 24 ? 1 : 0) ;
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

    /// Data members

    private $url_base = null ;

    /* Constructor
     *
     * Construct the top-level API object using the specified connection
     * parameters. Put null to envorce default values of parameters.
     */
    public function __construct ($url_base) {
        $this->url_base = $url_base ;
    }
    
    
    public function proposals ($startDate=null, $stopDate=null) {
        $parameters = '' ;
        if (!is_null($startDate)) {
            if ($startDate instanceof LusiTime)
                $parameters = ($parameters === '' ? '?' : '&') . $startDate->toStringDay() ;
            else
                throw new RegDBException(__METHOD__, "invalid type of parameter 'startDate'") ;
        }
        if (!is_null($stopDate)) {
            if ($stopDate instanceof LusiTime)
                $parameters = ($parameters === '' ? '?' : '&') . $stopDate->toStringDay() ;
            else
                throw new RegDBException(__METHOD__, "invalid type of parameter 'stopDate'") ;
        }

        $result = array() ;

        $proposals_json = $this->request("proposal_info{$parameters}", null) ;
        foreach($proposals_json->proposals as $p)
            array_push($result, $p->proposalNo) ;

        return $result ;
    }
    
    public function proposalInfo ($number) {
        if (!is_string($number)) throw new RegDBException(__METHOD__, "invalid type of parameter 'number'") ;
        $number = strtoupper($number) ;

        $url = "btsr_info?proposalNo={$number}" ;
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
        if ($data_json->status !== "success") {
            if (!is_null($on_error)) $on_error($data_json->message) ;
            else
                throw new RegDBException (
                    __METHOD__ ,
                    "Web service request {$request_url} failed with status: '{$data_json->status}' and message: '{$data_json->message}'") ;
        }
        return $data_json ;
    }
}