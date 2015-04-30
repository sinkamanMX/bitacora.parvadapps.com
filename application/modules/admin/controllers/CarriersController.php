<?php

class admin_carriersController extends My_Controller_Action
{
	protected $_clase = 'carriers';
	public $validateNumbers;
	public $validateAlpha;
	public $dataIn;
	public $idToUpdate=-1;
	public $errors = Array();
	public $operation='init';
	public $resultop=null;	
		
    public function init()
    {
    	try{
		$sessions = new My_Controller_Auth();
		$perfiles = new My_Model_Perfiles();
        if(!$sessions->validateSession()){
            $this->_redirect('/');		
		}
		$this->view->dataUser   = $sessions->getContentSession();
		$this->view->modules    = $perfiles->getModules($this->view->dataUser['ID_PERFIL']);
		$this->view->moduleInfo = $perfiles->getDataModule($this->_clase);
		$this->view->idEmpresa  = $this->view->dataUser['ID_EMPRESA'];
		
		$this->dataIn = $this->_request->getParams();
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

		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  		
    }
    
    public function indexAction(){
    	try{
	    	$this->view->mOption = 'carriers';
			$classObject = new My_Model_Transportistas(); 
			$this->view->datatTable = $classObject->getRowsEmp($this->view->idEmpresa); //id de empresa
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  
    }
    
    public function getinfoAction(){
		$dataInfo = Array();
		$classObject = new My_Model_Transportistas();
		$functions = new My_Controller_Functions();
		//$transports= new My_Model_Unidades();
		
		if($this->idToUpdate >-1){
			$dataInfo    = $classObject->getData($this->idToUpdate);
		}
		
		if($this->operation=='update'){			
			if($this->idToUpdate>-1){
				 $updated = $classObject->updateRow($this->dataIn,$this->view->idEmpresa,$this->idToUpdate); //mandar el ide del transportista
				 if($updated['status']){
				 	$dataInfo    = $classObject->getData($this->idToUpdate);
				 	$this->resultop = 'okRegister';	
				 	$this->_redirect('/admin/carriers/index');
				 }
			}else{
				$this->errors['status'] = 'no-info';
			}	
		}else if($this->operation=='new'){
			$insert = $classObject->insertRow($this->dataIn,$this->view->idEmpresa);
			if($insert['status']){
				$this->idToUpdate	= $insert['id'];
				$this->resultop = 'okRegister';	
				$dataInfo    = $classObject->getData($this->idToUpdate);
				$this->_redirect('/admin/carriers/index');
			}else{
				$this->errors['status'] = 'no-insert';
			}
		}else if($this->operation=='delete'){
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$answer = Array('answer' => 'no-data');
			    
			$this->dataIn['idEmpresa'] = $this->view->idEmpresa; /*Aqui va la variable que venga de la session*/
			$delete = $classObject->deleteRow($this->dataIn);
			if($delete['status']){
				$answer = Array('answer' => 'deleted'); 
			}	

	        echo Zend_Json::encode($answer);
	        die();   			
		}
		
		$this->view->status     = $functions->cboStatus(@$dataInfo['ACTIVO']);
		//$this->view->transportistas = $transports->getRowsEmp(1);
		$this->view->data 		= $dataInfo; 
		$this->view->error 		= $this->errors;	
    	$this->view->mOption 	= 'carriers';
		$this->view->resultOp   = $this->resultop;
		$this->view->catId		= $this->idToUpdate;
		$this->view->idToUpdate = $this->idToUpdate;		
    }     
}