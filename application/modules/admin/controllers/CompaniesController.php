<?php
class admin_CompaniesController extends My_Controller_Action
{		
	protected $_clase = 'mcompanies';	
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

		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        } 		
    }
    
    public function indexAction(){
    	try{
	    	$this->view->mOption = 'mcompanies';			
			$cRutas      = new My_Model_Empresas();
			
			$this->view->datatTable = $cRutas->getDataTables();
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  
    }  
    
    public function getinfoAction(){
    	try{
			$cEmpresas 		 = new My_Model_Empresas();
			$cSucursales	 = new My_Model_Sucursales();
			$cUsuarios  	 = new My_Model_Usuarios();
			$cTransportistas = new My_Model_Transportistas();
			$cFunctions		 = new My_Controller_Functions();
			$sEstatus		 = '';
			$sClientUda		 = 0;
			$sCViajes 		 = 1;
			$aDataInfo		 = Array();
			
			$this->_dataIn['inputEmpresa'] = $this->view->idEmpresa;			
			if($this->_idUpdate >-1){
				$aDataInfo  = $cEmpresas->getData($this->_idUpdate);
				$sEstatus	= $aDataInfo['ESTATUS'];
				$sClientUda = $aDataInfo['CLIENTE_UDA'];
				$sCViajes 	= $aDataInfo['COBRAR_VIAJES'];
			}
			
    		if($this->_dataOp=="new"){
				$validateEmp  = $cEmpresas->validateExist($this->_dataIn['inputRFC']);
				if(count($validateEmp)==0){
					$validateUser = $cUsuarios->userExist($this->_dataIn['inputUser']);
					if(count($validateUser)==0){
						$insertEmpresa = $cEmpresas->insertRow($this->_dataIn);
						if($insertEmpresa['status']){
							$idEmpresa = $insertEmpresa['id'];
							$this->_dataIn['inputIdEmpresa']   = $idEmpresa;
							$this->_dataIn['inputStatus']      = 1;
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
										'subjectTo' => ('Viajes - Grupo UDA'),
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
			}elseif($this->_dataOp=="update"){
				if($this->_idUpdate>-1){
					 $updated = $cEmpresas->updateRow($this->_dataIn,$this->_idUpdate); //mandar el ide del transportista
					 if($updated['status']){
					 	$aDataInfo    = $cEmpresas->getData($this->_idUpdate);
					 	$this->_resultOp = 'okRegister';	
					 	$this->_redirect('/admin/companies/index');
					 }
				}else{
					$this->errors['status'] = 'no-info';
				}					
			}			
			    		
	    	$this->view->errors 	 = $this->_aErrors;
	    	$this->view->resultOp    = $this->_resultOp;	    	
	    	$this->view->bUsuarioUda = $cFunctions->cboOptions($sClientUda);
	    	$this->view->aCviajes    = $cFunctions->cboOptions($sCViajes);	
	    	$this->view->status      = $cFunctions->cboStatus($sEstatus);	
	    	$this->view->data	     = $aDataInfo;   
			$this->view->catId		 = $this->_idUpdate;
			$this->view->idToUpdate  = $this->_idUpdate;
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }  
    }

}