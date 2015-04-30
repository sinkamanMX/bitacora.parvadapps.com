<?php

class main_RecoverController extends My_Controller_Action
{	
    public function init()
    {
		$this->view->layout()->setLayout('layout_blank');
		
		$sessions = new My_Controller_Auth();
        if($sessions->validateSession()){
	        $this->view->dataUser   = $sessions->getContentSession();   		
		}
    }
    
    public function indexAction()
    {
		try{
			$sessions = new My_Controller_Auth();
	        if($sessions->validateSession()){
	            $this->_redirect('/main/main/inicio');		
			}
			
	    	$cUsuarios  = new My_Model_Usuarios();
			$cFunctions = new My_Controller_Functions();
	    	$optionTodo = 'remember';
	    	$sResult	= '';
	    	$aError     = Array();	    	
	    	$dataIn     = $this->_request->getParams();
	    	header('Content-Type: text/html; charset=utf-8');
	    	if(isset($dataIn['optReg'])     && $dataIn['optReg']  !="remember" &&
	    	   isset($dataIn['inputUser']) && $dataIn['inputUser']!="" ){				
				$optionTodo = '';
				
				$validateUsuarios = $cUsuarios->userExist($dataIn['inputUser']);
				if(count($validateUsuarios)>0 && $validateUsuarios['USUARIO']==$dataIn['inputUser']){
					$aDatauser  = $cUsuarios->getDataUser($validateUsuarios['ID_USUARIO']);
					$sNameuser  = $aDatauser['N_USUARIO'].' '.$aDatauser['APELLIDOS'];
					$randomReset= $cFunctions->getRandomCodeReset();
					
					$aDataKey   = Array(
						'idUser' 	 => $aDatauser['ID_USUARIO'],
						'inputClave' => $randomReset,
					);
					$setKey   	= $cUsuarios->setKeyRestore($aDataKey);
					if($setKey){						
						$bodymail   = '<h3>Estimado '.$sNameuser.':</h3>'.
									  'Has solicitado recuperar tu contrase&ntilde;a <br/>'.
									  'Para hacerlo, debes de ingresar al siguiente link:'.
									  '<a href="http://viajes.grupouda.com.mx/main/recover/recoverresq?keyResetToker='.$randomReset.'">Da Click Aqui</a><br/>'.
									  'o bien copia y pega en tu navegador el siguiente enlace<br>'.
									  '<b> http://viajes.grupouda.com.mx/main/recover/recoverresq?keyResetToker='.$randomReset.'</b>';
						$aMailer    = Array(
							'emailTo' 	=> $aDatauser['USUARIO'],
							'nameTo' 	=> $sNameuser,
							'subjectTo' => ('Viajes UDA - Recuperaci&oacute;n de Contrase&ntilde;a'),
							'bodyTo' 	=> $bodymail,
						);											
					 	$enviar = $cFunctions->sendMailSmtp($aMailer);
					   	$sResult= 'okRegister';						
					}									
				}else{
					$aError['eUsuario'] = 1;
				}
	    	}
	    	
	    	$this->view->optRegister = $optionTodo;
	    	$this->view->aErrors 	 = $aError;
	    	$this->view->resultOp    = $sResult;

        } catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }
    }
    
    
    public function recoverresqAction(){
    	try{	    	
	    	$cUsuarios  = new My_Model_Usuarios();
			$cFunctions = new My_Controller_Functions();
	    	$optionTodo = 'remember';
	    	$sResult	= '';
	    	$aError     = Array();	    	
	    	$dataIn     = $this->_request->getParams();
	    	$aDataUser  = Array();
	    	$catId		= 0;
	    	
	    	header('Content-Type: text/html; charset=utf-8');
	    	
	    	if(isset($dataIn['keyResetToker']) && $dataIn['keyResetToker']!="" ){
	    		$keyRestore = $dataIn['keyResetToker'];
	    		$aDataUser  = $cUsuarios->validateKeyRecovery($keyRestore);
	    		
	    		if(count($aDataUser)==0){
	    			$aError['errorConfig'] = 1;	    				    	
	    		}else{
	    			$catId = $aDataUser['ID_USUARIO'];	
	    			
		    		if(isset($dataIn['optReg']) && $dataIn['optReg']=='reset'){
		    			$update = $cUsuarios->updatePassword($dataIn);
		    			if($update){	    				
							$aDataKey   = Array(
								'idUser' 	 => $dataIn['idReset'],
								'inputClave' => '',
							);
							$setKey  = $cUsuarios->setKeyRestore($aDataKey);
							$sResult = 'updated';	    				
		    				//$this->_redirect("/login/main/index");	
		    			}else{
		    				$aError['errorConfig'] = 1;	
		    			}
		    		}	    			
	    		}	    		
	    	}else{
	    		$this->_redirect("/main/main/index");	
	    	}
	    	
	    	$this->view->aUserData = $aDataUser; 
	    	$this->view->catId	   = $catId;
	    	$this->view->dataIn	   = $dataIn;
	    	$this->view->result    = $sResult;
	    	$this->view->aErrors   = $aError;
    	}catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }    
}