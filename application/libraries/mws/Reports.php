<?php 
/**
 * MWS Reports API
 * 
 * http://docs.developer.amazonservices.com/en_IN/reports/Reports_Overview.html 
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
     * Request a report
     * 
     * http://docs.developer.amazonservices.com/en_IN/reports/Reports_RequestReport.html
     *
     * @param   string      $sellerid             SellerId
     * @param   string      $mwsauthtoken         MWSAuthToken
     * @param   string      $awsaccesskey         AWSAccessKeyId
     * @param   string      $secretkey            Secret Key
     * @param   string      $reporttype           ReportType
     * @param   array       $mpidlist           
     * @param   date        $startdate
     * @param   date        $enddate
     * @param   string      $reportopt
     * @return  object      Return XML response
     */
    public function RequestReport($sellerid, $mwsauthtoken, $awsaccesskey, $secretkey, $reporttype, $mpidlist = null, $startdate = null, $enddate = null, $reportopt = null)
    {
        $param = array(
            'AWSAccessKeyId'        => $awsaccesskey, 
            'Action'                => 'RequestReport', 
            'MWSAuthToken'          => $mwsauthtoken, 
            'Merchant'              => $sellerid, 
            'SignatureMethod'       => 'HmacSHA256', 
            'SignatureVersion'      => '2', 
            'Timestamp'             => date("c", time()), 
            'Version'               => '2009-01-01', 
            'ReportType'            => $reporttype
        ); 

        // Optional parameters
        if(isset($startdate)) $param['StartDate']     = $startdate;
        if(isset($enddate)) $param['EndDate']         = $enddate;
        if(isset($reportopt)) $param['ReportOptions'] = $reportopt;

        // Marketplace id list array parameter
        if(isset($mpidlist)) 
        {   
            if(is_array($mpidlist)) 
            {
                $n = 1; 

                foreach($mpidlist as $mp_id)
                {
                    $param['MarketplaceIdList.Id.'.$n] = $mp_id;

                    $n++; 
                }
            }
            else $param['MarketplaceIdList.Id.1'] = $mpidlist;
        }

        // Get request url
        $request_url = $this->GenerateRequestURL($secretkey, $param); 

        // Make curl request
        return $this->CurlRequest($request_url);
    }

    /**
     * Get report request list 
     * 
     * http://docs.developer.amazonservices.com/en_IN/reports/Reports_GetReportRequestList.html
     *
     * @param   string    $sellerid             SellerId
     * @param   string    $mwsauthtoken         MWSAuthToken
     * @param   string    $awsaccesskey         AWSAccessKeyId
     * @param   string    $secretkey            Secret Key
     * @param   integer   $maxcount             Optional - MaxCount
     * @param   date      $reqdtfm              Optional - RequestedFromDate
     * @param   date      $reqdtto              Optional - RequestedToDate
     * @param   array     $repreqidlist         Optional - ReportRequestIdList
     * @param   array     $reptypelist          Optional - ReportTypeList
     * @param   array     $repprostatuslist     Optional - ReportProcessingStatusList [ _SUBMITTED_, _IN_PROGRESS_, _CANCELLED_, _DONE_, _DONE_NO_DATA_]
     * @return  object                          Returns XML response
     */
    public function GetReportRequestList($sellerid, $mwsauthtoken, $awsaccesskey, $secretkey, $maxcount = NULL, $reqdtfm = NULL, $reqdtto = NULL, $repreqidlist = NULL, $reptypelist = NULL, $repprostatuslist = NULL)
    {
        $param = array(
            'AWSAccessKeyId'        => $awsaccesskey, 
            'Action'                => 'GetReportRequestList', 
            'MWSAuthToken'          => $mwsauthtoken, 
            'Merchant'              => $sellerid, 
            'SignatureMethod'       => 'HmacSHA256', 
            'SignatureVersion'      => '2', 
            'Timestamp'             => date("c", time()), 
            'Version'               => '2009-01-01'
        ); 

        // Optional parameters
        if(isset($maxcount)) $param['MaxCount']         = $maxcount;
        if(isset($reqdtfm)) $param['RequestedFromDate'] = $reqdtfm;
        if(isset($reqdtto)) $param['RequestedToDate']   = $reqdtto;

        // Report request id list array parameter
        if(isset($repreqidlist)) 
        {   
            if(is_array($repreqidlist)) 
            {
                $n = 1; 

                foreach($repreqidlist as $rep_req_id)
                {
                    $param['ReportRequestIdList.Id.'.$n] = $rep_req_id;

                    $n++; 
                }
            }
            else $param['ReportRequestIdList.Id.1'] = $repreqidlist;
        }

        // Report type list array parameter
        if(isset($reptypelist)) 
        {   
            if(is_array($reptypelist)) 
            {
                $n = 1; 

                foreach($reptypelist as $rep_type)
                {
                    $param['ReportTypeList.Type.'.$n] = $rep_type;

                    $n++; 
                }
            }
            else $param['ReportTypeList.Type.1'] = $reptypelist;
        }

        // Report processing status list array parameter
        if(isset($repprostatuslist)) 
        {   
            if(is_array($repprostatuslist)) 
            {
                $n = 1; 

                foreach($repprostatuslist as $pro_status)
                {
                    $param['ReportProcessingStatusList.Status.'.$n] = $pro_status;

                    $n++; 
                }
            }
            else $param['ReportProcessingStatusList.Status.1'] = $repprostatuslist;
        }

        // Get request url
        $request_url = $this->GenerateRequestURL($secretkey, $param); 

        // Make curl request
        return $this->CurlRequest($request_url);
    }

    /**
     * Get report list
     *
     * @param [type] $sellerid
     * @param [type] $mwsauthtoken
     * @param [type] $awsaccesskey
     * @param [type] $secretkey
     * @param [type] $reporttypelist
     * @return void
     */
    public function GetReportList($sellerid, $mwsauthtoken, $awsaccesskey, $secretkey, $maxcount = NULL, $reporttypelist = NULL)
    {   
        
        $param = array(
            'AWSAccessKeyId'        => $awsaccesskey, 
            'Action'                => 'GetReportList', 
            'MWSAuthToken'          => $mwsauthtoken, 
            'Merchant'              => $sellerid, 
            'SignatureMethod'       => 'HmacSHA256', 
            'SignatureVersion'      => '2', 
            'Timestamp'             => date("c", time()), 
            'Version'               => '2009-01-01'
        ); 

        // Max count parameter
        if(isset($maxcount))
        {
            $param['MaxCount'] = $maxcount;
        }

        // Report type list array parameter
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
        $request_url = $this->GenerateRequestURL($secretkey, $param); 

        // Make curl request
        return $this->CurlRequest($request_url);
    }

    /**
     * Get report list by next token
     *
     * @param [type] $sellerid
     * @param [type] $mwsauthtoken
     * @param [type] $awsaccesskey
     * @param [type] $secretkey
     * @param [type] $nexttoken
     * @return void
     */
    public function GetReportListByNextToken($sellerid, $mwsauthtoken, $awsaccesskey, $secretkey, $nexttoken)
    {
        $param = array(
            'AWSAccessKeyId'        => $awsaccesskey, 
            'Action'                => 'GetReportListByNextToken', 
            'MWSAuthToken'          => $mwsauthtoken, 
            'Merchant'              => $sellerid, 
            'SignatureMethod'       => 'HmacSHA256', 
            'SignatureVersion'      => '2', 
            'Timestamp'             => date("c", time()), 
            'Version'               => '2009-01-01', 
            'NextToken'             => $nexttoken
        ); 

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
     * @param [type] $reportid
     * @return void
     */
    public function GetReport($sellerid, $mwsauthtoken, $awsaccesskey, $secretkey, $reportid)
    {
        $param = array(
            'AWSAccessKeyId'        => $awsaccesskey, 
            'Action'                => 'GetReport', 
            'MWSAuthToken'          => $mwsauthtoken, 
            'Merchant'              => $sellerid, 
            'SignatureMethod'       => 'HmacSHA256', 
            'SignatureVersion'      => '2', 
            'Timestamp'             => date("c", time()), 
            'Version'               => '2009-01-01', 
            'ReportId'             => $reportid
        ); 

        // Get request url
        $request_url = $this->GenerateRequestURL($secretkey, $param); 

        // Make curl request
        return $this->CurlRequest($request_url);
    }
}
?>