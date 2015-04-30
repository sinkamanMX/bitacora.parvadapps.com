<?php
class monitor_MapController extends My_Controller_Action
{		
	protected $_clase = 'monitor';	
	public $validateNumbers;
	public $validateAlpha;
	
    public function init()
    {
    	try{
	    		
			$sessions = new My_Controller_Auth();
			$perfiles = new My_Model_Perfiles();
	        if(!$sessions->validateSession()){
	            $this->_redirect('/');		
			}
			
			$this->_dataUser        = $sessions->getContentSession();
			$this->view->modules    = $perfiles->getModules($this->_dataUser['ID_PERFIL']);
			$this->view->moduleInfo = $perfiles->getDataModule($this->_clase);
			$this->view->idEmpresa  = $this->_dataUser['ID_EMPRESA'];		
			$this->_dataIn			= $this->_request->getParams();
			$this->validateNumbers = new Zend_Validate_Digits();		
					
			if(isset($this->_dataIn['optReg'])){
				$this->_dataOp	 = $this->_dataIn['optReg'];
				
				if($this->_dataOp=='update'){
					$this->_dataOp = $this->_dataIn['optReg'];
	
					$this->validateAlpha   = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));				
				}	
			}
			
			if(isset($this->_dataIn['catId']) && $this->validateNumbers->isValid($this->_dataIn['catId'])){
				$this->_idUpdate	   = $this->_dataIn['catId'];	
			}else{
				$this->_idUpdate 	   = -1;
				$this->_aErrors['status'] = 'no-info';
			}

			$this->view->dataUser = $this->_dataUser;
			$this->view->bPageAll = true;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 		
    }	
    
    public function indexAction(){
    	try{
			$dataInfo 			= Array();			
			$aIncidencias		= Array();
			$aRecorrido			= Array();
			
			$classObject 	= new My_Model_Viajes();
			$functions   	= new My_Controller_Functions();
			$sucursales 	= new My_Model_Sucursales();
			$transportistas = new My_Model_Transportistas();
			
			if($this->_idUpdate >-1){
				$dataInfo        = $classObject->getDataComplete($this->_idUpdate);
				$aIncidencias	 = $classObject->getIncidencias($this->_idUpdate);				
				$aRecorrido      = $classObject->getRecorrido($this->_idUpdate);
			}
			
			$this->view->data 		= $dataInfo; 
			$this->view->aRecorrido = $aRecorrido;
			$this->view->aIncidencias= $aIncidencias;
			$this->view->aErrors 	= $this->_aErrors;	
			$this->view->resultOp   = $this->_resultOp;
			$this->view->catId		= $this->_idUpdate;
			$this->view->idToUpdate = $this->_idUpdate;    		
    		
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
		$aContactos	  	 = Array();
		$aDataInc		 = Array();
		if(isset($data['option'])){
			if($validateNumbers->isValid($data['catId'])  && 
				$validateString->isValid($data['option'])){
				
				if($data['option']=='insert'){					
					$data['userRegister']	= $this->view->dataUser['ID_USUARIO'];
					$insert  = $travels->setIncidencia($data);
					if($insert){
						
						$cIndicencias = new My_Model_Incidencias();
						$aDataInc	  = $cIndicencias->getData($data['inputIncidencia']);
						if($aDataInc['PRIORIDAD']==1){
							$infoData   = $travels->getData($data['catId']);														
							$cContactos	  = new My_Model_Contactos();
							$aContactos   = $cContactos->getContactsBy('inc',$data['catId']);							
							$cFunctions->sendNotifications(1,$aContactos,$infoData['CLAVE']);						
						}
						
						$result =true;
					}
				}
			}			
		} 
		
		$this->view->incidencias = $travels->getTipoIncidencias($this->view->dataUser['ID_EMPRESA']);
		$this->view->insert = $result;
		$this->view->catId = $data['catId'];
    }    
    
    public function manualposAction(){
    	try{
    		$this->view->layout()->setLayout('layout_blank');	
    		
	    	$aDataViaje = Array();	    	
			$result 	= false;
			$validateNumbers = new Zend_Validate_Digits();
			$validateString  = new Zend_Validate_Alnum();		
			$travels 		 = new My_Model_Viajes();
			$cFunctions      = new My_Controller_Functions();
			
			if(isset($this->_idUpdate)){
				$aDataViaje = $travels->getDataComplete($this->_idUpdate);
			}
			
			if(isset($this->_dataIn['option'])){
				if($validateNumbers->isValid($this->_dataIn['catId'])  && 
					$validateString->isValid($this->_dataIn['option'])){
					
					if($this->_dataIn['option']=='insert'){					
						$this->_dataIn['userRegister']	= $this->view->dataUser['ID_USUARIO'];
						$insert  = $travels->setManualPosition($this->_dataIn);
						if($insert['status']){
							if(isset($this->_dataIn['inputIncidencia']) && $this->_dataIn['inputIncidencia']!=""){
								$idHistorico 	   = $insert['id'];
								$insertIncidencia  = $travels->setIncidencia($this->_dataIn,$idHistorico);
								if($insertIncidencia){
									
									$cIndicencias = new My_Model_Incidencias();
									$aDataInc	  = $cIndicencias->getData($this->_dataIn['inputIncidencia']);
									
									if($aDataInc['PRIORIDAD']==1){
										$cContactos	  = new My_Model_Contactos();
										$aContactos   = $cContactos->getContactsBy('inc',$this->_dataIn['catId']);							
										$cFunctions->sendNotifications(1,$aContactos,$aDataViaje['CLAVE']);
									}									
									
									$result =true;
								}	
							}else{
								$result =true;
							}
						}
					}
				}			
			} 
			
			$this->view->incidencias = $travels->getTipoIncidencias($this->_dataUser['ID_EMPRESA']);
			$this->view->insert 	 = $result;
			$this->view->catId 		 = $this->_dataIn['catId'];
			$this->view->aDataViaje	 = $aDataViaje;    

		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 		
    }    
}	

