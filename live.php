<?php
    $soapclient = new SoapClient("sample.wsdl",array("trace"=>1));   
    $user = $_POST['user'];
    $password = $_POST['password'];
    $serialNum = $_POST['serialNum'];
    $timeStamp = $_POST['timeStamp'];

    $ident = array(	 
        'user' => $user,
        'password' => $password,
        'serialNum' => $serialNum,
        'timeStamp' => $timeStamp	
		
    );



    $getresponse = $soapclient->IsAlive($ident);		
   

   
    $xml = $soapclient->__getLastRequest();

    


    $arr = array("request"=>$ident,'response'=>$getresponse);
    $axm = array2xml($arr);
    print_r($axm);


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