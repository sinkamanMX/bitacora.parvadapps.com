<?php
/**
 * Archivo de definici—n de Unidades
 * 
 * @author epena
 * @package library.My.Models
 */
class My_Model_Monitor extends My_Db_Table
{
    protected $_schema 	= 'gtp_bd';
	protected $_name 	= 'GTP_VIAJES';
	protected $_primary = 'ID_VIAJE';

	public function getNoAssing(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT V.ID_VIAJE, V.CLAVE, V.INICIO, V.FIN, V.RETRASO, S.DESCRIPCION AS SUCURSAL, U.ECONOMICO, ST.ICONO,E.`NOMBRE`  AS CLIENTE,
				ST.ID_ESTATUS, ST.DESCRIPCION AS STATUS_VIAJE, R.DESCRIPCION AS DES_RUTA,
				U.IDENTIFICADOR
				FROM GTP_VIAJES V
				INNER JOIN SUCURSALES S ON V.ID_SUCURSAL = S.ID_SUCURSAL
				INNER JOIN EMPRESAS E ON S.ID_EMPRESA = E.ID_EMPRESA
				LEFT JOIN GTP_ESTATUS_VIAJE ST ON V.ID_ESTATUS = ST.ID_ESTATUS
				LEFT JOIN GTP_UNIDADES U ON V.ID_UNIDAD = U.ID_UNIDAD
				INNER JOIN RUTAS       R ON V.ID_RUTA      = R.ID_RUTA 
				WHERE V.ID_USUARIO_ASIGNADO IS NULL
				ORDER BY V.ID_VIAJE ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}	
        
		return $result;			
	}  

	public function getAssingUser($idUser,$iStatus=1,$dateEnd=0){
		$result= Array();
		$this->query("SET NAMES utf8",false);
		$sFilter = ($dateEnd != 0 ) ? 'AND CAST(V.FIN AS DATE )  = CURRENT_DATE': ''; 		
    	$sql ="SELECT V.ID_VIAJE, V.CLAVE, V.INICIO, V.FIN, V.RETRASO, S.DESCRIPCION AS SUCURSAL, U.ECONOMICO, ST.ICONO,E.`NOMBRE`  AS CLIENTE,
				ST.ID_ESTATUS, ST.DESCRIPCION AS STATUS_VIAJE, R.DESCRIPCION AS DES_RUTA,
				U.IDENTIFICADOR
				FROM GTP_VIAJES V
				INNER JOIN SUCURSALES S ON V.ID_SUCURSAL = S.ID_SUCURSAL
				INNER JOIN EMPRESAS E ON S.ID_EMPRESA = E.ID_EMPRESA
				LEFT JOIN GTP_ESTATUS_VIAJE ST ON V.ID_ESTATUS = ST.ID_ESTATUS
				LEFT JOIN GTP_UNIDADES U ON V.ID_UNIDAD = U.ID_UNIDAD
				INNER JOIN RUTAS       R ON V.ID_RUTA      = R.ID_RUTA 
				WHERE V.ID_USUARIO_ASIGNADO = $idUser
				  AND V.ID_ESTATUS          = $iStatus
				  $sFilter
				ORDER BY V.ID_VIAJE ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}	
        
		return $result;			
	}  	

    public function assignTravel($idTravel,$idUser){
        $result     = Array();
        $result['status']  = false;
        
        $sql="UPDATE GTP_VIAJES 
				SET ID_USUARIO_ASIGNADO = $idUser
				WHERE ID_VIAJE 		    = $idTravel
				LIMIT 1";
        try{            
    		$query   = $this->query($sql,false);
			if($query){
				$result['status']  = true;					
			}	
        }catch(Exception $e) {
            echo $e->getMessage();
            echo $e->getErrorMessage();
        }
		return $result['status'];	      		
    }	
    
    public function monitorStatus($idUser){
		$result= Array();
		$this->query("SET NAMES utf8",false);		 	
    	$sql ="SELECT V.ID_VIAJE, V.CLAVE, E.NOMBRE,
				IF((TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP, MAX(D.CREADO)))/60) >= T.FRECUENCIA_RASTREO,'1', IF(D.ID_VIAJE IS NULL,'1','0') ) AS FLAG_INSERTAR_POS,
				T.FRECUENCIA_RASTREO,
				IF(D.ID_VIAJE IS NULL,'0','1')  AS EXIST_INFO,					
				IF(D.CREADO IS NULL,'Sin Reporte', (TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP, MAX(D.CREADO)))/60) ) AS MINUTOS_SIN_REPORTAR,
				ID_USUARIO_ASIGNADO,
				IF(D.CREADO IS NULL,'Sin Reporte', MAX(D.CREADO)) AS LAST_FECHA
				FROM GTP_VIAJES V
				INNER JOIN TIPO_VIAJES       T ON T.ID_TIPO_VIAJE = V.ID_TIPO_VIAJE
				INNER JOIN USUARIOS 		 U ON V.ID_USUARIO_ASIGNADO = U.ID_USUARIO
				 LEFT JOIN GTP_DETALLE_VIAJE D ON D.ID_VIAJE 	  = V.ID_VIAJE 
				 INNER JOIN SUCURSALES       S ON V.ID_SUCURSAL   = S.ID_SUCURSAL
				 INNER JOIN EMPRESAS         E ON E.ID_EMPRESA    = S.ID_EMPRESA
				 WHERE V.ID_USUARIO_ASIGNADO = $idUser
				   AND V.ID_ESTATUS 		 = 2
				   AND U.ID_PERFIL NOT IN(2)
				GROUP BY V.ID_VIAJE   
				ORDER BY D.CREADO  ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}	
        
		return $result;		    	
    }
    
	public function getCurrentTravels(){
		$result= Array();
		$this->query("SET NAMES utf8",false); 		
    	$sql ="SELECT V.ID_VIAJE, V.CLAVE, V.INICIO, V.FIN, V.RETRASO, S.DESCRIPCION AS SUCURSAL, U.ECONOMICO, ST.ICONO,E.`NOMBRE`  AS CLIENTE,
				ST.ID_ESTATUS, ST.DESCRIPCION AS STATUS_VIAJE, R.DESCRIPCION AS DES_RUTA,				
				CONCAT(A.NOMBRE, ' ',A.APELLIDOS) AS MONITOR,
				U.IDENTIFICADOR
				FROM GTP_VIAJES V
				INNER JOIN SUCURSALES S ON V.ID_SUCURSAL = S.ID_SUCURSAL
				INNER JOIN EMPRESAS E ON S.ID_EMPRESA = E.ID_EMPRESA
				INNER JOIN USUARIOS A ON V.ID_USUARIO_ASIGNADO = A.ID_USUARIO
				LEFT JOIN GTP_ESTATUS_VIAJE ST ON V.ID_ESTATUS = ST.ID_ESTATUS
				LEFT JOIN GTP_UNIDADES U ON V.ID_UNIDAD = U.ID_UNIDAD
				INNER JOIN RUTAS       R ON V.ID_RUTA      = R.ID_RUTA 
				WHERE V.ID_ESTATUS          = 2
				ORDER BY V.ID_VIAJE ASC";
		$query   = $this->query($sql);
		if(count($query)>0){		  
			$result = $query;
		}	
        
		return $result;			
	}     
}