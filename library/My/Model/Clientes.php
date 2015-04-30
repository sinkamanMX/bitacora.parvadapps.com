<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Clientes extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'GTP_CLIENTES';
	protected $_primary = 'ID_CLIENTE';
	
	public function getRowsEmp($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
	    $sql = "SELECT C.NOMBRE AS EMPRESA, 
	                   B.DESCRIPCION AS SUCURSAL, 
	                   A.*
				FROM $this->_name A,
				     SUCURSALES B,
				     EMPRESAS C
				WHERE A.ID_SUCURSAL = B.ID_SUCURSAL AND
				      B.ID_EMPRESA = C.ID_EMPRESA AND
				      C.ID_EMPRESA = $idObject ";
		$query   = $this->query($sql);
		//echo $sql;
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}                                    

    public function getData($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql =" SELECT  *
                FROM $this->_name
                WHERE $this->_primary = $idObject LIMIT 1";	
                echo $sql;
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
				SET  ID_SUCURSAL		=  ".$data['inputSucursal'].",
					 NOMBRE 		    = '".$data['inputNombre']."',				 					 
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
    
    public function updateRow($data,$idCliente){
        $result     = Array();
        $result['status']  = false;

        $sql="UPDATE  $this->_name
				SET  ID_SUCURSAL 		= ".$data['inputSucursal'].",
					 NOMBRE				=  '".$data['inputNombre']."',
					 CREADO 			= CURRENT_TIMESTAMP
					 WHERE $this->_primary = $idCliente";
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

	public function getCbo($idObject,$idEmpresa=0){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_CLIENTE AS ID, NOMBRE AS NAME
				FROM $this->_name
				WHERE ID_SUCURSAL = $idObject  
				ORDER BY NOMBRE ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
}