<?php 
/**
 * FBA Estimated Fee Preview
 * 
 * @package     Codeigniter
 * @version     3.1.11
 * @subpackage  Controller
 * @author      MD TARIQUE ANWER| mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed'); 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Excel_test extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct(); 

        $this->load->helper(array('auth_helper')); 
    }

    /**
     * View Amazon FBA Fee Preview page
     *
     * @return void
     */
    public function index()
    {
        $page_data['title'] = "PHP Spreadsheet";
        $page_data['descr'] = "Testing excel using Phpspreadsheet in codeigniter."; 

        $this->load->view('exceltest', $page_data);
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Hello World!');

        $writer = new Xlsx($spreadsheet);

        //$filename = 'phpspreadsheetfile';
        //header('Content-Type: application/vnd.ms-excel');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="phpexcelfile.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        ob_end_clean();
        $writer->save('php://output'); // download file
        exit(); 
    }

    public function import()
    {
        $file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        if(isset($_FILES['upload_file']['name']) && in_array($_FILES['upload_file']['type'], $file_mimes)) 
        {
            $arr_file = explode('.', $_FILES['upload_file']['name']);
            $extension = end($arr_file);

            if('csv' == $extension)
            {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            } 
            else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }

            $spreadsheet = $reader->load($_FILES['upload_file']['tmp_name']);

            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            echo "<pre>";

            print_r($sheetData);

            echo "</pre>"; 
        }
    }
}