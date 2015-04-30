<?php
/**
 * Archivo de definición de clase 
 * 
 * @package library.My.Controller
 * @author andres
 */

/**
 * Definición de clase de controlador genérico
 *
 * @package library.My.Controller
 * @author andres
 */
class My_Controller_Functions
{
    public $aMont=array(
        '',
        'Enero',
        'Febrero',
        'Marzo',
        'Abril',
        'Mayo',
        'Junio',
        'Julio',
        'Agosto',
        'Septiembre',
        'Octubre',
        'Noviembre',
        'Diciembre'
        );
        
    public $optionStatus = Array(
		array("id"=>"1",'name'=>'Activo' ),
		array("id"=>"0",'name'=>'Inactivo' )    
    );  
    
    public $aOptions = Array(
		array("id"=>"1",'name'=>'Si' ),
		array("id"=>"0",'name'=>'No' )    
    );        

	public $configMail = array(
            'port'     => '587',
            'auth' 	   => 'login',
			'urlSmtp'  => 'smtp.tecnologiza.me',
			'username' => 'no-reply@tecnologiza.me',			
		    'password' => 'nOr3plym41l3r_');
	    
    public function dateToText($fecha_db){
    	$fecha=explode("-",$fecha_db);
    	$mes_digito= (int) $fecha[1];
    	$fecha_texto=date("d",strtotime($fecha_db))." de $aMont[$mes_digito], ".date("Y ",strtotime($fecha_db))."";
    
    	//Si la fecha tiene horas y minutos
    	if (date("H",strtotime($fecha_db))!="00")
    		$fecha_texto.=" ".date("H:i",strtotime($fecha_db))." hrs.";
    
    	return $fecha_texto;
    }
    
    public function sendMail($data,$config){	
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\n";
		$headers .= "X-Priority: 3\n";
		$headers .= "X-Mailer: PHP 5.2\n";
		$headers .= "From: \"".$config->getOption("admin_nombre")."\" <".$config->getOption("admin_email_noreply").">\n";
		$headers .= "Reply-To:".$config->getOption("mailCco")."\n";
		$enviado = mail($data['mail_admin'], $data['subject'], $data['mensaje'], $headers);
		return $enviado;    	
    }
    
    public function cboStatus($option=''){
		$options='';
		for($p=0;$p<count($this->optionStatus);$p++){
			$select='';
			if($this->optionStatus[$p]['id']==@$option){$select='selected';}
			$options .= '<option '.$select.' value="'.$this->optionStatus[$p]['id'].'" >'.$this->optionStatus[$p]['name'].'</option>';
		}
		return $options;
    }
    
	public function cbo_from_array($array,$option=''){
		$options='';
		for($p=0;$p<count($array);$p++){
			$select='';
			if($array[$p][id]==@$option){$select='selected';}
			$options .= '<option '.$select.' value="'.$array[$p]['id'].'" >'.$array[$p]['name'].'</option>';
		}
		return $options;		
	}

	public function cbo_number($n,$option='',$bDigitComp=true){
	  $select='';
	  for($i=0; $i<$n; $i++){
		  $h = ($i<=9 && $bDigitComp)?"0".$i:$i;
		  $current = ($h==$option) ? 'selected': '';
		  $select .= '<option '.$current.' value="'.$h.'" >'.$h.'</option>';
		  }
	  return $select;  		    
	}
	
	public function selectDb($dataTable,$option=''){	
		$result='';	
		if(count($dataTable)>0){
			foreach($dataTable as $key => $items){
				$select='';
				if($items['ID'] == @$option){$select='selected';}
				$result .= '<option '.$select.' value="'.$items['ID'].'" >'.$items['NAME'].'</option>';			
			}
		}else{
			$result='no-info';
		}
		return $result;			
	}
	
	public function creationClass($nameClass){
		switch($nameClass) {
		   case "clients":
		       return new My_Model_Clientes();
		   case "units":
		       return new My_Model_Unidades();
		   case "operators":
		       return new My_Model_Operadores();		       
		}		
	}
	
	public function arrayToStringDb($dataTable){
		$result='';
		foreach($dataTable as $key => $items){
			if($items['ID']!="NULL"){
				$result .= ($result!='')? ',':'' ;			
				$result .= $items['ID'];	
			}			
		}
		return $result;
	}
	
    public function cboOptions($option=''){
		$options='';
		for($p=0;$p<count($this->aOptions);$p++){
			$select='';
			if($this->aOptions[$p]['id']==@$option){$select='selected';}
			$options .= '<option '.$select.' value="'.$this->aOptions[$p]['id'].'" >'.$this->aOptions[$p]['name'].'</option>';
		}
		return $options;
    }

	function getRandomCodeReset(){
	    $an = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZqazwsxedcrfvtgbyhnujmikolp";
	    $su = strlen($an) - 1;
	    return  substr($an, rand(0, $su), 1) .
	            substr($an, rand(0, $su), 1) .
	            substr($an, rand(0, $su), 1) .
	            substr($an, rand(0, $su), 1) .
	            substr($an, rand(0, $su), 1) .
	            substr($an, rand(0, $su), 1) .
	            substr($an, rand(0, $su), 1) .
	            substr($an, rand(0, $su), 1) .
	            substr($an, rand(0, $su), 1);
	} 

	function sendMailSmtp($aMailer){
    	try{  			
			//$sTransport = new Zend_Mail_Transport_Sendmail($this->configMail['urlSmtp'], $this->configMail);
			$sTransport = new Zend_Mail_Transport_Smtp($this->configMail['urlSmtp'], $this->configMail);
			$mail = new Zend_Mail('UTF-8');
			$mail->addHeader('Content-Type', 'text/plain; charset=utf-8');
	
			$mail->setFrom('contacto@grupouda.com.mx', 'Viajes Grupo UDA');
			$mail->addTo($aMailer['emailTo'], $aMailer['nameTo']);	
			$mail->setSubject(html_entity_decode($aMailer['subjectTo']));
			$mail->setBodyHtml(html_entity_decode($aMailer['bodyTo']));
			$enviado = $mail->send($sTransport);		
		}catch(Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";
        } 			
	}	
	
	function sendMailAdmins($sSubject,$sBody){
    	try{  		
			$config     = Zend_Controller_Front::getInstance()->getParam('bootstrap');
			$aDataAdmin = $config->getOption('admin');	
				
			$sTransport = new Zend_Mail_Transport_Smtp($this->configMail['urlSmtp'], $this->configMail);
			$mail = new Zend_Mail('UTF-8');
			$mail->addHeader('Content-Type', 'text/plain; charset=utf-8');
			$mail->setFrom('contacto@grupouda.com.mx', 'Viajes Grupo UDA');
			
			$aDestinos = explode(",",$aDataAdmin['mails']);
			for($i=0;$i<count($aDestinos);$i++){
				$mail->addTo($aDestinos[$i], $aDataAdmin['name']);	
			}
			
			$mail->setSubject(html_entity_decode($sSubject));
			$mail->setBodyHtml(html_entity_decode($sBody));
			$enviado = $mail->send($sTransport);
		}catch(Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";
        } 			
	}
	
	/**
	 * 
	 * $option 1= Incidencia , 2= Inicio Viaje, 3=Fin de Viaje
	 * 
	 */
	function sendNotifications($option=1,$aDataMails,$codeViaje){
    	try{    			
			$sTransport = new Zend_Mail_Transport_Smtp($this->configMail['urlSmtp'], $this->configMail);
			$mail = new Zend_Mail('UTF-8');
			$mail->addHeader('Content-Type', 'text/plain; charset=utf-8');
			$mail->setFrom('contacto@grupouda.com.mx', 'Viajes Grupo UDA');
			
			if(count($aDataMails)>0){
				foreach($aDataMails as $key => $items){
					$mail->addTo($items['CORREO'],$items['NOMBRE']);	
				}
				switch ($option) {
				    case 1:
				        $sSubject = 'Registro de Incidencia en Viaje';
				        $sBody 	  = '<h3>Atencion</h3>'.
				        			'Se ha registrado una incidencia durante el trayecto del viaje con clave <b>'.$codeViaje.'</b><br/>';
				        break;
				    case 2:
				        $sSubject = 'Inicio de Viaje';
				        $sBody 	  = '<h3>Atencion</h3>'.
				        			'Ha iniciado el viaje con clave <b>'.$codeViaje.'</b>';
				        break;
				    case 3:
				        $sSubject = 'Fin de Viaje';
				        $sBody 	  = '<h3>Atencion</h3>'.
				        			'Ha terminado el viaje con clave <b>'.$codeViaje.'</b>';
				        break;
				}
				
				$mail->setSubject(html_entity_decode($sSubject));
				$mail->setBodyHtml(html_entity_decode($sBody));
				$enviado = $mail->send($sTransport);							
			}	
		}catch(Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";
        } 			
	}	
}