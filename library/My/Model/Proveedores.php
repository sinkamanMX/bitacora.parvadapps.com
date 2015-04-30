<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Proveedores extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'PROVEEDORES';
	protected $_primary = 'ID_PROVEEDOR';
	
	public function getCbo($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary  AS ID, DESCRIPCION AS NAME 
				FROM $this->_name
				WHERE ID_EMPRESA = $idObject
				   OR ID_EMPRESA = 0 
				GROUP BY $this->_primary";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}  
	
	public function getDataTableEmp($idObject){
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
				SET ID_EMPRESA 	=  ".$data['inputEmpresa'].",
					DESCRIPCION = '".$data['inputDescripcion']."',
					URL			= '".$data['inputUrl']."',
					USUARIO		= '".$data['inputUsuario']."',
					PASSWORD	= '".$data['inputPassword']."',
					COMENTARIO	= '".$data['inputComentario']."',
					ESTATUS		=  ".$data['inputStatus'].",
					FECHA_CREADO= CURRENT_TIMESTAMP";
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
    
    public function updateRow($data,$idObject){
        $result     = Array();
        $result['status']  = false;

        $sql="UPDATE  $this->_name
				SET DESCRIPCION = '".$data['inputDescripcion']."',
					URL			= '".$data['inputUrl']."',
					USUARIO		= '".$data['inputUsuario']."',
					PASSWORD	= '".$data['inputPassword']."',
					COMENTARIO	= '".$data['inputComentario']."',
					ESTATUS		=  ".$data['inputStatus']."
					WHERE $this->_primary = $idObject";
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
}