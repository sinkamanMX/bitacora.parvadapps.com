<?php
/**
 * Archivo de definici—n de Unidades
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Transportistas extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'GTP_TRANSPORTISTA';
	protected $_primary = 'ID_TRANSPORTISTA';
	
	public function getRowsEmp($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT * 
				FROM $this->_name
				WHERE ID_EMPRESA = $idObject
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
        
        $sql="INSERT INTO $this->_name
				SET  ID_EMPRESA 		=  ".$idEmpresa.",
					 DESCRIPCION 		= '".$data['inputDescripcion']."',
					 ACTIVO				=  ".$data['inputStatus'].",
                     RAZON_SOCIAL       = '".$data['inputRazonSocial']."',
                     DIRECCION          = '".$data['inputDireccion']."',
                     CONTACTO           = '".$data['inputContacto']."',
                     TEL_MOVIL          = '".$data['inputTelMovil']."',
                     TEL_FIJO           = '".$data['inputTelFijo']."',
                     RADIO              = '".$data['inputRadio']."',					 					 
					 CREADO 			= CURRENT_TIMESTAMP";
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
    
    public function updateRow($data,$idEmpresa,$idTransportista){
        $result     = Array();
        $result['status']  = false;

        $sql="UPDATE  $this->_name
				SET  ID_EMPRESA 		=  ".$idEmpresa.",
					 DESCRIPCION 		= '".$data['inputDescripcion']."',
					 ACTIVO				=  ".$data['inputStatus'].",
                     RAZON_SOCIAL       = '".$data['inputRazonSocial']."',
                     DIRECCION          = '".$data['inputDireccion']."',
                     CONTACTO           = '".$data['inputContacto']."',
                     TEL_MOVIL          = '".$data['inputTelMovil']."',
                     TEL_FIJO           = '".$data['inputTelFijo']."',
                     RADIO              = '".$data['inputRadio']."',
					 CREADO 			= CURRENT_TIMESTAMP
					 WHERE $this->_primary = $idTransportista";
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

    public function deleteRow($data){
        $result     = Array();
        $result['status']  = false;

        $sql="DELETE FROM  $this->_name
					 WHERE $this->_primary = ".$data['catId']."
					  AND  ID_EMPRESA	   = ".$data['idEmpresa']."  LIMIT 1";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	     	
    }   

    public function getFirst($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT *
    			FROM $this->_name
                WHERE ID_EMPRESA = $idObject 
               ORDER BY CREADO DESC LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	    	
    } 
}