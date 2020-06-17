<?php 
/**
 * MWS Reports API
 * 
 * @category    Amazon
 * @package 	Marketplace Web Services
 * @version     2009-01-01
 * @subpackage 	Reports API
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports {
    public function __construct()
    {
        /* array (
            'Marketplace' => array('FieldValue' => null, 'FieldType' => 'string'),
            'Merchant' => array('FieldValue' => null, 'FieldType' => 'string'),
            'MWSAuthToken' => array('FieldValue' => null, 'FieldType' => 'string'),
            'MaxCount' => array('FieldValue' => null, 'FieldType' => 'string'),
            'ReportTypeList' => array('FieldValue' => null, 'FieldType' => 'MarketplaceWebService_Model_TypeList'),
            'Acknowledged' => array('FieldValue' => null, 'FieldType' => 'bool'),
            'AvailableFromDate' => array('FieldValue' => null, 'FieldType' => 'DateTime'),
            'AvailableToDate' => array('FieldValue' => null, 'FieldType' => 'DateTime'),
            'ReportRequestIdList' => array('FieldValue' => null, 'FieldType' => 'MarketplaceWebService_Model_IdList')
            ); */
    }

    /**
     * Generates request url
     *
     * @param   string  $secret_key         AWS or developer secret key
     * @param   array   $request_param      Request parameter array
     * @return  string                      Request URL    
     */
    private function GenerateRequestURL($secret_key, $request_param)
    {   
        // Generate URL parameter
        $url_param = array(); 

        foreach($request_param as $key => $val) {
            $key   = str_replace("%7E", "~", rawurlencode($key));
            $val   = str_replace("%7E", "~", rawurlencode($val)); 
            $url_param[] = "{$key}={$val}";  
        }

        sort($url_param); 

        $arr = implode('&', $url_param); 

        // Create signature
        $sign  = 'GET' . "\n";
		$sign .= 'mws.amazonservices.com' . "\n";
		$sign .= '/Reports/2009-01-01' . "\n";
        $sign .= $arr;
        
        $signature = hash_hmac("sha256", $sign, $secret_key, true);
		$signature = urlencode(base64_encode($signature));

        // Generate request URL
		$request_url  = "https://mws.amazonservices.com/Reports/2009-01-01?";
		$request_url .= $arr."&Signature=".$signature;

		return $request_url;
    }

    /**
     * Make curl request
     *
     * @param  string   $request_url    Request URL
     * @return text/xml                 Response
     */
    public function CurlRequest($request_url)
    {
        // Header
        $header = array('Content-Type: application/xml; charset=utf-8');
        
        // Curl initialize
        $ch = curl_init($request_url);
        
        // Set curl options
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_PORT, 443);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$curl_response 	= curl_exec($ch);
		
		// Close curl
		curl_close($ch);

		return $curl_response;
    }

    /**
     * Get report list
     *
     * @param [type] $param
     * @return void
     */
    public function GetReportList($sellerid, $mwsauthtoken, $awsaccesskey, $secretkey, $reporttypelist = NULL)
    {   
        
        $param = array(
            'AWSAccessKeyId'        => base64_decode($awsaccesskey), 
            'Action'                => 'GetReportList', 
            'MWSAuthToken'          => base64_decode($mwsauthtoken), 
            'Merchant'              => base64_decode($sellerid), 
            'SignatureMethod'       => 'HmacSHA256', 
            'SignatureVersion'      => '2', 
            'Timestamp'             => date("c", time()), 
            'Version'               => '2009-01-01'
        ); 

        if(isset($reporttypelist)) 
        {   
            if(is_array($reporttypelist)) 
            {
                $n = 1; 

                foreach($reporttypelist as $report_type)
                {
                    $param['ReportTypeList.Type.'.$n] = $report_type;

                    $n++; 
                }
            }
            else $param['ReportTypeList.Type.1'] = $reporttypelist;
        }

        // Get request url
        $request_url = $this->GenerateRequestURL(base64_decode($secretkey), $param); 

        // Make curl request
        return $this->CurlRequest($request_url);
    }
}
?>