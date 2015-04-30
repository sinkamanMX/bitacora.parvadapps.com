<?php
/**
 * Archivo de definici—n de Unidades
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Extras extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'GTP_EXTRAS';
	protected $_primary = 'ID_EXTRA';

    public function insertTravel($data){
        $result     = false;
                
        $sql="INSERT INTO GTP_VIAJE_EXTRAS 
        		SET ID_VIAJE	= ".$data['inputIdViaje'].",
        		ID_EXTRA		= ".$data['inputExtra'].",
        		TOTAL			= ".$data['inputTotal'].",
        		CANTIDAD		= ".$data['inputCantidad'].",
        		FECHA_CREADO 	= CURRENT_TIMESTAMP";
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

    public function getIncidenciasByTravel($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT  *
				FROM GTP_VIAJE_EXTRAS E
				INNER JOIN GTP_EXTRAS I ON I.ID_EXTRA = E.ID_EXTRA
				WHERE E.ID_VIAJE = $idObject";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}	
        
		return $result;	    	
    }    
    
	
	public function getRowsEmp(){
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
				SET  DESCRIPCION 		= '".$data['inputDescripcion']."',
					 PRIORIDAD    		=  ".$data['inputPrioridad'].",
					 COSTO_EXTRA		=  ".$data['inputCosto'].", 
					 PRECIO_EXTRA 		=  ".$complement." " ;
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
				SET  DESCRIPCION        = '".$data['inputDescripcion']."',
                     PRIORIDAD          =  ".$data['inputPrioridad'].",
					 COSTO_EXTRA		=  ".$data['inputCosto'].", 
					 PRECIO_EXTRA 		=  ".$complement."                   
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

    public function getExtrasCosto(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT  *
                FROM $this->_name
                WHERE COSTO_EXTRA IS NOT NULL
                ORDER BY DESCRIPCION DESC";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;	    	
    }    
}	