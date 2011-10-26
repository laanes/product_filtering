<?php 

require_once('ProductResults.php');
require_once('APF_URL.php');

class Advanced_Product_Filtering {

		public static $allowedKeys=array();
		
		public static $parameters = array();
		
		public static $sql_query_parts = array();
		
		public static $filters_query_string;
		
		public static $regex_list;
		
		public static $url_query;

		public static $prefix;
		
		public $product_ids = array();

		public $filters = array();

		public $count = array();
		
		public $product_results;
		
		public $url_controller;
		
		public $filterVariables;
		

	
														
		public function __construct( $GET, $prefix="I", $URL_QUERY ) {
			
		self::$parameters = $GET;
		
		$this->createProductResultsObject();
		
		$this->product_ids = $this->product_results->product_ids;

		($prefix) ? self::$prefix = $prefix."." : self::$prefix = "";

		$this->get_allowed_keys();

		$this->get_sql_query_parts($prefix="");

		$this->merge_sql_query_parts();
		
		$this->url_controller = new APF_URL( $URL_QUERY );
		
		$this->create_filter_data();
		
		}
		
		public function createProductResultsObject() {
			
		if(isset(self::$parameters['searchStr'])) {
			
		$this->product_results = new ProductResults( "", "", self::$parameters['searchStr'] );
			
		}
			
		elseif(isset(self::$parameters['catId'])) {	
				
		$this->product_results = new ProductResults( "cat_id", self::$parameters['catId']);
		
		}
		
		elseif(isset(self::$parameters['brandId'])) {
			
		$this->product_results = new ProductResults( "brand_id", self::$parameters['brandId']);
			
		}
			
		}

		public static function call_filter_regex() {
		
		return self::$regex_list;
		
		}

		private function get_allowed_keys() {

		global $db;

		$query = "SELECT allowed_key, var_name FROM advanced_product_filtering";

		$filter_data = $db->select($query);
		
		for($i=0;$i<=count($filter_data)-1;$i++) {

		self::$allowedKeys[] = $filter_data[$i]['allowed_key'];
		$this->filterVariables[] = $filter_data[$i]['var_name'];

		}

		}

		public function get_sql_query_parts($prefix="") {

		foreach(self::$parameters as $key => $value) {
			
		if(in_array($key, self::$allowedKeys)) {
			
		if(is_array($value)) {
		
		// if(empty(self::$sql_query_parts)) {
		
		self::$sql_query_parts[] = " AND " . self::$prefix . $key . " IN (" . $this->create_csv_id_list($value).")";
		
		// }
		
		// else {
		
		// self::$sql_query_parts[] = " OR " . self::$prefix . $key . " IN (" . $this->create_csv_id_list($value).")";
		
		// }
				
		}
		
		else {
		
		// if(empty(self::$sql_query_parts)) {
	
		self::$sql_query_parts[] = " AND " . self::$prefix . $key . " = '" . $value."'";
		
		// }
		
		// else {
		
		// self::$sql_query_parts[] = " OR " . self::$prefix . $key . " = '" . $value."'";
		
		// }
	
		}
		

		}

		}

		}
		
		public function merge_sql_query_parts() {


		$filters = implode(' ', self::$sql_query_parts);

		$filters = str_replace('catId', 'cat_id', $filters);

		if (empty($filters)) {

		self::$filters_query_string = "";

		}

		else {

		self::$filters_query_string = $filters;

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
		
		
		public function mysql_safe($value) {
		
		$value = mysql_real_escape_string($value);
		
		return $value;
		
		}
		
		public function regex_safe($value) {
		
		$value = stripslashes($value);
		$value = urlencode($value);
		
		return $value;
		
		}
		
		public function bulletProof($value) {
		
		$value = $this->mysql_safe($value);
		$value = $this->regex_safe($value);
		
		return $value;
		
		}
		
		public function create_filter_data() {
			
			foreach($this->filterVariables as $key => $var_name) {
				
				if(!empty($var_name)) {
				
				$get_filter = "get_$var_name";
				
				$this->filters[$var_name] = $this->$get_filter();
				$this->count[$var_name] = count($this->filters[$var_name]);
				
				}
			
			}

		}
		
		public function get_cat($homepage=false) {
		
		global $db;
		
		$url_filter = self::$filters_query_string;
		
		$query = "SELECT C.cat_name, C.cat_id FROM CubeCart_category as C INNER JOIN CubeCart_inventory as I ON C.cat_id = I.cat_id WHERE I.productId IN ('". $this->product_ids ."')";
		
		if(!$this->is_only_filter('cat_id')) {

		$query .= "$url_filter ";
		
		}
		
		$query .= "GROUP BY C.cat_name";
		
		$cats = $db->select($query);
		
		return $cats;
		
		}
		
		/* BRAND  */
		public function get_brand() {
		
		if($url_params['_a'] === 'viewBrand') { return false; }
		
			else {
			
				global $db;

				$url_filter = self::$filters_query_string;

				$query = "SELECT B.brand_name, B.brand_id FROM CubeCart_productbrands as B INNER JOIN CubeCart_inventory as I ON B.brand_id = I.brand_id WHERE I.productId IN ('". $this->product_ids ."')";

				if(!$this->is_only_filter('brand_id')) {
				
				$query .= $url_filter;
				
				}
				
				$query .= " GROUP BY B.brand_name";
				
				$brands = $db->select($query);
				
				return $brands;
			
			}
		}
		
		/* BRAND  */

	
		/* PRICE RANGE  */
		public function get_price_ranges() {
		
		global $db;
	
		$url_filter = self::$filters_query_string;

		$query = "SELECT R.range_id, R.price_range FROM price_ranges as R INNER JOIN CubeCart_inventory as I ON R.range_id = I.price_range WHERE I.productId IN ('". $this->product_ids ."')";
		
		if(!$this->is_only_filter('price_range')) {
		
		$query .= $url_filter;
		
		}

		$query .= "GROUP BY R.range_id";
		
		$ranges = $db->select($query);
		
		return $ranges;
		
		}
		/* PRICE RANGE  */
		
		
		/*  TYPE  */
		public function get_type() {
		global $db;

		$url_filter = self::$filters_query_string;
		
		$query = "SELECT I.type FROM CubeCart_inventory as I WHERE I.productId IN ('". $this->product_ids ."')"; 
		
		// if(!$this->is_only_filter('type')) {
		
		$query .= $url_filter;
		
		// }
		
		$query .= "GROUP BY I.type";
		
		$type = $db->select($query);
		
		return $type;
		
		$db->select("SELECT I.type FROM CubeCart_inventory as I WHERE I.productId IN ('". $this->product_ids ."')");
		
		}
		/* TYPE  */
		
		
		/* FINISH  */
		public function get_finish() {
		
		global $db;

		$url_filter = self::$filters_query_string;
		
		$query = "SELECT I.finish FROM CubeCart_inventory as I WHERE I.productId IN ('". $this->product_ids ."')"; 
		
		// if(!$this->is_only_filter('finish')) {
		
		$query .= $url_filter;
		
		// }

		$query .= "GROUP BY I.finish";
		
		$finish = $db->select($query);
		
		return $finish;
		
		}
		/* FINISH  */
		
		/* FIXING CENTRES  */
		public function get_fixing_centres() {
		
		global $db;

		$url_filter = self::$filters_query_string;
		
		$query = "SELECT I.fixing_centres FROM CubeCart_inventory as I WHERE I.productId IN ('". $this->product_ids ."')"; 
		
		// if(!$this->is_only_filter('fixing_centres')) {
		
		$query .= $url_filter;
		
		// }
		
		$query .= "GROUP BY I.fixing_centres ORDER BY (I.fixing_centres+00) ASC";

		$fixing_centres = $db->select($query);
		
		return $fixing_centres;
		
		}
		/* FIXING CENTRES  */
		
		/* MOST POPULAR  */
		public function get_most_popular() {
		
		global $db;

		$url_filter = self::$filters_query_string;
		
		$query = "SELECT I.fixing_centres FROM CubeCart_inventory as I WHERE I.productId IN ('". $this->product_ids ."') $url_filter GROUP BY I.fixing_centres ORDER BY (I.fixing_centres+00) ASC";
		$fixing_centres = $db->select($query);
		
		return $fixing_centres;
		
		}
		/* MOST POPULAR  */
		
		public function param_array_to_str( $parameters, $operator="AND", $url=false ) {

		$str = "";
		$count = 0;
			
			foreach( $parameters as $key => $value ):

			$count++;

				$str .= $this->sanitize_param_key( $key );

				if( is_array( $value ) ) {
						
					foreach( $value as $val_key => $val_value ):

					$str .= $this->sanitize_param_key( $val_key );
					 
					$str .= "'" . $this->sanitize_param_value( $val_value ) . "'";					

					endforeach;

				}

				else {
						
				$str .= "=" . $this->sanitize_param_value( $value );

				}

			if( count( $parameters ) > $count ) {
				
			$str .= " " . $operator . " ";

			}

			endforeach;

		// if( $url ) {
			
		// $str = urlencode($str);

		// }

		return $str;

		}

		private function sanitize_param_value( $data ) {
			
		$data = str_replace( '+', '\+', $data );

		return $data;

		}

		private function sanitize_param_key( $data ) {
			
		$data = self::escape_square_brackets( $data );

		return $data;

		}

		private static function escape_square_brackets($value) {
		
		$value = str_replace('[', '\[', $value);
		$value = str_replace(']', '\]', $value);
		
		return $value;
		
		}

		public function count_prods( $parameters, $includeQueryParams=true, $operator = "AND" ) {
		
		global $db;
		
		if(!empty($this->product_ids)) {

		$param = $this->param_array_to_str( $parameters, $operator );
		
		$url_filter = self::$filters_query_string;
		
		$condition = "productId IN ('" . $this->product_ids ."') AND $param";

		if( $includeQueryParams ) {
		
		$condition .= $url_filter;
		
		}
		
		$query = "SELECT COUNT(productId) as product_count FROM CubeCart_inventory as I WHERE $condition";
		
		$prod_count = $db->select( $query );
		
		return $prod_count;
		
		}
		
		else {
		
		return false;
		
		}
		
		}
		
		public function is_only_filter($filter_id="") {
		
		if ($this->url_controller->has_parameters($this->url_controller->request_url) >= 1
		
		&& strpos($this->url_controller->request_url, $filter_id)) {
		
		return true;
		
		}
		
		else {
		
		return false;
		
		}
		
		}
		
		public function showAllFilters() {
		
		if(self::$parameters['cat_id']) {
		
		return true;
		
		}
		
		elseif(self::$parameters['_a'] == 'viewCat' && !isset(self::$parameters['searchStr'])) {
		
		return true;
		
		}
		
		else {
		
		return false;
		
		}
		
		}
		
}

?>