<?php
/**
 * Modelo de tabla: usuarios
 *
 * @package library.My.Models
 * @author EPENA
 */
class My_Model_Adminusuarios extends My_Db_Table
{
    protected $_schema 	= 'gtp';
	protected $_name 	= 'USUARIOS';
	protected $_primary = 'ID_USUARIO';
	
	public function getDataTable(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT U.* ,P.*
				FROM $this->_name U 
				INNER JOIN PERFILES P ON U.ID_PERFIL = P.ID_PERFIL  
				WHERE P.ID_PERFIL != 2
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
    
  	public function userExist($sMail,$idObject=-1){  		
		$result= Array();
		$sFilter = ($idObject>0) ? "AND ID_USUARIO != $idObject": ""; 
    	$sql ="SELECT  *
                FROM ".$this->_name." 
                WHERE USUARIO = '".$sMail."' 
                $sFilter 
                LIMIT 1";		                	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query;			
		}	
        
		return $result;			
	}     
	
    public function insertRow($data){
        $result     = Array();
        $result['status']  = false;    

        $sql="INSERT INTO $this->_name	
        		SET ID_PERFIL		=  ".$data['inputPerfil'].",
					USUARIO			= '".$data['inputUser']."',
					PASSWORD		= SHA1('".$data['inputPassword']."'),
					PASSWORD_TEXT	= '".$data['inputPassword']."',
					NOMBRE			= '".$data['inputName']."',
					APELLIDOS		= '".$data['inputApps']."',
					EMAIL			= '".$data['inputUser']."',					
					ESTATUS			=  ".$data['inputStatus'].",
        			FECHA_REGISTRO	= CURRENT_TIMESTAMP";  
        Zend_Debug::dump($sql);   	  
        try{            
    		$query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				$result['id']  = $query_id[0]['ID_LAST'];  			 	
				$result['status']  = true;	
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	
    }  	

    public function updateRow($data,$idObject){
        $result     = Array();
        $result['status']  = false;  

        $sPassword = (isset($data['inputPassword']) && $data['inputPassword']!="") ? "PASSWORD		= SHA1('".$data['inputPassword']."'), PASSWORD_TEXT	= '".$data['inputPassword']."' , ": " ";

        $sql="UPDATE $this->_name	
        		SET ID_PERFIL		=  ".$data['inputPerfil'].",
					USUARIO			= '".$data['inputUser']."',
					$sPassword	
					NOMBRE			= '".$data['inputName']."',
					APELLIDOS		= '".$data['inputApps']."',
					EMAIL			= '".$data['inputUser']."',					
					ESTATUS			=  ".$data['inputStatus']."
        			WHERE $this->_primary = ".$idObject." LIMIT 1";     	  
        try{            
    		$query   = $this->query($sql,false);
    		$sql_id ="SELECT LAST_INSERT_ID() AS ID_LAST;";
			$query_id   = $this->query($sql_id);
			if(count($query_id)>0){
				$result['id']  = $query_id[0]['ID_LAST'];  			 	
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

    public function getUserToAssign(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT COUNT(ID_VIAJE) AS TOTAL, U.ID_USUARIO AS ID, CONCAT(U.NOMBRE,' ',U.APELLIDOS) AS NOMBRE, P.`DESCRIPCION` AS NAME_PERFIL
				FROM USUARIOS U 
				INNER JOIN PERFILES P ON U.ID_PERFIL = P.ID_PERFIL 								
								LEFT JOIN GTP_VIAJES V ON U.ID_USUARIO = V.ID_USUARIO_ASIGNADO AND V.ID_ESTATUS  IN(1,2)
								WHERE U.ID_PERFIL != 2
								  AND U.ESTATUS = 1
								 GROUP BY U.ID_USUARIO	
				ORDER BY NOMBRE ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}
        
		return $result;		    	    
    }
    
	public function getCboUsers(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT U.ID_USUARIO AS ID, CONCAT(U.NOMBRE,' ',U.APELLIDOS) AS NAME
				FROM $this->_name U 
				INNER JOIN PERFILES P ON U.ID_PERFIL = P.ID_PERFIL  
				WHERE P.ID_PERFIL != 2
				GROUP BY $this->_primary";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}
        
		return $result;			
	}      
}