<?php
class register_MainController extends My_Controller_Action
{	
	public $validateNumbers;
	public $validateAlpha;
		
    public function init()
    {
		$this->view->layout()->setLayout('layout_blank');
	
		$this->_dataIn = $this->_request->getParams(); 
		$this->validateNumbers = new Zend_Validate_Digits();

		
		if(isset($this->_dataIn['optReg'])){
			$this->_dataOp = $this->_dataIn['optReg'];
			
			/*
			if($this->_dataOp=='update'){
				$this->_dataOp = $this->_dataIn['optReg'];

				$this->validateAlpha   = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));				
			}*/	
		}
		
		$sessions = new My_Controller_Auth();
        if($sessions->validateSession()){
	        $this->view->dataUser   = $sessions->getContentSession();   		
		}
    }
    
    public function indexAction()
    {
		try{
			$cEmpresas 		 = new My_Model_Empresas();
			$cSucursales	 = new My_Model_Sucursales();
			$cUsuarios  	 = new My_Model_Usuarios();
			$cTransportistas = new My_Model_Transportistas();
			$cFunctions		 = new My_Controller_Functions();
						
			$sessions 	= new My_Controller_Auth();
	        if($sessions->validateSession()){
	            $this->_redirect('/main/main/inicio');		
			}
			
			if($this->_dataOp=="register"){
				$validateEmp  = $cEmpresas->validateExist($this->_dataIn['inputRFC']);
				if(count($validateEmp)==0){
					$validateUser = $cUsuarios->userExist($this->_dataIn['inputUser']);
					if(count($validateUser)==0){
						$this->_dataIn['inputEstatus'] = 0;
						$insertEmpresa = $cEmpresas->insertRow($this->_dataIn);
						if($insertEmpresa['status']){
							$idEmpresa = $insertEmpresa['id'];
							$this->_dataIn['inputIdEmpresa']   = $idEmpresa;
							$this->_dataIn['inputStatus']      = 0;
							$this->_dataIn['inputDescripcion'] = 'Sucursal '.$this->_dataIn['inputDescripcion'];							
							
							$insertSucursal = $cSucursales->insertRowRegister($this->_dataIn);							
							if($insertSucursal['status']){
								$idSucursal 		= $insertSucursal['id'];
								$this->_dataIn['codeActivation'] = $cFunctions->getRandomCodeReset(); 
								$this->_dataIn['inputPerfil']    = 2;
								$insertiUser = $cUsuarios->insertRowRegister($this->_dataIn);
								if($insertiUser['status']){
									$idUsuario = $insertiUser['id'];
									$this->_dataIn['inputIdUsuario'] = $idUsuario;
									$this->_dataIn['inputSucursal']  = $idSucursal;
									
									$insertRel = $cUsuarios->setSucursal($this->_dataIn);
									$bodymail   = '<h3>Estimado '.$this->_dataIn['inputName'].' '.$this->_dataIn['inputApps'].':</h3>'.
												  'Para empezar a utilizar su cuenta, es necesario confirmar su correo electronico<br/>'.
												  'Para hacerlo, debes de ingresar al siguiente link:'.
												  '<a href="http://viajes.grupouda.com.mx/register/main/activation?keyACode='.$this->_dataIn['codeActivation'].'">Da Click Aqui</a><br/>'.
												  'o bien copia y pega en tu navegador el siguiente enlace<br>'.
												  '<b> http://viajes.grupouda.com.mx/register/main/activation?keyACode='.$this->_dataIn['codeActivation'].'</b>';									
									$aMailer    = Array(
										'emailTo' 	=> $this->_dataIn['inputUser'],
										'nameTo' 	=> $this->_dataIn['inputName'].' '.$this->_dataIn['inputApps'],
										'subjectTo' => ('GTP - Grupo UDA'),
										'bodyTo' 	=> $bodymail,
									);					
									
								 	$enviar = $cFunctions->sendMailSmtp($aMailer);																		
									$this->_resultOp = 'okRegister';
								}else{
									$this->_aErrors['status'] = 1;	
								}						
							}else{
								$this->_aErrors['status'] = 1;	
							}
						}else{
							$this->_aErrors['status'] = 1;	
						}						
					}else{
						$this->_aErrors['eUsuario'] = 1;	
					}
				}else{
					$this->_aErrors['eEmpresa'] = 1;
				}
			}

	    	$this->view->errors 	 = $this->_aErrors;
	    	$this->view->resultOp    = $this->_resultOp;
	    	$this->view->bUsuarioUda = $cFunctions->cboOptions(0);
        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    public function activationAction(){
    	try{    		    		
	    	$cUsuarios  = new My_Model_Usuarios();
			$cFunctions = new My_Controller_Functions();
			$cEmpresas  = new My_Model_Empresas();

	    	header('Content-Type: text/html; charset=utf-8');	    	
	    	if(isset($this->_dataIn['keyACode']) && $this->_dataIn['keyACode']!="" ){
	    		$keyActivation 	= $this->_dataIn['keyACode'];
	    		$aDataUser  	= $cUsuarios->validateKeyActivation($keyActivation);
	    		
	    		if(count($aDataUser)==0){
	    			$this->_aErrors['errorConfig'] = 1;	    				    	
	    		}else{
	    			$idUser   = $aDataUser['ID_USUARIO'];	
	    			$iEmpresa = $aDataUser['ID_EMPRESA'];
					$bActive  = $cUsuarios->setActivateUser($idUser);
					if($bActive){
						$bActiveEmp = $cEmpresas->setActivate($iEmpresa);
						if($bActiveEmp){
							$this->_resultOp = 'okRegister';
						}else{
							$this->_aErrors['status'] = 1;
						}
					}else{
						$this->_aErrors['status'] = 1;
					}
	    		}	    		
	    	}else{
	    		$this->_redirect("/main/main/index");	
	    	}
	    	
	    	$this->view->dataIn	   = $this->_dataIn;
	    	$this->view->resultOp  = $this->_resultOp;
	    	$this->view->errors   = $this->_aErrors;
    	}catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }   
}