<?php

namespace RegDB;

require_once( 'regdb.inc.php' );
require_once( 'authdb/authdb.inc.php' );
require_once( 'lusitime/lusitime.inc.php' );
require_once( 'dataportal/dataportal.inc.php' );

use AuthDB\AuthDB;
use LusiTime\LusiTime;

/**
 * Class RegDBExperiment an abstraction for experiments.
 *
 * @author gapon
 */
class RegDBExperiment {

    /* Data members
     */
    private $connection;
    private $registry;

    public $attr;

    /* Constructor
     */
    public function __construct ( $connection, $registry, $attr ) {
        $this->connection = $connection;
        $this->registry = $registry;
        $this->attr = $attr;
    }

    public function parent () {
        return $this->registry; }

    public function id () {
        return $this->attr['id']; }

    public function name () {
        return $this->attr['name']; }

    public function description () {
        return $this->attr['descr']; }

    public function instr_id () {
        return $this->attr['instr_id']; }

    public function instrument () {
        $result = $this->parent()->find_instrument_by_id( $this->instr_id());
        if( is_null( $result ))
            throw new RegDBException (
                __METHOD__,
                "no instrument found - database may be in the inconsistent state" );
        return $result;
    }

    public function registration_time () {
        return LusiTime::from64( $this->attr['registration_time'] ); }

    public function begin_time () {
        return LusiTime::from64( $this->attr['begin_time'] ); }

    public function end_time () {
        if( is_null( $this->attr['end_time'] )) return null;
        return LusiTime::from64( $this->attr['end_time'] ); }

    public function leader_account () {
        return $this->attr['leader_account']; }

    public function contact_info () {
        return $this->attr['contact_info']; }

    public function POSIX_gid () {
        return $this->attr['posix_gid']; }

    public function group_member_accounts () {
        $result = array();
        $members = $this->connection->posix_group_members( $this->POSIX_gid());
        foreach( $members as $member )
            array_push( $result, $member['uid'] );
        return $result;
    }

    public function group_members () {
        return $this->connection->posix_group_members( $this->POSIX_gid()); }

    public function in_interval ( $timestamp ) {
        return LusiTime::in_interval(
            $timestamp,
            $this->attr['begin_time'],
            $this->attr['end_time'] ); }

    public function is_facility () {
        return $this->instrument()->is_location();
    }

    /* =============
     *   MODIFIERS
     * =============
     */
    public function set_description ( $description ) {
        $result = $this->connection->query(
            "UPDATE {$this->connection->database}.experiment SET descr='".$this->connection->escape_string( $description ).
            "' WHERE id=".$this->id());
        $this->attr['descr'] = $description;
    }

    public function set_contact_info ( $contact_info ) {
        $result = $this->connection->query(
            "UPDATE {$this->connection->database}.experiment SET contact_info='".$this->connection->escape_string( $contact_info ).
            "' WHERE id=".$this->id());
        $this->attr['contact_info'] = $contact_info;
    }

    public function set_interval( $begin_time, $end_time ) {

        /* Verify parameters
         */
        if( !is_null( $end_time ) && !$begin_time->less( $end_time ))
            throw new RegDBException (
                __METHOD__,
                "begin time '".$begin_time."' isn't less than end time '".$end_time."'" );

        $begin_time_64 = $begin_time->to64();
        $end_time_64 = $end_time->to64();

        /* Proceed with the operation.
         */
        $this->connection->query (
            "UPDATE {$this->connection->database}.experiment SET begin_time=".$begin_time_64.
            ", end_time=".$end_time_64.
            " WHERE id=".$this->id());

        $this->attr['begin_time'] = $begin_time_64;
        $this->attr['end_time'] = $end_time_64;
    }

    /* ==============
     *   PARAMETERS
     * ==============
     */
    public function num_params ( $condition='' ) {

        $extra_condition = $condition == '' ? '' : ' AND '.$condition;
        $result = $this->connection->query(
            "SELECT COUNT(*) FROM {$this->connection->database}.experiment_param WHERE exper_id=".$this->id().$extra_condition );

        if( $nrows == 1 )
            return mysql_result( $result, 0 );

        throw new RegDBException (
            __METHOD__,
            "unexpected size of result set returned by the query" );
    }

    public function param_names ( $condition='' ) {

        $list = array();

        $extra_condition = $condition == '' ? '' : ' AND '.$condition;
        $result = $this->connection->query(
            "SELECT param FROM {$this->connection->database}.experiment_param WHERE exper_id=".$this->id().$extra_condition );

        $nrows = mysql_numrows( $result );
        for( $i = 0; $i < $nrows; $i++ )
            array_push(
                $list,
                mysql_result( $result, $i ));

        return $list;
    }

    public function params ( $condition='' ) {

        $list = array();

        $extra_condition = $condition == '' ? '' : ' AND '.$condition;
        $result = $this->connection->query(
            "SELECT * FROM {$this->connection->database}.experiment_param WHERE exper_id=".$this->id().$extra_condition );

        $nrows = mysql_numrows( $result );
        for( $i = 0; $i < $nrows; $i++ )
            array_push (
                $list,
                new RegDBExperimentParam (
                    $this->connection,
                    $this,
                    mysql_fetch_array( $result, MYSQL_ASSOC )));

        return $list;
    }

    public function find_param_by_name ( $name ) {
        return $this->find_param_by_( "param='".$this->connection->escape_string( trim( $name ))."'" ); }

    private function find_param_by_ ( $condition ) {

        $extra_condition = $condition == '' ? '' : ' AND '.$condition;
        $result = $this->connection->query(
            "SELECT * FROM {$this->connection->database}.experiment_param WHERE exper_id=".$this->id().$extra_condition );

        $nrows = mysql_numrows( $result );
        if( $nrows == 0 ) return null;
        if( $nrows == 1 )
            return new RegDBExperimentParam (
                $this->connection,
                $this,
                mysql_fetch_array( $result, MYSQL_ASSOC ));

        throw new RegDBException(
            __METHOD__,
            "unexpected size of result set returned by the query" );
    }

    public function add_param ( $name, $value, $description ) {

        /* erify values of parameters
         */
        if( is_null( $name ) || is_null( $value ) || is_null( $description ))
            throw new RegDBException (
                __METHOD__, "method parameters can't be null objects" );

        $trimmed_name = trim( $name );
        if( strlen( $trimmed_name ) == 0 )
            throw new RegDBException(
                __METHOD__, "parameter name can't be empty" );

        /* Proceed with the operation.
         */
        $this->connection->query (
            "INSERT INTO {$this->connection->database}.experiment_param VALUES(".$this->id().
            ",'".$this->connection->escape_string( $trimmed_name ).
            "','".$this->connection->escape_string( $value ).
            "','".$this->connection->escape_string( $description )."')" );

        $new_param = $this->find_param_by_( "param='".$this->connection->escape_string( $trimmed_name )."'" );
        if( is_null( $new_param ))
            throw new RegDBException (
                __METHOD__,
                "internal implementation errort" );

        return $new_param;
    }

    public function set_param ( $name, $value ) {

        /* Verify values of parameters
    	 */
        if( is_null( $name ) || is_null( $value ))
            throw new RegDBException (
                __METHOD__, "method parameters can't be null objects" );

        /* Make sure the parameter already exists. If it doesn't then create it with
         * some default description.
         */
        $param = $this->find_param_by_name( $name );
        if( is_null( $param )) return $this->add_param( $name, $value, "" );

        /* Otherwise proceed and set its new value.
         */
        $trimmed_name = trim( $name );
        if( strlen( $trimmed_name ) == 0 )
            throw new RegDBException(
                __METHOD__, "parameter name can't be empty" );

        $trimmed_value = trim( $value );
               
        /* Proceed with the operation.
         */
        $this->connection->query (
            "UPDATE {$this->connection->database}.experiment_param SET val='".$this->connection->escape_string( $trimmed_value )."'".
            " WHERE exper_id=".$this->id().
            " AND param='".$this->connection->escape_string( $trimmed_name )."'" );

        return $this->find_param_by_name( $name );
    }
    
    public function remove_param ( $name ) {

        /* Verify values of parameters
         */
        if( is_null( $name ))
            throw new RegDBException (
                __METHOD__, "method parameters can't be null objects" );

        $trimmed_name = trim( $name );
        if( strlen( $trimmed_name ) == 0 )
            throw new RegDBException(
                __METHOD__, "parameter name can't be empty" );

        /* Make sure the parameter is known.
         */
        $param = $this->find_param_by_name ( $name );
        if( is_null( $param ))
            throw new RegDBException(
                __METHOD__, "parameter doesn't exist in the database" );

        /* Proceed with the operation.
         */
        $this->connection->query (
            "DELETE FROM {$this->connection->database}.experiment_param WHERE param='{$trimmed_name}' AND exper_id={$this->id()}" );
    }

    public function remove_all_params () {

        /* Proceed with the operation.
         */
        $this->connection->query (
            "DELETE FROM {$this->connection->database}.experiment_param WHERE exper_id={$this->id()}" );
    }

    /* ===============
     *   RUN NUMBERS
     * ===============
     */
    public function runs () {

        $list = array();
        $table = "{$this->connection->database}.run_".$this->id();

        $result = $this->connection->query(
            "SELECT * FROM {$table} ORDER BY num" );

        $nrows = mysql_numrows( $result );
        for( $i = 0; $i < $nrows; $i++ )
            array_push (
                $list,
                new RegDBRun (
                    $this->connection,
                    $this,
                    mysql_fetch_array( $result, MYSQL_ASSOC )));

        return $list;
    }

    public function generate_run () {

        $table = "{$this->connection->database}.run_".$this->id();
        $request_time = LusiTime::now()->to64();

        $this->connection->query (
            "INSERT INTO {$table} VALUES(NULL,{$request_time})" );

        $result = $this->connection->query(
            "SELECT * FROM {$table} WHERE num=(SELECT LAST_INSERT_ID())" );

        $nrows = mysql_numrows( $result );
        if( $nrows == 0 ) return null;
        if( $nrows == 1 )
            return new RegDBRun (
                $this->connection,
                $this,
                mysql_fetch_array( $result, MYSQL_ASSOC ));

        throw new RegDBException(
            __METHOD__,
            "unexpected size of result set returned by the query" );
    }

    public function last_run () {

        $list = array();
        $table = "{$this->connection->database}.run_".$this->id();

        $result = $this->connection->query(
            "SELECT * FROM {$table} WHERE num=(SELECT MAX(num) FROM {$table})" );

        $nrows = mysql_numrows( $result );
        if( $nrows == 0 ) return null;
        if( $nrows == 1 )
            return new RegDBRun (
                $this->connection,
                $this,
                mysql_fetch_array( $result, MYSQL_ASSOC ));

        throw new RegDBException(
            __METHOD__,
            "unexpected size of result set returned by the query" );

    }

   /**
     * Get a list of files reported by the DAQ system as "open". If the optional
     * run number is present (not null) then restrict the search to a specific
     * run only.
     *
     * @param $run - optional run number
     * @return array
     */
    public function files( $run=null, $reverse_order=false, $order_by_time=false ) {

    	$list = array();
        $table = "{$this->connection->database}.file";

        $run_selector = is_null( $run ) ? '' : 'AND run='.$run;

        $order = $reverse_order ? 'DESC' : '';
        $order_by_opt = $order_by_time ?
            "open {$order}" :
            "run {$order}, stream {$order}, chunk {$order}";

        $sql = "SELECT * FROM {$table} WHERE exper_id=".$this->id()." {$run_selector} ORDER BY {$order_by_opt}";
        $result = $this->connection->query( $sql );
        $nrows = mysql_numrows( $result );
        for( $i = 0; $i < $nrows; $i++ )
            array_push (
                $list,
                new RegDBFile (
                    $this->connection,
                    $this,
                    mysql_fetch_array( $result, MYSQL_ASSOC )));

        return $list;
    }
 
    /**
     * Return an iterator of files reported by the DAQ system as "open".
     * 
     * @param integer $min_run
     * @param integer $max_run
     * @param boelean $reverse_order
     * @param boelean $order_by_time
     * @return \RegDB\RegDBFileItr
     */
    public function files_itr (
        $min_run=null ,
        $max_run=null ,
        $reverse_order=true ,
        $order_by_time=true) {

        $table = "{$this->connection->database}.file" ;

        $run_selector = '' ;
        if ($min_run && $max_run && ($min_run == $max_run)) {
            $run_selector .= " AND run ={$min_run}" ;
        } else {
            if ($min_run) $run_selector .= " AND run >={$min_run}" ;
            if ($max_run) $run_selector .= " AND run <={$max_run}" ;
        }
        $order = $reverse_order ? 'DESC' : '' ;
        $order_by_opt = $order_by_time ?
            "open {$order}" :
            "run {$order}, stream {$order}, chunk {$order}" ;

        $sql = "SELECT * FROM {$table} WHERE exper_id={$this->id()} {$run_selector} ORDER BY {$order_by_opt}" ;

        return new RegDBFileItr($this->instrument()->regdb(), $this->connection, $sql) ;
    }
    
   /**
     * Get a list of files registered in the data migration table.
     *
     * @return array
     */
    public function data_migration_files() {

    	$list   = array();
        $table  = "{$this->connection->database}.data_migration";
        $result = $this->connection->query(
            "SELECT * FROM {$table} WHERE exper_id=".$this->id()." ORDER BY file_type, file" );

        $nrows = mysql_numrows( $result );
        for( $i = 0; $i < $nrows; $i++ )
            array_push (
                $list,
                new RegDBDataMigrationFile (
                    $this->connection,
                    $this,
                    mysql_fetch_array( $result, MYSQL_ASSOC )));

        return $list;
    }

   /**
     * Get a list of files found in the ANA data migration table.
     *
     * @return array
     */
    public function data_migration2ana_files() {

    	$list   = array();
        $table  = "{$this->connection->database}.data_migration_ana";
        $result = $this->connection->query(
            "SELECT * FROM {$table} WHERE exper_id=".$this->id()." ORDER BY file, file_type" );

        $nrows = mysql_numrows( $result );
        for( $i = 0; $i < $nrows; $i++ )
            array_push (
                $list,
                new RegDBDataMigrationFile (
                    $this->connection,
                    $this,
                    mysql_fetch_array( $result, MYSQL_ASSOC )));

        return $list;
    }

   /**
     * Get a list of files found in the NERSC data migration table.
     *
     * @return array
     */
    public function data_migration2nersc_files() {

    	$list   = array();
        $table  = "{$this->connection->database}.data_migration_nersc";
        $result = $this->connection->query(
            "SELECT * FROM {$table} WHERE exper_id=".$this->id()." ORDER BY file, file_type" );

        $nrows = mysql_numrows( $result );
        for( $i = 0; $i < $nrows; $i++ )
            array_push (
                $list,
                new RegDBDataMigration2NERSCFile (
                    $this->connection,
                    $this,
                    mysql_fetch_array( $result, MYSQL_ASSOC )));

        return $list;
    }

    /**
     * Return the operator's account for the experiment
     *
     * The ruls for determining the account:
     *
     * 1. for regular instruments is '<low_case_instr_TLA>opr'
     * 2. for the facility e-Logs of regular instruments is the same as above
     *    (however it's pulled from the database of teh experiment's parameters)
     * 3. it's undefined for others
     *
     * @return String
     */
    public function operator_uid () {
        $uid = null ;
        if ($this->instrument()->is_standard()) {
            $uid = strtolower($this->instrument()->name()).'opr' ;
        } else if ($this->instrument()->is_location()) {
            $operator_uid_param = $this->find_param_by_name('operator_uid') ;
            if ($operator_uid_param) $uid = strtolower($operator_uid_param->value()) ;
        }
        return $uid ;
    }
    public function find_parameters_v () {
        $parameters = array (
            'values' => array ()
        ) ;

        // Find the latest set of parameters (if any)
        $result = $this->connection->query("SELECT * FROM {$this->connection->database}.experiment_paramv WHERE exper_id={$this->id()} ORDER BY modified_time DESC LIMIT 1") ;
        $nrows = mysql_numrows($result) ;
        if ($nrows) {
            if ($nrows != 1)
                throw new ShiftMgrException (
                        __class__.'::'.__METHOD__ ,
                        "internal error when looking for extended parameters of experiment ID {$this->id()}") ;
                        
            $attr = mysql_fetch_array($result, MYSQL_ASSOC) ;
            $id   = intval(trim($attr['id'])) ;

            $parameters['modified_uid']  = trim($attr['modified_uid']) ;
            $parameters['modified_time'] = LusiTime::from64(trim($attr['modified_time']))->toStringShort() ;
            $parameters['id'] = $id ;
            
            // Fetch values from that set
            $result = $this->connection->query("SELECT * FROM {$this->connection->database}.experiment_paramv_value WHERE param_id={$id}") ;
            for ($i = 0, $nrows = mysql_numrows($result); $i < $nrows; $i++) {
                $attr  = mysql_fetch_array($result, MYSQL_ASSOC) ;
                $key   = trim($attr['key']) ;
                $value = trim($attr['value']) ;
                $parameters['values'][$key] = $value ;
            }
        }
        
        // Mix in parameters from the experiment definition
        
        $parameters['values']['general-spokesperson'] = \DataPortal\DataPortal::decorated_experiment_contact_info($this) ;
        $parameters['values']['general-title']        = $this->description() ;
        
        $parameters['attachments'] = $this->find_attachments_v() ;

        return $parameters ;
    }
    
    public function save_parameters_v ($values, $attachments) {
        
        $modified_time = LusiTime::now()->to64() ;
        $modified_uid = $this->connection->escape_string(AuthDB::instance()->authName()) ;
        
        // First, make an empty parameter set
        $this->connection->query("INSERT INTO {$this->connection->database}.experiment_paramv VALUES(NULL,{$this->id()},'{$modified_uid}',{$modified_time})") ;
        
        // Get the parameter set's identifier which we're going to use to log
        // parameter set's values in a different table.
        $parameters = $this->find_parameters_v () ;
        $id = $parameters['id'] ;

        // Log values for the set
        foreach ($values as $key => $value) {

            $key   = trim($key) ;
            $value = trim($value) ;

            // Special processing for parameters from the experiment definition
            switch ($key) {
                case 'general-spokesperson' :
                    // ATTENTION: This parameter can't be set in this way
                    continue ;
                case 'general-title':
                    $this->set_description($value) ;
                    continue ;
            }

            $key   = $this->connection->escape_string($key) ;
            $value = $this->connection->escape_string("{$value}") ;

            $this->connection->query("INSERT INTO {$this->connection->database}.experiment_paramv_value VALUES({$id},'{$key}','{$value}')") ;
        }
        
        foreach ($attachments as $id => $value) {
            if ($value->delete) {
                $this->delete_attachment_v_by_id($id) ;
                continue ;
            }
            $description = $this->connection->escape_string($value->description) ;
            $this->connection->query (
                "UPDATE {$this->connection->database}.experiment_paramv_attachment SET description='{$description}' WHERE id={$id}"
            ) ;
        }

        // Return the complete set
        return $this->find_parameters_v () ;
    }
    public function find_attachments_v ($include_data=false) {

        $result = $this->connection->query (
            "SELECT id FROM {$this->connection->database}.experiment_paramv_attachment WHERE exper_id={$this->id()}") ;

        $attachments = array() ;
        for ($i = 0, $nrows = mysql_numrows($result); $i < $nrows; $i++) {
            $attr = mysql_fetch_array($result, MYSQL_ASSOC) ;
            array_push (
                $attachments ,
                $this->find_attachment_v_by_id($attr['id'], $include_data)) ;
        }
        return $attachments ;
    }
    public function find_attachment_v_by_id ($id, $include_data=false) {
        return $this->registry->find_attachment_v_by_id ($id, $include_data) ;
    }

    public function save_attachment_v ($name, $data, $data_type) {

        $description = $name ;

        $this->connection->query (
            "INSERT INTO {$this->connection->database}.experiment_paramv_attachment VALUES(NULL,".$this->id() .
            ",'" .$this->connection->escape_string($name) .
            "','".$this->connection->escape_string($data) .
            "','".$this->connection->escape_string($data_type).
            "','".$this->connection->escape_string($description)."')") ;

        return $this->find_attachment_v_by_id('(SELECT LAST_INSERT_ID())') ;        
    }
    public function delete_attachment_v_by_id ($id) {
        $result = $this->connection->query (
            "DELETE FROM {$this->connection->database}.experiment_paramv_attachment WHERE id={$id}") ;
    }
}
?>
