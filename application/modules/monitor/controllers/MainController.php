<?php
class monitor_mainController extends My_Controller_Action
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
	    	$this->view->mOption = 'monitor';
	    	$cViajes 	= new My_Model_Monitor();
			$aNoAssing  = Array();
	    	$aCurrent 	= Array();
	    	
	    	if($this->_dataUser['ID_PERFIL']==1){
				$aNoAssing  = $cViajes->getNoAssing();
	    		$aCurrent 	= $cViajes->getCurrentTravels();	
	    	}	    		

	    	$this->view->aNoAssign  	= $aNoAssing;
	    	$this->view->aAssignNew 	= $cViajes->getAssingUser($this->_dataUser['ID_USUARIO'],1);
	    	$this->view->aAssignCurrent = $cViajes->getAssingUser($this->_dataUser['ID_USUARIO'],2);
	    	$this->view->aAssignFinish  = $cViajes->getAssingUser($this->_dataUser['ID_USUARIO'],4,1);
	    	$this->view->aReassign		= $aCurrent;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  
    }    
    

    public function searchusersAction(){
		try{
			$this->view->layout()->setLayout('layout_blank');
			$sTypeMode  = (isset($this->_dataIn['mode']))? $this->_dataIn['mode'] : 'assign';			
			$cObject    = new My_Model_Adminusuarios();			
			$this->view->dataTable   = $cObject->getUserToAssign();
			
			$this->view->aModeAssign = $sTypeMode;
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }       	
    }    
    
    public function assigntravelAction(){
    	try{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();    	                
	        $answer = Array('answer' => 'no-data');	
	        $cViajes = new My_Model_Monitor();
	        $cFunctions		 = new My_Controller_Functions();

	        if($this->_dataOp=="assignMonitor"){
	        	if(isset($this->_dataIn['userId']) && $this->_dataIn['userId'] && $this->_idUpdate >-1){
	        		$insert = $cViajes->assignTravel($this->_idUpdate,$this->_dataIn['userId']);
	        		if($insert){
	        			$cUsuarios = new My_Model_Adminusuarios();
	        			$dataUser  = $cUsuarios->getData($this->_dataIn['userId']);
						$bodymail   = '<h3>Atencion</h3>'.
										'Se ha asignado un viaje para monitorear.<br/>'.
										'Para revisarlo, debes de ingresar al siguiente link:'.
										'<a href="http://viajes.grupouda.com.mx">Da Click Aqui</a><br/>'.
										'o bien copia y pega en tu navegador el siguiente enlace<br>'.
										'<b> http://viajes.grupouda.com.mx</b>';									
						$aMailer    = Array(
							'emailTo' 	=> $dataUser['USUARIO'],
							'nameTo' 	=> $dataUser['NOMBRE']." ".$dataUser['APELLIDOS'],
							'subjectTo' => ('GTP - Grupo UDA'),
							'bodyTo' 	=> $bodymail,
						);
						$enviar = $cFunctions->sendMailSmtp($aMailer);	        			
	        			
	        			$answer = Array('answer' => 'ok');
	        		}else{
	        			$answer = Array('answer' => 'problem');			
	        		}
	        	}
	        }else if($this->_dataOp=="reassignMonitor"){
	        	if(isset($this->_dataIn['userId']) && $this->_dataIn['userId'] && $this->_idUpdate >-1){
	        		$insert = $cViajes->assignTravel($this->_idUpdate,$this->_dataIn['userId']);
	        		if($insert){
	        			$cUsuarios = new My_Model_Adminusuarios();
	        			$dataUser  = $cUsuarios->getData($this->_dataIn['userId']);
						$bodymail   = '<h3>Atencion</h3>'.
										'Se ha re-asignado un viaje para monitorear.<br/>'.
										'Para revisarlo, debes de ingresar al siguiente link:'.
										'<a href="http://viajes.grupouda.com.mx">Da Click Aqui</a><br/>'.
										'o bien copia y pega en tu navegador el siguiente enlace<br>'.
										'<b> http://viajes.grupouda.com.mx</b>';									
						$aMailer    = Array(
							'emailTo' 	=> $dataUser['USUARIO'],
							'nameTo' 	=> $dataUser['NOMBRE']." ".$dataUser['APELLIDOS'],
							'subjectTo' => ('GTP - Grupo UDA'),
							'bodyTo' 	=> $bodymail,
						);
						$enviar = $cFunctions->sendMailSmtp($aMailer);	        			
	        			
	        			$answer = Array('answer' => 'ok');
	        		}else{
	        			$answer = Array('answer' => 'problem');			
	        		}
	        	}
	        }
	        		
        	echo Zend_Json::encode($answer);  			
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }
}