<?php	
		$curl = curl_init();
		$user = $_POST['user'];
		$password = $_POST['password'];
		$serialNum = $_POST['serialNum'];
		$timeStamp = $_POST['timeStamp'];
		$soapparam = $_POST['soapparam'];

		$postData = file_get_contents('php://input');
		// $UUID =str_replace("}","",str_replace("{","",explode("</UUID>", explode("<UUID>",$postData)[1])[0])); ;
		
	
		$myfile = fopen("/var/www/html/soap/log.txt", "a") or die("Unable to open file!");
		$txt = '
		++++++++++++++++++++++'.date("H:m Y-m-d").'++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		'.$postData;
		fwrite($myfile, $txt);	
		fclose($myfile);


		if(!$soapparam) { // isAlive define if soapparam is null
			if(!$postData){
				$params = '<SOAP-ENV:Envelope
								xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
								xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
								xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
								xmlns:xsd="http://www.w3.org/2001/XMLSchema"
								xmlns:c14n="http://www.w3.org/2001/10/xml-exc-c14n#"
								xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
								xmlns:saml1="urn:oasis:names:tc:SAML:1.0:assertion"
								xmlns:saml2="urn:oasis:names:tc:SAML:2.0:assertion"
								xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd"
								xmlns:xenc="http://www.w3.org/2001/04/xmlenc#"
								xmlns:wsc="http://docs.oasis-open.org/ws-sx/ws-secureconversation/200512"
								xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd"
								xmlns:ns3="http://schemas.datacontract.org/2004/07/System"
								xmlns:ns2="http://schemas.microsoft.com/2003/10/Serialization/"
								xmlns:ns7="http://139.59.165.125/soap/sample.wsdl">
							<SOAP-ENV:Body>
								<ns7:IsAlive>
								<ident>
									<user>'.$user.'</user>
									<password>'.$password.'</password>
									<serialNum>'.$serialNum.'</serialNum>
									<timeStamp>'.$timeStamp.'</timeStamp>
								</ident>
								</ns7:IsAlive>
							</SOAP-ENV:Body>
						</SOAP-ENV:Envelope>';
			} else {
				$params = $postData;
			}
		} else $params = $soapparam;

		$params = str_replace('<?xml version="1.0" encoding="UTF-8"?>',"",$params);


		curl_setopt_array($curl, array(
			CURLOPT_URL => "http://139.59.165.125/soap/soap.php",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $params,
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type:  text/xml;charset=\'utf-8\'",
				"Accept: text/xml",
				"Cache-Control: no-cache",
				"Pragma: no-cache",
				"SOAPAction: http://connecting.website.com/WSDL_Service/GetPrice", 
				"Content-length: ".strlen($params)
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			$arr = array("request"=>$params,'response'=>$response); 
			$axm = array2xml($arr);
			print_r(str_replace('xsi:type="xsd:string"','',str_replace("</item>","",str_replace('<item xsi:type="SOAP-ENC:Struct">',"",$response))));
		}	


		function array2xml($array, $xml = false){
		
		    if($xml === false){
		        $xml = new SimpleXMLElement('<result/>');
		    }
		
		    foreach($array as $key => $value){
		        if(is_array($value)){
		            array2xml($value, $xml->addChild($key));
		        } else {
		            $xml->addChild($key, $value);
		        }
		    }
		
		    return $xml->asXML();
		}



?>