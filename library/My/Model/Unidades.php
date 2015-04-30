<?php
/**
 * Archivo de definici—n de Unidades
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Unidades extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'GTP_UNIDADES';
	protected $_primary = 'ID_UNIDAD';
	
	public function getUnidades($idObject){
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

    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
        
        $sql="INSERT INTO $this->_name
				SET  ID_TRANSPORTISTA 	= ".$data['inputTransportista'].",
					 ID_PROVEEDOR       = ".$data['inputProveedor'].",
					 ECONOMICO 			= '".$data['inputEco']."',
					 PLACAS				= '".$data['inputPlacas']."',
					 IDENTIFICADOR		= '".$data['inputIden']."',
					 ACTIVO				=  ".$data['inputStatus'].",
					 ID_EMPRESA			=  ".$data['idEmpresa'].",				 
					 REGISTRO 			= CURRENT_TIMESTAMP";
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
        }
		return $result;	      	
    }
    
    public function updateRow($data){
        $result     = Array();
        $result['status']  = false;

        $sql="UPDATE  $this->_name
				SET  ID_TRANSPORTISTA 	= ".$data['inputTransportista'].",
					 ID_PROVEEDOR       = ".$data['inputProveedor'].",    
					 ECONOMICO 			= '".$data['inputEco']."',
					 PLACAS				= '".$data['inputPlacas']."',
					 IDENTIFICADOR		= '".$data['inputIden']."',
					 ACTIVO				=  ".$data['inputStatus']."
					 WHERE $this->_primary =".$data['catId']." LIMIT 1";
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
    
	public function getCbo($idObject,$idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_UNIDAD AS ID, CONCAT(IDENTIFICADOR,'-',PLACAS) AS NAME
				FROM $this->_name
				WHERE ID_TRANSPORTISTA = $idObject
				  AND ID_EMPRESA  	   = $idEmpresa  
				ORDER BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}    
	
	public function getDataComplete($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT U.*, P.DESCRIPCION AS NAME_PROVEEDOR, P.USUARIO, P.PASSWORD, P.URL
				FROM $this->_name U
				INNER JOIN PROVEEDORES P ON U.ID_PROVEEDOR = P.ID_PROVEEDOR				
                WHERE U.$this->_primary = $idObject LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;			
	}   	
    
    public function validateUnitByPlaque($sPlaque){
		$result= false;
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT  *
                FROM $this->_name
                WHERE PLACAS = '$sPlaque' LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = true;			
		}
        
		return $result;	    	
    }	
}