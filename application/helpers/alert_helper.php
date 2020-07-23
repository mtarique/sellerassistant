<?php 
/**
 * Alert message helper
 * 
 * @package     Codeigniter
 * @version     3.1.11
 * @subpackage  Helper
 * @author      MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit("No direct script access allowed"); 

/**
 * To show alert messages
 * 
 * Uses bootstrap alert component
 *
 * @param   string  $type   danger, success, info, warning
 * @param   string  $msg    Message to be displayed
 * @return  void
 */
function show_alert($type, $msg)
{   
    switch ($type) 
    {   
        // Set icons and themes based on alert type
        case "danger": 
            $icon  = '<i class = "far fa-times-circle fa-lg"></i>'; 
            $theme = 'text-pink-600-border-pink-600'; 
            break; 
        case "success": 
            $icon  = '<i class = "far fa-check-circle fa-lg"></i>'; 
            $theme = 'text-green-600-border-green-600'; 
            break;  
        case "info": 
            $icon  = '<i class = "fas fa-info-circle fa-lg"></i>'; 
            $theme = 'text-cyan-600-border-cyan-600'; 
            break; 
        case "primary": 
                $icon  = '<i class = "fas fa-info-circle fa-lg"></i>'; 
                $theme = 'text-cyan-600-border-cyan-600'; 
                break; 
        case "warning": 
            $icon  = '<i class = "fas fa-exclamation-circle fa-lg"></i>'; 
            $theme = 'text-yellow-700-border-yellow-700'; 
            break; 
        default: 
            $icon  = '';
            $theme = '';
    }

    // Bootstrap alert
    $html = '
        <div class="alert alert-'.$type.' '.$theme.' alert-dismissible rounded-0 fade show px-3" role="alert">
            <div class="d-flex flex-row align-items-center">
                <div class="pr-2">'.$icon.'</div>
                <div class="fs-14">'.$msg.'</div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    ';   

    return $html; 
}
?>