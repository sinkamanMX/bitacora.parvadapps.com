<?php
/**
 * Modelo de tabla: usuarios
 *
 * @package library.My.Models
 * @author EPENA
 */
class My_Model_Precios extends My_Db_Table
{
    protected $_schema 	= 'gtp';
	protected $_name 	= 'PRECIOS_SERVICIO';
	protected $_primary = 'ID_PRECIO';
	
	public function getDataTable(){
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

    public function insertRow($aData,$idRow){
        $result     = Array();
        $result['status']  = false;
        
        $iCostoExtra = (isset($aData['inputCostoExtra'])&& $aData['inputCostoExtra']!="") ? $aData['inputCostoExtra']: 'NULL';

        $sql="INSERT INTO  $this->_name
        		SET DESCRIPCION 	= '".$aData['inputDescripcion']."',
        			COSTO			=  ".$aData['inputCosto'].",
        			COSTO_EXTRA		=  ".$iCostoExtra.",
        			ESTATUS			=  ".$aData['inputStatus'].",
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
        
        $iCostoExtra = (isset($aData['inputCostoExtra'])&& $aData['inputCostoExtra']!="") ? $aData['inputCostoExtra']: 'NULL'; 

        $sql="UPDATE  $this->_name
        		SET DESCRIPCION 	= '".$aData['inputDescripcion']."',
        			COSTO			=  ".$aData['inputCosto'].",
        			COSTO_EXTRA		=  ".$iCostoExtra.",
        			ESTATUS			=  ".$aData['inputStatus']."
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
		return $result;	     	
    }  

	public function getCbo(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT $this->_primary AS ID, CONCAT(DESCRIPCION,'-$',COSTO) AS NAME
				FROM $this->_name
				GROUP BY $this->_primary";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}
        
		return $result;			
	}      
}