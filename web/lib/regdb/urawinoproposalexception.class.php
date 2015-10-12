<?php

namespace RegDB ;

require_once 'regdb.inc.php' ;

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Class RegDBException is a standard exception to be thrown by the API
 * to report wrongful conditions.
 *
 * The current implementation of the class doesn't have any extra functionality
 * on top of its base class. Therefore the sole role of the current class is
 * to provide an identification mechanism for recognizable non-standard
 * situations appearing within the API.
 *
 * @author gapon
 */
class URAWINoProposalException extends \Exception {


    /**
     * Constructor
     */
    public function __construct () {
        parent::__construct('No such proposal') ;
    }

    /**
     * HTML decorated string representation of the exception
     *
     * @return string
     */
    public function toHtml() {
        return "<b style='color:red'>".__CLASS__ . "</b> : <i>{$this->message}</i>\n";
    }
}
?>
