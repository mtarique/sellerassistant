<?php 
/**
 * Amazon FBA Fees Calculator helper 
 * 
 * Calculates various types of Amazon FBA fees.
 * 
 * This helper is loaded using the following code:
 * $this->load->helper('fba_fees_calc_helper'); 
 * 
 * NOTE: Don't included any helper or library that has already been
 * called in autoload. 
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Helper 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed.'); 

/**
     * Check product is oversize
     *
     * @param double $ls    Longest side
     * @param double $ms    Median side
     * @param double $ss    Shortest side
     * @param double $wt    Weight
     * @param double $dt    Order date or fees calculation date
     * @return boolean
     */
    function is_oversize($ls, $ms, $ss, $wt, $dt)
    {
        $last_updated = strtotime('2020-02-19'); // FBA fees calculation last updated on
        $order_date   = strtotime($dt);          // Order date or fees calculation date

        if($order_date >= $last_updated)
		{
			return ($ls > 18 || $ms > 14 || $ss > 8 || $wt > 20.75) ? true : false;	
		}
		else {
			return ($ls > 18 || $ms > 14 || $ss > 8 || $wt > 19.75) ? true : false;
		}
    }

    /**
     * Get dimensional weight 
     *
     * 22-Feb-2017: Product volume in cubic inches will be devided by 166.
     * 22-Feb-2018: Product volume in cubic inches will be devided by 139.
     * 19-Feb-2019: Amazon will apply a minimum width and height of 2 inches for oversize items.
     * 
     * @param double    $ls    Longest side
     * @param double    $ms    Median side
     * @param double    $ss    Shortest side
     * @param double    $wt    Weight
     * @param date      $dt    Order date or fees calculation date
     * @return void
     */
    function get_dim_wt($ls, $ms, $ss, $wt, $dt)
    {
		$update2017 = strtotime('2017-02-22');
		$update2018 = strtotime('2018-02-22');
		$update2019 = strtotime('2019-02-19');
		$update2020 = "No changes in dim weight calculations";
		$order_date = strtotime($dt);

		// Calculate dimensional weight based on date provided
		if($order_date >= $update2019)
		{
			// Calculate dimensional weight before 2020
            if(is_oversize($ls, $ms, $ss, $wt, $dt))
            {
				// Apply a minimum 2 inches to width and height
				if($ms < 2) $ms = 2;
				if($ss < 2) $ss = 2;
			}
            
            return round(($ls*$ms*$ss)/139, 2); 
		}
        elseif($order_date >= $update2018 && $order_date < $update2019)
        {
			// Calculate dimensional weight before 2019-02-19
			return round(($ls*$ms*$ss)/139, 2); 
		}
        elseif($order_date >= $update2017 && $order_date < $update2018)
        {
			// Calculate dimensional weight before 2018-02-22
			return round(($ls*$ms*$ss)/166, 2); 	
		}
		else return 0; // Algrothim not available before 2017-02-22
    }

    /**
     * Get product size code
     *
     * @param double    $ls    Longest side
     * @param double    $ms    Median side
     * @param double    $ss    Shortest side
     * @param double    $wt    Weight
     * @param date      $dt    Order date or fees calculation date
     * @return void
     */
    function get_size_code($ls, $ms, $ss, $wt, $dt)
    {   
        $CI = & get_instance(); 
            
        $CI->load->model('pricing/calculator_model');

        // Get dimensional weight
        $dim_wt = get_dim_wt($ls, $ms, $ss, $wt, $dt); 

        /**
         * Oversize product
         */
        if(is_oversize($ls, $ms, $ss, $wt, $dt))
        {   
            // Length & Girth
            $lg = 2*($ms+$ss)+$ls;

            // Query to get oversize product size chart
            $result = $CI->calculator_model->get_prod_size_by_type('Oversize', $dt); 

            if(!empty($result))
            {
                foreach($result as $row)
                {   
                    // Change median size for small oversize products
                    $ms = ($row->median_side > 0) ? $ms : 0; 

                    // Weight comparison for size tier other than Special Oversize
                    if(strpos($row->prod_size_tier, "Special Oversize") !== true)
                    {
                        $wt = ($dim_wt > $wt) ? $dim_wt : $wt; 
                    }

                    // Validate dimensions and weight to match current itteration
                    if($ls <= $row->longest_side && $ms <= $row->median_side && $lg <= $row->length_and_girth && $wt <= $row->max_prod_wt && $wt > $row->min_prod_wt)
                    {
                        return $row->prod_size_code;
                        break;
                    }
                    else continue;
                }
            }
        }
        else {
            
            // Query to get standard-size product size chart
            $result = $CI->calculator_model->get_prod_size_by_type('Standard-Size', $dt); 

            if(!empty($result))
            {
                foreach($result as $row)
                {
                    /**
                     * For standard-size products that weigh 1 lb or less,
                     * use the unit weight. For all other products, 
                     * use the larger of either the single unit weight or the dimensional weight.
                     */
                    if($wt > 1)
                    {
                        $wt = ($dim_wt > $wt) ? $dim_wt : $wt;
                    }

                    // Validate dimensions and weight to match current itteration
                    if($ls <= $row->longest_side && $ms <= $row->median_side && $ss <= $row->shortest_side && $wt <= $row->max_prod_wt && $wt >= $row->min_prod_wt)
                    {
                        return $row->prod_size_code;
                        break;
                    }
                    else continue;  
                }
            }
        }
    }

    /**
     * Get product size tier
     *
     * @param   string  $size_code  Product size code get from above function 
     * @param   date    $dt         Order date or fees calculation date
     * @return  void
     */
    function get_size_tier($size_code, $dt)
    {
        $CI = & get_instance(); 
            
        $CI->load->model('pricing/calculator_model');

        $result = $CI->calculator_model->get_prod_size_by_code($size_code, $dt);

        if(!empty($result))
        {
            $row = $result[0]; 
            return $row->prod_size_tier; 
        }
        else return "Not available"; 
    }

    /**
     * Get outbound shipping weight
     * 
     * Outbound Shipping Weight is calculated for each individual 
     * unit by adding the Packaging Weight to the greater of the 
     * Unit Weight or the Dimensional Weight. The total for each
     * unit is rounded up to the nearest pound.
     * 
     * If product is small standard or special oversize only
     * item unit weight will be added to pckg weight.
     * 
     * https://sellercentral.amazon.com/gp/help/external/201112670?language=en_US&ref=efph_201112670_cont_200209150 
     *
     * @param double    $ls    Longest side
     * @param double    $ms    Median side
     * @param double    $ss    Shortest side
     * @param double    $wt    Weight
     * @param date      $dt    Order date or fees calculation date
     * @return void
     */
    function get_ship_wt($ls, $ms, $ss, $wt, $dt)
    {   
        $CI = & get_instance(); 
            
        $CI->load->model('pricing/calculator_model');

        $size_code = get_size_code($ls, $ms, $ss, $wt, $dt); 
        $dim_wt    = get_dim_wt($ls, $ms, $ss, $wt, $dt);

        $result = $CI->calculator_model->get_prod_size_by_code($size_code, $dt);

        $row = $result[0];

        if(strpos($row->prod_size_tier, "Small Standard") !== false ||  strpos($row->prod_size_tier, "Special Oversize") !== false)
        {
			return ceil($wt+$row->packaging_wt);
		}
		else {
			return ($wt > $dim_wt) ? ceil($wt+$row->packaging_wt) : ceil($dim_wt+$row->packaging_wt);	
		}	
    }

    /**
     * Get FBA fulfillment fees
     *
     * @param double    $ls    Longest side
     * @param double    $ms    Median side
     * @param double    $ss    Shortest side
     * @param double    $wt    Weight
     * @param date      $dt    Order date or fees calculation date
     * @return void
     */
    function get_fba_ful_fees($ls, $ms, $ss, $wt, $dt)
    {   
        $CI = & get_instance();     
        $CI->load->model('pricing/calculator_model');

        if($ls > 0 && $ms > 0 && $ss > 0 && $wt > 0){
			// Get product size code
			$size_code = get_size_code($ls, $ms, $ss, $wt, $dt);

			// Get outbound shipping weight
			$ship_wt = get_ship_wt($ls, $ms, $ss, $wt, $dt);
            
            $result = $CI->calculator_model->get_fba_ful_fees($size_code, $dt); 

            $row = $result[0]; 

			// Get additional outbound shipping weight
            if($ship_wt > $row->first_outshp_wt)
            {
				$addl_ship_wt = $ship_wt-$row->first_outshp_wt;
			}
			else $addl_ship_wt = 0;

			// Return FBA fees
			return $row->fba_fees_first_wt+($row->fba_fees_addl_wt*$addl_ship_wt); 
		}
		else return 0;
    }

    /**
     * Get FBA storage fees
     *
     * @param double    $ls    Longest side
     * @param double    $ms    Median side
     * @param double    $ss    Shortest side
     * @param double    $wt    Weight
     * @param date      $dt    Order date or fees calculation date
     * @return void
     */
    function get_fba_stg_fees($ls, $ms, $ss, $wt, $dt)
    {   
        $CI = & get_instance();     
        $CI->load->model('pricing/calculator_model');

        if($ls > 0 && $ms > 0 && $ss > 0 && $wt > 0){
            // Get product size type
            $size_type = (is_oversize($ls, $ms, $ss, $wt, $dt)) ? 'Oversize' : 'Standard-Size';
            $prod_vol = $ls*$ms*$ss/pow(12, 3); // Cubic feet

            // Query to get storage fees by size type
            $result = $CI->calculator_model->get_fba_stg_fees($size_type, $dt);

            $row = $result[0]; 

            // Calculate and return monthly storage fees 
            return round($row->storage_fees * $prod_vol, 2);
        }
        else return 0;
    }

?>