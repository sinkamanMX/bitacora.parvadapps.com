<?php
/**
 * Archivo de definici—n de perfiles
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Perfiles extends My_Db_Table
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

    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;
        /*
        $sql="INSERT INTO $this->_name
				SET  ID_EMPRESA 		=  ".$data['inputTransportista'].",
					 DESCRIPCION 		= '".$data['inputDescripcion']."',
					 ACTIVO				=  ".$data['inputStatus'].",					 					 
					 REGISTRO 			= CURRENT_TIMESTAMP";
        */
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
	
        /*
        $sql="UPDATE  $this->_name
				SET  ID_EMPRESA 		=  ".$data['inputTransportista'].",
					 DESCRIPCION 		= '".$data['inputDescripcion']."',
					 ACTIVO				=  ".$data['inputStatus'].",		 					 
					 REGISTRO 			= CURRENT_TIMESTAMP
					 WHERE $this->_primary = $idObject LIMIT 1";
        */
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

	
	public function getModuleDefault($idProfile){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT *
				FROM MODULOS_PERFIL MP
				INNER JOIN MODULOS M ON MP.ID_MODULO = M.ID_MODULO
				WHERE MP.ID_PERFIL = $idProfile
				 AND  MP.INICIO    = 1 AND M.ACTIVO = 1 LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	  		
	}   

	public function getModules($idObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT M.*, M.DESCRIPCION AS M_DESCRIPCION,N.DESCRIPCION AS N_DESCRIPCION,N.ID_MENU AS IDMENU, N.*, M.SCRIPT AS S_MODULE
				FROM MODULOS_PERFIL MP
				INNER JOIN MODULOS M ON MP.ID_MODULO = M.ID_MODULO
				INNER JOIN MENU    N ON M.ID_MENU    = N.ID_MENU 
				WHERE MP.ID_PERFIL = ".$idObject." AND M.ACTIVO = 1
				ORDER BY N.ID_MENU ASC, M.DESCRIPCION ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}
	
	public function getDataModule($classObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT *
				FROM  MODULOS
				WHERE CLASE = '".$classObject."' LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	 		
	}
	
	public function getDataMenu($classObject){
		$result= Array();
		$this->query("SET NAMES utf8",false); 
    	$sql ="SELECT *
				FROM  MENU
				WHERE CLASE = '".$classObject."' LIMIT 1";	
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query[0];			
		}	
        
		return $result;	 		
	}

	public function getCbo(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT ID_PERFIL AS ID, DESCRIPCION AS NAME
				FROM PERFILES
				WHERE ID_PERFIL != 2
				GROUP BY NAME ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;			
		}	
        
		return $result;			
	}  	
}