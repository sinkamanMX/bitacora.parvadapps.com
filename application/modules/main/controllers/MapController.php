<?php

class main_MapController extends My_Controller_Action
{
	protected $_clase = 'map';
	public $dataIn;
	public $idToUpdate=-1;
	public $errors = Array();
	public $operation='init';
	public $resultop=null;	
	public $userUpdate=-1;
	
    public function init()
    {
		$sessions = new My_Controller_Auth();
		$perfiles = new My_Model_Perfiles();
        if(!$sessions->validateSession()){
            $this->_redirect('/');		
		}
		$this->view->dataUser   = $sessions->getContentSession();
		$this->view->modules    = $perfiles->getModules($this->view->dataUser['ID_PERFIL']);
		$this->view->moduleInfo = $perfiles->getDataModule($this->_clase);

		$this->dataIn = $this->_request->getParams();
		$this->dataIn['userRegister']	= $this->view->dataUser['ID_USUARIO'];
		$this->validateNumbers = new Zend_Validate_Digits();
				
		if(isset($this->dataIn['optReg'])){
			$this->operation = $this->dataIn['optReg'];
			
			if($this->operation=='update'){
				$this->operation = $this->dataIn['optReg'];

				$this->validateAlpha   = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));				
			}	
		}
		
		if(isset($this->dataIn['catId']) && $this->validateNumbers->isValid($this->dataIn['catId'])){
			$this->idToUpdate 	   = $this->dataIn['catId'];	
		}else{
			$this->idToUpdate 	   = -1;
			$this->errors['status'] = 'no-info';
		}		
    }
    
    public function indexAction(){
    	try{
    		
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 
    }
    
    public function getravelsAction(){
    	$result = '';
		try{  
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();

    		$travels = new My_Model_Viajes();
    		$travelsOn = $travels->getRowsEmp($this->view->dataUser['ID_EMPRESA']);
    		
    		foreach ($travelsOn as $value => $items){
    			$infoLastP = $travels->lastPosition($items['ID_VIAJE']);
    			$dataposition = '';
    			if(count($infoLastP)>0){    				
    				$dataposition = $infoLastP['LATITUD']."|".$infoLastP['LONGITUD']."|".$infoLastP['FECHA']."|".
    								$infoLastP['UBICACION']."|".round($infoLastP['VELOCIDAD'],2)."|".$infoLastP['ANGULO']."|".$infoLastP['MODO']
    								."|".$infoLastP['INCIDENCIA'];
    			}else{
    				$dataposition = "null|null|null|".
    								"null|null|null|null|--";
    			}
				$result .=  ($result=="") ? "" : "!";
				$result .=  $items['ID_VIAJE']."|".
							$items['CLAVE']."|".
							$items['INICIO']."|".
							$items['FIN']."|".
							$items['RETRASO']."|".
							$items['CLIENTE']."|".
							$items['ECONOMICO']."|".
							$items['ICONO']."|".
							$items['SUCURSAL']."|".
							$items['ID_ESTATUS']."|".
							$dataposition;	
    		}
			echo $result;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }
    
    public function infotravelAction(){
    	$this->view->layout()->setLayout('layout_blank');
		$dataInfo 			= Array();
		$clientes 			= '';
		$IdTransportistas 	= -1;		
		$aUnidades			= Array();
		$aOperadores		= Array();
		$aIncidencias		= Array();
		$aRecorrido			= Array();
		
		$classObject 	= new My_Model_Viajes();
		$functions   	= new My_Controller_Functions();
		$sucursales 	= new My_Model_Sucursales();
		$transportistas = new My_Model_Transportistas();
		
		$this->view->sucursales     = $sucursales->getRowsEmp($this->view->dataUser['ID_EMPRESA']);
		$this->view->transportistas = $transportistas->getRowsEmp($this->view->dataUser['ID_EMPRESA']);
		
    	if($this->idToUpdate >-1){
			$dataInfo    = $classObject->getData($this->idToUpdate);
			
			$clients     	 = new My_Model_Clientes();
			$cboValues       = $clients->getCbo($dataInfo['ID_SUCURSAL'],$this->view->dataUser['ID_EMPRESA']);
			$clientes        = $functions->selectDb($cboValues,$dataInfo['ID_CLIENTE']);

			$operadores  	 = new My_Model_Operadores();
			$IdTransportistas= $operadores->getData($dataInfo['ID_OPERADOR']);	
			
			$unidades		 = new My_Model_Unidades();
			$aUnidades		 = $unidades->getCbo($IdTransportistas['ID_TRANSPORTISTA'],$this->view->dataUser['ID_EMPRESA']);
			
			$aOperadores	 = $operadores->getCbo($IdTransportistas['ID_TRANSPORTISTA'],$this->view->dataUser['ID_EMPRESA']);
			
			$aIncidencias	 = $classObject->getIncidencias($this->idToUpdate);
			
			$aRecorrido      = $classObject->getRecorrido($this->idToUpdate);
		}	
		
		if($this->operation=='update'){			
			if($this->idToUpdate>-1){
				 $updated = $classObject->updateRow($this->dataIn);
				 if($updated['status']){
				 	$dataInfo    = $classObject->getData($this->idToUpdate);
				 	$this->resultop = 'okRegister';	
				 }
			}else{
				$this->errors['status'] = 'no-info';
			}	
		}else if($this->operation=='new'){
			$insert = $classObject->insertRow($this->dataIn);
			if($insert['status']){
				$this->idToUpdate	= $insert['id'];
				$this->resultop = 'okRegister';	
				$dataInfo    = $classObject->getData($this->idToUpdate);
			}else{
				$this->errors['status'] = 'no-insert';
			}
		}				
		
		$this->view->clientes = $clientes; 
		$this->view->idTransportista = $IdTransportistas['ID_TRANSPORTISTA'];
		$this->view->operadores = $aOperadores;
		$this->view->unidades	= $aUnidades;
		$this->view->incidencias= $aIncidencias;
		$this->view->data 		= $dataInfo; 
		$this->view->recorrido  = $aRecorrido;
		$this->view->error 		= $this->errors;	
		$this->view->resultOp   = $this->resultop;
		$this->view->catId		= $this->idToUpdate;
		$this->view->idToUpdate = $this->idToUpdate;		
    }
    
    public function chagestatusAction(){
    		try{   			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();    
	                
	        $answer = Array('answer' => 'no-data');
			$data = $this->_request->getParams();
			
			$validateNumbers = new Zend_Validate_Digits();
			$validateString  = new Zend_Validate_Alnum();	
			$cFunctions 	 = new My_Controller_Functions();	
		
			if($validateNumbers->isValid($data['catId'])  && 
				$validateString->isValid($data['option'])){
			
				$idUpdated    = $data['catId'];
				$optionUpdate = $data['option'];
				$statusChange = 0;

				$classObject = new My_Model_Viajes();
				$infoData   = $classObject->getData($idUpdated);
				$cContactos   = new My_Model_Contactos(); 	
				$aContactos	  = Array();
				if($optionUpdate=='start'){
					$statusChange = ($infoData['ID_ESTATUS']==1) ? 2 : $infoData['ID_ESTATUS'];
									
					$aContactos   = $cContactos->getContactsBy('beg',$idUpdated);
					$cFunctions->sendNotifications(2,$aContactos,$infoData['CLAVE']);
										
				}else if($optionUpdate=='stop'){
					$statusChange = ($infoData['ID_ESTATUS']==2) ? 4 : $infoData['ID_ESTATUS'];
					$aContactos   = $cContactos->getContactsBy('end',$idUpdated);
					$cFunctions->sendNotifications(3,$aContactos,$infoData['CLAVE']);
				}
				
				$updated = $classObject->changeStatus($statusChange,$idUpdated);	
				if($updated){
					$infoData   = $classObject->getData($idUpdated);
					if($infoData['ID_ESTATUS']==2){
						$answer = Array('answer' => 'started');
					}elseif($infoData['ID_ESTATUS']==4){
						$answer = Array('answer' => 'stoped');	
					} 
				}else{
					$answer = Array('answer' => 'problem'); 
				}							
			}
		
		echo Zend_Json::encode($answer);   		
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function setincidenciaAction(){
    	$this->view->layout()->setLayout('layout_blank');    	
		$data = $this->_request->getParams();
		$result = false;
		$validateNumbers = new Zend_Validate_Digits();
		$validateString  = new Zend_Validate_Alnum();		
		$travels 		 = new My_Model_Viajes();
		$cFunctions      = new My_Controller_Functions();
		if(isset($data['option'])){
			if($validateNumbers->isValid($data['catId'])  && 
				$validateString->isValid($data['option'])){
				
				if($data['option']=='insert'){					
					$data['userRegister']	= $this->view->dataUser['ID_USUARIO'];
					$insert  = $travels->setIncidencia($data);
					if($insert){
						$cIndicencias = new My_Model_Incidencias();
						$aDataInc	  = $cIndicencias->getData($data['catId']);
						if($aDataInc['PRIORIDAD']==1){
							$classObject = new My_Model_Viajes();
							$infoData   = $classObject->getData($data['catId']);							
							
							$cContactos	  = new My_Model_Contactos();
							$aContactos   = $cContactos->getContactsBy('inc',$idUpdated);							
							$cFunctions->sendNotifications(1,$aContactos,$infoData['CLAVE']);							
						}
						
						$result =false;
					}
				}
			}			
		} 
		
		$this->view->incidencias = $travels->getTipoIncidencias($this->view->dataUser['ID_EMPRESA']);
		$this->view->insert = $result;
		$this->view->catId = $data['catId'];
    }
    
    public function manualposAction(){
    	$aDataViaje = Array();
    	$this->view->layout()->setLayout('layout_blank');	
		$data = $this->_request->getParams();
		$result = false;
		$validateNumbers = new Zend_Validate_Digits();
		$validateString  = new Zend_Validate_Alnum();		
		$travels 		 = new My_Model_Viajes();
		
		if(isset($data['catId'])){
			$aDataViaje = $travels->getDataComplete($data['catId']);
		}
		
		if(isset($data['option'])){
			if($validateNumbers->isValid($data['catId'])  && 
				$validateString->isValid($data['option'])){
				
				if($data['option']=='insert'){					
					$data['userRegister']	= $this->view->dataUser['ID_USUARIO'];
					$insert  = $travels->setManualPosition($data);
					if($insert['status']){
						if(isset($data['inputIncidencia']) && $data['inputIncidencia']!=""){
							$idHistorico 	   = $insert['id'];
							$insertIncidencia  = $travels->setIncidencia($data,$idHistorico);
							if($insertIncidencia){
								$result =true;
							}	
						}else{
							$result =true;
						}
					}
				}
			}			
		} 
		
		$this->view->incidencias = $travels->getTipoIncidencias($this->view->dataUser['ID_EMPRESA']);
		$this->view->insert 	 = $result;
		$this->view->catId 		 = $data['catId'];
		$this->view->aDataViaje	 = $aDataViaje;    	
    }
    
    public function externalAction(){
    	try{
	    	$this->view->layout()->setLayout('layout_blank');	
			$data 	 = $this->_request->getParams();
			$result  		 = false;
			$cViajes 		 = new My_Model_Viajes();
			$cUnidades		 = new My_Model_Unidades();
			$aDataViaje  	 = Array();
			$aDataUnidad 	 = Array();
			$validateNumbers = new Zend_Validate_Digits();
			 
			if($validateNumbers->isValid($data['catId']) ){
				$idViaje 	= $data['catId'];
				$aDataViaje = $cViajes->getData($idViaje);
				$aDataUnidad= $cUnidades->getDataComplete($aDataViaje['ID_UNIDAD']);
			}else{
				$this->errors['noinfo'] = 1;
			}
					
			$this->view->aDataUnidad = $aDataUnidad;
			$this->view->aDataViaje  = $aDataViaje;
			$this->view->aErrors	 = $this->errors;
    	} catch (Zend_Exception $e) {
        	echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }		
    }  

    public function getpositionlogAction(){
		try{
			$answer = Array('answer' => 'no-data');
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			
			$validateNumbers = new Zend_Validate_Digits();
			$validateString  = new Zend_Validate_Alnum();		
			$cViajes 		 = new My_Model_Viajes();	
			$cUnidades		 = new My_Model_Unidades();		
			
			if($validateNumbers->isValid($this->dataIn['catId']) ){
				$idViaje 	= $this->dataIn['catId'];
				$aDataViaje = $cViajes->getDataComplete($idViaje);
				$aDataUnidad= $cUnidades->getDataComplete($aDataViaje['ID_UNIDAD']);
				$userUda 	= $aDataViaje['USUARIO_UDA'];
    			$passUda 	= $aDataViaje['PASSWORD_UDA'];				
				
    		  	$soap_client  = new SoapClient("http://192.168.6.41/ws/wsUDAHistoryGetByPlate.asmx?WSDL");
				$aParams 	  = array('sLogin'     => $userUda,
			                  		  'sPassword'  => $passUda,
									  'sPlate' 	   => $aDataUnidad['PLACAS']);
				
				$result=$soap_client->HistoyDataLastLocationByPlate($aParams);
				
				if (is_object($result)){
			       	$x = get_object_vars($result); 	
					$y = get_object_vars($x['HistoyDataLastLocationByPlateResult']);
					$xml = $y['any'];		
					if($xml2 = simplexml_load_string($xml)){						
						$bContinue = true;						
						if($xml2->Response->Status->code=='101'){
							$answer = Array('answer' => 'login');
							$bContinue = false;								
						}
						
						if($bContinue){
							$c = 0;
							for($i = 0 ; $i < count($xml2->Response->Plate) ; $i++){
								$aDataPosition  = Array();
								$sFechaServer 	= (string) $xml2->Response->Plate[$i]->hst->DateTimeServer;
								$sFechaServer 	= str_replace("/", "-", $sFechaServer);
								$sLocation      =  (string)$xml2->Response->Plate[$i]->hst->Location;							
								$vowels = array("&", "%", "'", '"' );
								$sLocation  = str_replace($vowels, " ", $sLocation);
								
								$aDataPosition['sFechaServer'] 	= $sFechaServer; 
								$aDataPosition['fLatitude'] 	= (string)$xml2->Response->Plate[$i]->hst->Latitude;
								$aDataPosition['fLongitude'] 	= (string)$xml2->Response->Plate[$i]->hst->Longitude;
								$aDataPosition['iVelocidad']  	= (string)$xml2->Response->Plate[$i]->hst->Speed;
								$aDataPosition['iAngle']		= (string)$xml2->Response->Plate[$i]->hst->Angle;
								$aDataPosition['sLocation']		= $sLocation;
								
								$answer = Array('answer' => 'ok',
											   'dataPos' => $aDataPosition);
				        	}							
						}			        	
					}else{
						$answer = Array('answer' => 'problem');
					}
				}else{
					$answer = Array('answer' => 'problem');
				}
			}else{
	            $answer = Array('answer' => 'problem');	
	        }
	        
	        echo Zend_Json::encode($answer);   
    	} catch (Zend_Exception $e) {
        	echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }	 	
    }
}