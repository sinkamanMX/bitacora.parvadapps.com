<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_TipoViajes extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'TIPO_VIAJES';
	protected $_primary = 'ID_TIPO_VIAJE';
	
	public function getCbo($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID, DESCRIPCION AS NAME
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
    	$sql ="SELECT V.DESCRIPCION AS T_DESC, V.*,P.*
				FROM TIPO_VIAJES V
				INNER JOIN PRECIOS_SERVICIO P ON V.ID_PRECIO = P.ID_PRECIO
				WHERE V.ID_TIPO_VIAJE = ".$idObject." LIMIT 1";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];
		}
        
		return $result;				
	}
	
	public function getDataTable(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT T.* , P.DESCRIPCION AS DESC_PRECIO, P.COSTO
				FROM $this->_name T
				INNER JOIN PRECIOS_SERVICIO P ON T.ID_PRECIO = P.ID_PRECIO
				GROUP BY T.$this->_primary";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}
        
		return $result;			
	}
	
    public function insertRow($aData,$idRow){
        $result     = Array();
        $result['status']  = false;

        $sql="INSERT INTO  $this->_name
        		SET ID_PRECIO		=  ".$aData['inputPrecio'].",
        			DESCRIPCION 	= '".$aData['inputDescripcion']."',
        			PRIORIDAD		=  ".$aData['inputPrioridad'].",
        			ESTATUS			=  ".$aData['inputStatus'].",
        			FRECUENCIA_RASTREO=  ".$aData['inputFrecuencia'].", 
        			FECHA_CREADO    = CURRENT_TIMESTAMP";
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
    
    public function updateRow($aData,$idObject){
        $result     = Array();
        $result['status']  = false;

        $sql="UPDATE  $this->_name
        		SET ID_PRECIO		=  ".$aData['inputPrecio'].",
        			DESCRIPCION 	= '".$aData['inputDescripcion']."',
        			PRIORIDAD		=  ".$aData['inputPrioridad'].",
        			ESTATUS			=  ".$aData['inputStatus'].",
        			FRECUENCIA_RASTREO=  ".$aData['inputFrecuencia']."
				WHERE $this->_primary   = ".$idObject;
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
					 WHERE $this->_primary = ".$data['catId']." LIMIT 1";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
    }       
}