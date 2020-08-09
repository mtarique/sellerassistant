<?php  
// Connect to database
require_once($_SERVER['DOCUMENT_ROOT']."/oms/config/ConConfig.php");
// Custom defined functions
require_once($_SERVER['DOCUMENT_ROOT']."/oms/lib/mtdefined/mt-function.php");
// Start a php session, Get Active User Info
require_once($_SERVER['DOCUMENT_ROOT']."/oms/config/Session.php");
// File to track users access, must include Session.php and ConConfig.php before this
require_once($_SERVER['DOCUMENT_ROOT']."/oms/config/UsersLog.php");

/*
|-------------------------------------------
|   GET SKUs LIST
|-------------------------------------------
*/
if(isset($_GET['getskuslist'])){
	// Print the scrollable container
	$msg = '<div class="pre-scrollable" style="padding-left: 10px !important; background-color: #ffffcc !important;">';

	// Query to get item numbers to calculate historical ros
	$query = $conn->query("SELECT item_no FROM amazon_ros") 
	or die("An error occurred: ".$conn->error);

	// Declare an empty array to hold all sku list
	$full_sku_list = array();
	while($result = $query->fetch_array()) $full_sku_list[] = $result['item_no'];

	// Itnitialize 200 SKU's set counter
	$sku_set_id = 0;

	// Loop through sku list to get sku sets
	foreach(array_chunk($full_sku_list, 200) as $sku_set){
		// Increament SKU set id by 1
		$sku_set_id++; 

		// Print SKU set id
		$msg .= '<label><input type="checkbox" class="cbxSkuSet" value="'.$sku_set_id.'" data="Wow"> <a href="#" data-toggle="collapse" data-target="#skuSet'.$sku_set_id.'">SKU SET - '.$sku_set_id.'</a></label><br>';

		// Print SKU set container
		$msg .= '<div id=skuSet'.$sku_set_id.' class="collapse">';

		// Loop through sku set ang get sku printed
		foreach($sku_set as $sku){
			$msg .= '<label class="checkbox-inline"><input type="checkbox" name="cbxSKUsList[]" class="cbxSKUsList cbxSkuSet'.$sku_set_id.'" value="'.$sku.'">'.$sku.'</label><br>';
		}

		// Close SKU set container
		$msg .= '</div>'; 
	}

	// Close dcrollable container
	$msg .= '</div>';

	// Encode response in json
	echo json_encode(array('success'=>true, 'message'=>$msg));
}

if(isset($_GET['getskuslistold'])){
	// Query to get item numbers to calculate historical ros
	$query = $conn->query("SELECT item_no FROM amazon_ros") 
	or die("An error occurred: ".$conn->error);
	$msg = '<div class="pre-scrollable" style="padding-left: 10px !important; background-color: #ffffcc !important;">';
	$n = 0;
	$msg .= '<h4 class="text-primary" data-toggle="collapse" data-target="#skuSet1">SKU SET # 1</h4>';
	while($result = $query->fetch_array()){	
		$n++;
		$msg .= '<label class="checkbox-inline"><input type="checkbox" name="cbxSKUsList[]" class="cbxSKUsList" value="'.$result['item_no'].'">'.$result['item_no'].'</label><br>';
		if($n%200 == 0){
			$msg .= '<h4 class="text-primary">SKU SET # '.($n/200+1).'</h4>';
		}
	}
	$msg .= '</div>';

	echo json_encode(array('success'=>true, 'message'=>$msg));
}

?>