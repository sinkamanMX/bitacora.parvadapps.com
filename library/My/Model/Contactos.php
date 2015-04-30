<?php
/**
 * Modelo de tabla: usuarios
 *
 * @package library.My.Models
 * @author EPENA
 */
class My_Model_Contactos extends My_Db_Table
{
    protected $_schema 	= 'gtp';
	protected $_name 	= 'EMPRESA_CORREOS';
	protected $_primary = 'ID_CORREO';
	
	public function getDataTable($idEmpresa){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT * 
				FROM $this->_name
				WHERE ID_EMPRESA = $idEmpresa
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
    
    public function insertRow($aData){
        $result     = Array();
        $result['status']  = false;
        
        $bIncidencia = (isset($aData['chkIncidencia']) && $aData['chkIncidencia']!="") 	? 1: 0;
        $bInicio     = (isset($aData['chkInicio'])	   && $aData['chkInicio']!="") 		? 1: 0;
        $bFin 		 = (isset($aData['chkFin'])		    && $aData['chkFin']!="") 		? 1: 0;
        
        $sql="INSERT INTO  $this->_name
        		SET ID_EMPRESA		= ".$aData['inputEmpresa'].",
        			NOMBRE			= '".$aData['inputName']."',
        			CORREO 			= '".$aData['inputEmail']."',
        			INCIDENCIAS		= ".$bIncidencia.",
        			INICIO_VIAJE	= ".$bInicio.",
        			FIN_VIAJE		= ".$bFin.",
        			ESTATUS			= ".$aData['inputStatus'].",
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
        
        $bIncidencia = (isset($aData['chkIncidencia']) && $aData['chkIncidencia']!="") 	? 1: 0;
        $bInicio     = (isset($aData['chkInicio'])	   && $aData['chkInicio']!="") 		? 1: 0;
        $bFin 		 = (isset($aData['chkFin'])		    && $aData['chkFin']!="") 		? 1: 0; 

        $sql="UPDATE  $this->_name
        		SET NOMBRE			= '".$aData['inputName']."',
        			CORREO 			= '".$aData['inputEmail']."',
        			INCIDENCIAS		= ".$bIncidencia.",
        			INICIO_VIAJE	= ".$bInicio.",
        			FIN_VIAJE		= ".$bFin.",
        			ESTATUS			= ".$aData['inputStatus']."
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

    public function getContactsBy($option='inc',$idTravel){
		$result= Array();
		
		if($option=='inc'){
			$sFilter = "AND  INCIDENCIAS = 1";
		}elseif($option=='beg'){
			$sFilter = "AND INICIO_VIAJE = 1";
		}elseif($option=='end'){
			$sFilter = "AND FIN_VIAJE = 1";	
		}
		
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT *
				FROM EMPRESA_CORREOS
				WHERE ID_EMPRESA IN (
					SELECT S.ID_EMPRESA
					FROM GTP_VIAJES V
				 	INNER JOIN SUCURSALES S ON V.ID_SUCURSAL   = S.ID_SUCURSAL
				 	WHERE V.ID_VIAJE = $idTravel
				) AND ESTATUS = 1 ".$sFilter ;
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}
        
		return $result;	    	
    }
}