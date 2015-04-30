<?php

class admin_UnitsController extends My_Controller_Action
{
	protected $_clase = 'units';
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
	    	$this->view->mOption = 'units';
			$classObject = new My_Model_Unidades(); 
			$this->view->datatTable = $classObject->getUnidades($this->view->dataUser['ID_EMPRESA']);
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  
    }
    
    public function getinfoAction(){
    	try{
	    	$sProveedor = '';
			$dataInfo = Array();
			$classObject = new My_Model_Unidades();
			$functions = new My_Controller_Functions();
			$transports= new My_Model_Transportistas();
			$cProveedores = new My_Model_Proveedores();
			$aProveedores = $cProveedores->getCbo($this->view->dataUser['ID_EMPRESA']);
			
			if($this->idToUpdate >-1){
				$dataInfo    = $classObject->getData($this->idToUpdate);
				$sProveedor  = $dataInfo['ID_PROVEEDOR'];
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
				$this->dataIn['idEmpresa'] = $this->view->dataUser['ID_EMPRESA'];
				$insert = $classObject->insertRow($this->dataIn);
				if($insert['status']){
					$this->idToUpdate	= $insert['id'];
					$this->resultop = 'okRegister';	
					$dataInfo    = $classObject->getData($this->idToUpdate);
					$this->_redirect('/admin/units/index');
				}else{
					$this->errors['status'] = 'no-insert';
				}
			}else if($this->operation=='delete'){
				$this->_helper->layout->disableLayout();
				$this->_helper->viewRenderer->setNoRender();
				$answer = Array('answer' => 'no-data');
				    
				$this->dataIn['idEmpresa'] =$this->view->dataUser['ID_EMPRESA']; /*Aqui va la variable que venga de la session*/
				$delete = $classObject->deleteRow($this->dataIn);
				if($delete['status']){
					$answer = Array('answer' => 'deleted'); 
				}	
	
		        echo Zend_Json::encode($answer);
		        die();   			
			}
			
			$this->view->status     = $functions->cboStatus(@$dataInfo['ACTIVO']);
			$this->view->transportistas = $transports->getRowsEmp($this->view->dataUser['ID_EMPRESA']);
			$this->view->aProveedores= $functions->selectDb($aProveedores,$sProveedor);
			$this->view->data 		= $dataInfo; 
			$this->view->error 		= $this->errors;	
	    	$this->view->mOption 	= 'units';
			$this->view->resultOp   = $this->resultop;
			$this->view->catId		= $this->idToUpdate;
			$this->view->idToUpdate = $this->idToUpdate;	

		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 		
    }   

    public function migrateAction(){
    	try{
    		$cUnidades 	 = new My_Model_Unidades();
    		$cTransports = new My_Model_Transportistas();
    		if($this->view->dataUser['CLIENTE_UDA']==1){
    			$idTrans = $cTransports->getFirst($this->view->dataUser['ID_EMPRESA']);    			
    			if(count($idTrans)>0 && isset($idTrans['ID_TRANSPORTISTA'])){
	    			$userUda = $this->view->dataUser['USUARIO_UDA'];
	    			$passUda = $this->view->dataUser['PASSWORD_UDA'];

	    		  	//$soap_client  = new SoapClient("http://201.131.96.40/ws/wsUDAHistoryGetByPlate.asmx?WSDL");
	    		  	$soap_client  = new SoapClient("http://192.168.6.41/ws/wsUDAHistoryGetByPlate.asmx?WSDL");
					$aParams 	  = array('sLogin'     => $userUda,
				                  		  'sPassword'  => $passUda);
					
					$result=$soap_client->HistoyDataLastLocationByUser($aParams);
					if (is_object($result)){
				       	$x = get_object_vars($result);
						$y = get_object_vars($x['HistoyDataLastLocationByUserResult']);
				
						$xml = $y['any'];		
						if($xml2 = simplexml_load_string($xml)){
							$bContinue = true;						
							if($xml2->Response->Status->code=='101'){
								$answer = Array('answer' => 'login');
								$bContinue = false;								
							}	
							
							$c = 0;		
							if($bContinue){								
								for($i = 0 ; $i < count($xml2->Response->Plate) ; $i++){
					          		$sImei    	= (string) $xml2->Response->Plate[$i]['id'];
					          		$sEconomico = (string) $xml2->Response->Plate[$i]->hst->Alias;
					          		$sIp 		= (string) $xml2->Response->Plate[$i]->hst->IP;
					          		
					          		$validateUnit = $cUnidades->validateUnitByPlaque($sEconomico);
					          		if(!$validateUnit){
					          			$aDataInsertUnit['inputTransportista'] = $idTrans['ID_TRANSPORTISTA'];
					          			$aDataInsertUnit['inputProveedor']     = 1;
					          			$aDataInsertUnit['inputEco'] 		   = $sEconomico;
					          			$aDataInsertUnit['inputPlacas']   	   = $sEconomico;
					          			$aDataInsertUnit['inputIden']  		   = $sImei;
					          			$aDataInsertUnit['inputStatus']  	   = 1;
					          			$aDataInsertUnit['idEmpresa'] 		   = $this->view->dataUser['ID_EMPRESA'];
					          			
					          			$insertunit = $cUnidades->insertRow($aDataInsertUnit);
					          			if(!$insertunit){
					          				$errors[$c] = $sImei;
					          			}
					          		}
					
					          		$c = $c+1;
					        	}								
							}
				        	
				        	if($c >0){
				        		$this->resultop = 'okRegister';
				        	}elseif($c=0 && $bContinue){
				        		$this->errors['no-units'] = 1;
				        	}else if(!$bContinue){
				        		$this->errors['login'] = 1;
				        	}
						}else{
							$this->errors['no-info'] = 1;
						}
					}else{
						$this->errors['no-service'] = 1;
					}      				
    			}else{
    				$this->errors['no-transportista'] = 1;
    			}  			
    		}else{
    			$this->_redirect('/admin/units/index');	
    		}
    		
    		$this->view->bPageAll = true;
    		$this->view->resultOp = $this->resultop;
    		$this->view->errors	  = $this->errors;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 	
    }
}