<?php
/**
 * Archivo de INCIDENCIAS
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Incidencias extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'GTP_INCIDENCIAS';
	protected $_primary = 'ID_INCIDENCIA';
	
	public function getRowsEmp($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT * 
				FROM $this->_name
				GROUP BY $this->_primary";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}                                    

    public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT  *
                FROM $this->_name
                WHERE $this->_primary = $idObject LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    }

    public function insertRow($data,$idEmpresa){
        $result     = Array();
        $result['status']  = false;
        
        $complement = ($data['inputCosto']==1) ? $data['inputCextra'] : 'NULL';
        
        $sql="INSERT INTO $this->_name
				SET  ID_EMPRESA 		=  ".$idEmpresa.",
					 DESCRIPCION 		= '".$data['inputDescripcion']."',
					 PRIORIDAD    		=  ".$data['inputPrioridad'].",
                     CORREO             =  ".$data['inputCorreo'];
        try{            
    		$query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				$result['id']	   = $query_id[0]['ID_LAST'];
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
            echo $sql;
        }
		return $result;	      	
    }
    
    public function updateRow($data,$idEmpresa,$idIncidencia){
        $result     = Array();
        $result['status']  = false;
        
        $complement = ($data['inputCosto']==1) ? $data['inputCextra'] : 'NULL';
                
        $sql="UPDATE  $this->_name
				SET  ID_EMPRESA         =  ".$idEmpresa.",
                     DESCRIPCION        = '".$data['inputDescripcion']."',
                     PRIORIDAD          =  ".$data['inputPrioridad'].",        
                     CORREO             =  ".$data['inputCorreo']."
               WHERE $this->_primary = $idIncidencia";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
            echo $sql;
        }
		return $result;	      	
    }    
}