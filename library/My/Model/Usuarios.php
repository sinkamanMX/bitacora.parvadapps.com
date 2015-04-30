<?php
/**
 * Archivo de definición de usuarios
 * 
 * @author EPENA
 * @package library.My.Models
 */

/**
 * Modelo de tabla: usuarios
 *
 * @package library.My.Models
 * @author EPENA
 */
class My_Model_Usuarios extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'USUARIOS';
	protected $_primary = 'ID_USUARIO';
	
	public function validateUser($datauser){
		$result= Array();		
		$this->query("SET NAMES utf8",false);
    	$sql ="SELECT $this->_primary
	    		FROM USUARIOS U
				WHERE U.USUARIO  = '".$datauser['usuario']."'
                 AND  U.PASSWORD = SHA1('".$datauser['contrasena']."')";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];
		}
        
		return $result;			
	} 
    
    public function getDataUser($idObject){
      	$this->query("SET NAMES utf8",false); 
        
		$result= Array();
    	$sql ="SELECT U.* ,P.*, S.*, E.* ,IF(E.NOMBRE IS NULL ,'MONITOREO',E.NOMBRE) AS N_EMPRESA, U.NOMBRE AS N_USUARIO
				FROM USUARIOS U
				INNER JOIN PERFILES    P  ON U.ID_PERFIL     = P.ID_PERFIL
				LEFT JOIN USR_EMPRESA UE ON U.ID_USUARIO    = UE.ID_USUARIO
				LEFT JOIN SUCURSALES  S  ON UE.ID_SUCURSAL  = S.ID_SUCURSAL
				LEFT JOIN EMPRESAS    E  ON S.ID_EMPRESA    = E.ID_EMPRESA
                WHERE U.ID_USUARIO = $idObject";			         	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];			
		}	
        
		return $result;	        
    }  
    
    public function validatePassword($datauser){
		$result= Array();		
		$this->query("SET NAMES utf8",false);
    	$sql ="SELECT $this->_primary
	    		FROM USUARIOS U
				WHERE U.PASSWORD   = SHA1('".$datauser['VPASSWORD']."')
				  AND U.ID_USUARIO = ".$datauser['ID_USUARIO'];
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];
		}
        
		return $result;		    	
    }
    
    public function changePass($datauser){
        $result     = Array();
        $result['status']  = false;

        $sql="UPDATE  $this->_name
				SET  PASSWORD 	=  SHA1('".$datauser['NPASSWORD']."')					 
					 WHERE $this->_primary =".$datauser['ID_USUARIO']." LIMIT 1";
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
    
  	public function userExist($sMail){
		$result= Array();
    	$sql ="SELECT  *
                FROM ".$this->_name." 
                WHERE USUARIO = '".$sMail."' LIMIT 1";			         	
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];			
		}	
        
		return $result;			
	} 

    public function insertRowRegister($data){
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
					TEL_MOVIL		= '".$data['inputTelMovilUser']."',
					TEL_FIJO		= '".$data['inputTelFijoUser']."',
					ESTATUS			=  ".$data['inputEstatus'].",
					CODE_ACTIVATION = '".$data['codeActivation']."',
        			FECHA_REGISTRO	= CURRENT_TIMESTAMP";     	  
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
    
    public function setSucursal($data){
        $result = false;
        try{
        	$sql="INSERT INTO USR_EMPRESA
        		SET ID_USUARIO    	= ".$data['inputIdUsuario'].",
					ID_SUCURSAL		= ".$data['inputSucursal'];
                    
    		$query   = $this->query($sql,false);
			if($query){  			 	
				$result  = true;	
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;	
    }   

	public function validateKeyActivation($codeActivation){
		$result= Array();		
		$this->query("SET NAMES utf8",false);
    	$sql ="SELECT USUARIOS.ID_USUARIO,SUCURSALES.ID_EMPRESA
	    		FROM  USUARIOS
	    		INNER JOIN USR_EMPRESA ON USUARIOS.ID_USUARIO    = USR_EMPRESA.ID_USUARIO  
	    		INNER JOIN SUCURSALES  ON SUCURSALES.ID_SUCURSAL = USR_EMPRESA.ID_SUCURSAL
				WHERE CODE_ACTIVATION  = '".$codeActivation."'";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];
		}
        
		return $result;			
	}   

    public function setActivateUser($idUser){
        $result     = Array();
        $result['status']  = false;

        $sql="UPDATE $this->_name
				SET  CODE_ACTIVATION = '',
					 ESTATUS         = 1					 
			  WHERE $this->_primary = ".$idUser." LIMIT 1";
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
    
	
    public function setKeyRestore($data){
        $result  = false;
        
        $sql="UPDATE $this->_name	
        		SET CLAVE_RECUPERACION = '".$data['inputClave']."'
        		WHERE $this->_primary  = ".$data['idUser']." LIMIT 1";			  
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result = true;								
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;
    }     

  	public function validateKeyRecovery($sCodeRecovery){
		$result= Array();
    	$sql ="SELECT  *
                FROM ".$this->_name." 
                WHERE CLAVE_RECUPERACION = '".$sCodeRecovery."' LIMIT 1";
		$query   = $this->query($sql);
		if(count($query)>0){
			$result	 = $query[0];			
		}	
		return $result;	
		
	}      
    	
    public function updatePassword($data){
        $result  = false;
        
        $sql="UPDATE $this->_name
				SET PASSWORD	=  SHA1('".$data['inputPassword']."'), 
				PASSWORD_TEXT	= '".$data['inputPassword']."'
        		WHERE $this->_primary  = ".$data['idReset']." LIMIT 1";		  
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result = true;								
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result;
    }       
}