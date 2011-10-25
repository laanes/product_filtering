<?php require_once('APF_DB.php'); ?> 
<?php

class APF_DB_CAT extends APF_DB {

		public $parameters = array();
		
		public $allowedKeys;
		
		public $sql_query_parts = array();
		
		public $prefix;
		
		public $filters_query_string;
		
		public $cat_ids;
		
		public $product_results;
		
		public $brand_ids;
		
		public $product_ids;
		
		public $cats_query;
		
		
		public function __construct($GET, $prefix="", $cat_id="") {
		
		$this->product_results = $this->get_product_results($cat_id);
		
		(isset($prefix)) ? $this->prefix = $prefix."." : $this->prefix = "";
		
		$this->initialize($GET);
		
		}
		
		
		public function initialize($GET) {
		
		if(isset($GET['page'])) {

		unset($GET['page']);
		
		}
		
		$this->parameters = $GET;
		
		$this->get_ids();
		
		$this->get_allowed_keys();
		
		$this->create_sql_query_parts();
		
		$this->build_filters_query_string();
		
		}
		
		public function get_ids() {
		
		for($i=0; $i<=count($this->product_results)-1; $i++) {
		
		$product_cat_ids[] = $this->product_results[$i]['cat_id'];
		$product_brand_ids[] = $this->product_results[$i]['brand_id'];
		$product_product_ids[] = $this->product_results[$i]['productId'];
		
		}
		
		$u_product_cat_ids = array_unique($product_cat_ids);
		$u_product_brand_ids = array_unique($product_brand_ids);
		$u_product_product_ids = array_unique($product_product_ids);
		
		$cat_ids = implode('\',\'', $u_product_cat_ids);
		$brand_ids = implode('\',\'', $u_product_brand_ids);
		$product_ids = implode('\',\'', $u_product_product_ids);
		
		$this->cat_ids = $cat_ids;
		$this->brand_ids = $brand_ids;
		$this->product_ids = $product_ids;
		
		}
		
		
		private function get_allowed_keys() {
		
		global $db;
		
		$query = "SELECT allowed_key, filter_name FROM advanced_product_filtering";
		
		$filter_data = $db->select($query);
		
		for($i=0;$i<=count($filter_data)-1;$i++) {

		$this->allowedKeys[] = $filter_data[$i]['allowed_key'];

		}

		}
		
		public function create_sql_query_parts() {
		
		foreach($this->parameters as $key => $value) {
			
		if(in_array($key, $this->allowedKeys)) {
			
		if(is_array($value)) {
		
		$this->sql_query_parts[] = " AND " . $this->prefix . $key . " IN (" . $this->create_csv_id_list($value).")";	
				
		}
		
		else {

		$this->sql_query_parts[] = " AND " . $this->prefix . $key . " = '" . $value."'";
		
		}
		

		}

		}
		
		}
		
		public function create_csv_id_list($input_array) {
			
		$values = array();
			
		foreach($input_array as $key => $value) {
			
		if(!in_array($value, $values)) {
			
		$values[] = "'".$value."'";
				
		}	
				
		}
		
		$values = array_unique($values);
		
		$values = implode(',', $values);
		
		return $values;
			
		}
		
		public function build_filters_query_string() {

		$filters = implode(' ', $this->sql_query_parts);

		$filters = str_replace('catId', 'cat_id', $filters);
		
		$filters = str_replace('brandId', 'brand_id', $filters);

		if (empty($filters)) {

		$this->filters_query_string = "";
		}
		else {
		$this->filters_query_string = $filters;
		}
		}
		
		public function get_brand_by_id($id="") {
		
		global $db;
		$query = "SELECT brand_name, brand_id FROM CubeCart_productbrands WHERE brand_id=".$id;
		$brand = $db->select($query);
		return $brand;
		
		}
		
		public function get_all_brands($brand_id="") {
		global $db;
		$query = "SELECT brand_name, brand_id FROM CubeCart_productbrands WHERE brand_id !=".$brand_id;
		$brands = $db->select($query);
		return $brands;
		}
		
		public function get_categories() {
		global $db;
		$query = "SELECT C.cat_name, C.cat_id FROM CubeCart_category as C INNER JOIN CubeCart_inventory as I ON C.cat_id = I.cat_id WHERE I.cat_id IN ('". $this->cat_ids ."') GROUP BY C.cat_name";
		$cats = $db->select($query);
		return $cats;
		}
		public function get_brands($total_params=3, $parameter="") {
		global $db;
		if(count($this->parameters) == $total_params && isset($this->parameters[$parameter])) {		$url_filter = "";				}				else {		$url_filter = $this->filters_query_string;				}		
		$query = "SELECT B.brand_name, B.brand_id FROM CubeCart_productbrands as B INNER JOIN CubeCart_inventory as I ON B.brand_id = I.brand_id WHERE I.brand_id IN ('". $this->brand_ids ."') $url_filter GROUP BY B.brand_name";
		$brands = $db->select($query);
		return $brands;
		}
		
		public function get_price_ranges($total_params=3, $parameter="") {
		global $db;				
		
		// if(count($this->parameters) == $total_params && isset($this->parameters[$parameter])) 
		
		// {		
		
		// $url_filter = "";				
		
		// }				
		
		// else {		
		
		$url_filter = $this->filters_query_string;	

		// }
		
		
		$query = "SELECT R.range_id
, R.price_range FROM price_ranges as R INNER JOIN CubeCart_inventory as I ON R.range_id = I.price_range WHERE I.productId IN ('". $this->product_ids ."') $url_filter GROUP BY R.range_id";
		$ranges = $db->select($query);
		
		return $ranges;
		}
		
		/*  TYPE  */
		public function get_type() {
		global $db;

		$url_filter = $this->filters_query_string;
		
		$query = "SELECT I.type FROM CubeCart_inventory as I WHERE I.productId IN ('". $this->product_ids ."') $url_filter GROUP BY I.type";
		$type = $db->select($query);
		
		return $type;
		
		}
		/* TYPE  */
		
		
		/* FINISH  */
		public function get_finish() {
		
		global $db;

		$url_filter = $this->filters_query_string;
		
		$query = "SELECT I.finish FROM CubeCart_inventory as I WHERE I.productId IN ('". $this->product_ids ."') $url_filter GROUP BY I.finish";
		$finish = $db->select($query);
		
		return $finish;
		
		}
		/* FINISH  */
		
		/* FIXING CENTRES  */
		public function get_fixing_centres() {
		
		global $db;

		$url_filter = $this->filters_query_string;
		
		$query = "SELECT I.fixing_centres FROM CubeCart_inventory as I WHERE I.productId IN ('". $this->product_ids ."') $url_filter GROUP BY I.fixing_centres ORDER BY (I.fixing_centres+00) ASC";
		$fixing_centres = $db->select($query);
		
		return $fixing_centres;
		
		}
		/* FIXING CENTRES  */
		
		public function count_prods($parameter="", $id=""){
		global $db;

		$url_filter = $this->filters_query_string;

		$param = "AND $parameter = '$id'";
		
		$condition = "I.productId IN ('".$this->product_ids."') $param $url_filter";
		
		$query = "SELECT COUNT(productId) as product_count FROM CubeCart_inventory as I WHERE $condition";
		$prod_count = $db->select($query);
		return $prod_count;
		
		}
		
		public function get_product_results($cat_id="") {
		
		global $db;
		
		$query = "SELECT * FROM CubeCart_inventory WHERE cat_id = ".$cat_id." AND cat_id > 0";
		
		$productResults = $db->select($query);
		
		return $productResults;
		
		}
}
		
?>