<?php 
/**
 * MWS Finances API
 * 
 * @category    Amazon
 * @package 	Marketplace Web Services
 * @version     2009-01-01
 * @subpackage 	Reports API
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Finances {
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
		$sign .= '/Finances/2015-05-01' . "\n";
        $sign .= $arr;
        
        $signature = hash_hmac("sha256", $sign, base64_decode($secret_key), true);
		$signature = urlencode(base64_encode($signature));

        // Generate request URL
		$request_url  = "https://mws.amazonservices.com/Finances/2015-05-01?";
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
     * Undocumented function
     *
     * @param [type] $sellerid
     * @param [type] $mwsauthtoken
     * @param [type] $awsaccesskey
     * @param [type] $secretkey
     * @param [type] $dateafter
     * @param [type] $datebefore
     * @param [type] $maxresult
     * @return void
     */
    public function ListFinancialEventGroups($sellerid, $mwsauthtoken, $awsaccesskey, $secretkey, $dateafter, $datebefore = NULL, $maxresult = NULL)
    {   
        $param = array(
            'AWSAccessKeyId'                  => base64_decode($awsaccesskey), 
            'Action'                          => 'ListFinancialEventGroups', 
            'MWSAuthToken'                    => base64_decode($mwsauthtoken), 
            'SellerId'                        => base64_decode($sellerid), 
            'SignatureMethod'                 => 'HmacSHA256', 
            'SignatureVersion'                => '2', 
            'Timestamp'                       => date("c", time()), 
            'Version'                         => '2015-05-01', 
            'FinancialEventGroupStartedAfter' => date("c", strtotime($dateafter))
        ); 

        // Optional parameters
        if(isset($datebefore)) $param['FinancialEventGroupStartedBefore'] = $datebefore;
        if(isset($maxresult)) $param['MaxResultsPerPage'] = $maxresult;
        //strtotime('today - 30 days')

        // Get request url
        $request_url = $this->GenerateRequestURL($secretkey, $param); 

        // Make curl request
        return $this->CurlRequest($request_url);
    }

    /**
     * Undocumented function
     *
     * @param [type] $sellerid
     * @param [type] $mwsauthtoken
     * @param [type] $awsaccesskey
     * @param [type] $secretkey
     * @param [type] $postedafter
     * @param [type] $postedbefore
     * @param [type] $financialgroupid
     * @param [type] $amazonorderid
     * @param [type] $maxresult
     * @return void
     */
    public function ListFinancialEvents($sellerid, $mwsauthtoken, $awsaccesskey, $secretkey, $postedafter = NULL, $postedbefore = NULL, $financialeventgroupid = NULL, $amazonorderid = NULL, $maxresultperpage = NULL)
    {
        $param = array(
            'AWSAccessKeyId'                  => base64_decode($awsaccesskey), 
            'Action'                          => 'ListFinancialEvents', 
            'MWSAuthToken'                    => base64_decode($mwsauthtoken), 
            'SellerId'                        => base64_decode($sellerid), 
            'SignatureMethod'                 => 'HmacSHA256', 
            'SignatureVersion'                => '2', 
            'Timestamp'                       => date("c", time()), 
            'Version'                         => '2015-05-01'
        ); 

        // Optional parameters
        if(isset($postedafter)) $param['PostedAfter'] = $postedafter;
        if(isset($postedbefore)) $param['PostedBefore'] = $postedbefore;
        if(isset($financialeventgroupid)) $param['FinancialEventGroupId'] = $financialeventgroupid;
        if(isset($amazonorderid)) $param['AmazonOrderId'] = $amazonorderid;
        if(isset($maxresultperpage)) $param['MaxResultsPerPage'] = $maxresultperpage;

        // Get request url
        $request_url = $this->GenerateRequestURL($secretkey, $param); 

        // Make curl request
        return $this->CurlRequest($request_url);
        //return $request_url; 
    }
}