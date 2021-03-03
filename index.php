<?php

header('Content-type: application/json');

class ConvertXML
{
    private $xmlFile;
    private $elements = [];

    public function __construct($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //Return as string
        $this->xmlFile = curl_exec($curl);
        curl_close($curl);
    }

    public function run()
    {
        $members = $this->convertXMLtoArray($this->xmlFile);

        foreach($members as $member){
            
            $element['firstName'] = $member['first_name'];
            $element['lastName'] = $member['last_name'];
            $element['fullName'] = $member['first_name'].' '.$member['last_name'];
            $element['chartId'] = $member['bioguide_id'];
            $element['mobile'] = $member['phone'];
            $element['address'] = $this->convertAddress($member['address']);

            $this->elements[] = $element;
        }

        return json_encode($this->elements);
    }

    private function convertXMLtoArray($file)
    {
        $xml = new SimpleXMLElement($file);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        return $array['member'];
    }

    private function convertAddress($addr)
    {
        $arr = explode(' ', $addr);
        $addressString = '';

        for($i=0; $i<count($arr)-3; $i++) {
            $addressString .= $arr[$i].' ';
        }
        $addressString = trim($addressString);
        
        $address['street'] = $addressString;
        $address['city'] = $arr[count($arr)-3];
        $address['state'] = $arr[count($arr)-2];
        $address['postal'] =  $arr[count($arr)-1];
        
        return $address;
    }
}


$url = 'https://www.senate.gov/general/contact_information/senators_cfm.xml';
$parser = new ConvertXML($url);
echo $parser->run();