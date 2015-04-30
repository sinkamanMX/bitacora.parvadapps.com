<?php
/**
 * Modelo de tabla: usuarios
 *
 * @package library.My.Models
 * @author EPENA
 */
class My_Model_Rutas extends My_Db_Table
{
    protected $_schema 	= 'taccsi';
	protected $_name 	= 'RUTAS';
	protected $_primary = 'ID_RUTA';
	
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

    public function insertRow($aData,$idRow){
        $result     = Array();
        $result['status']  = false;

        $sql="INSERT INTO  $this->_name
				SET ID_EMPRESA			=  ".$aData['inputEmpresa'].",
					DESCRIPCION			= '".$aData['inputDescripcion']."',
					DESCRIPCION_ORIGEN	= '".$aData['inputDirOrigen']."',
					LATITUD_ORIGEN		= '".$aData['inputLatOrigen']."',
					LONGITUD_ORIGEN		= '".$aData['inputLonOrigen']."',
					DESCRIPCION_DESTINO	= '".$aData['inputDirDestino']."',
					LATITUD_DESTINO		= '".$aData['inputLatDestino']."',
					LONGITUD_DESTINO	= '".$aData['inputLonDestino']."',
					TIEMPO_ESTIMADO		= '".$aData['inputTiempo']."',
					ESTATUS				= '".$aData['inputStatus']."',
					FECHA_CREADO        = CURRENT_TIMESTAMP";
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
    
    public function updateRow($aData,$idRow){
        $result     = Array();
        $result['status']  = false;

        $sql="UPDATE  $this->_name
				SET ID_EMPRESA			=  ".$aData['inputEmpresa'].",
					DESCRIPCION			= '".$aData['inputDescripcion']."',
					DESCRIPCION_ORIGEN	= '".$aData['inputDirOrigen']."',
					LATITUD_ORIGEN		= '".$aData['inputLatOrigen']."',
					LONGITUD_ORIGEN		= '".$aData['inputLonOrigen']."',
					DESCRIPCION_DESTINO	= '".$aData['inputDirDestino']."',
					LATITUD_DESTINO		= '".$aData['inputLatDestino']."',
					LONGITUD_DESTINO	= '".$aData['inputLonDestino']."',
					TIEMPO_ESTIMADO		= '".$aData['inputTiempo']."',
					ESTATUS				= '".$aData['inputStatus']."'
				WHERE $this->_primary   = ".$idRow;
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

	public function getCbo($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID, DESCRIPCION AS NAME
				FROM $this->_name
				WHERE ID_EMPRESA = $idEmpresa
				GROUP BY $this->_primary";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}
        
		return $result;			
	}      
}