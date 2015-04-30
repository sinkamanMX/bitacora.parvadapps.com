<?php
/**
 * Archivo de definici—n de Unidades
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_LogPagos extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'LOG_PAGOS';
	protected $_primary = 'ID_LOG';

    public function insertLog($data,$idObject){
        $result     = false;
                
        $sql="INSERT INTO $this->_name 
        		SET LOG_TEXT = '".$data."',
        		    ID_VIAJE =  ".$idObject.",
        		FECHA_REGISTRO 	= CURRENT_TIMESTAMP";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result	  = true;
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	      	
    } 
}