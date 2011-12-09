<?php 

require_once('ProductResults.php');
require_once('APF_URL.php');

class Advanced_Product_Filtering {

		static $filters_query_string;

		static $breadcrumbs;
		
		static $regex_list;
		
		static $url_query;

		static $id_field;

		static $prefix;

		static $keyTables;

		static $parameters;

		static $allowedKeys;

		static $filterVariables;

		static $sql_query_parts = array();

		var $product_results;

		var $url_controller;

		var $product_ids;

		var $filters;

		var $count;
		
														
		public function __construct( $GET, $prefix="I", $URL_QUERY ) {

		self::$parameters = $GET;

		// $this->set_table_and_field_by_location();

		// $this->build_breadcrumb();
		
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
				
			$this->product_results = new ProductResults( "cat_id", self::$parameters['catId'] );

			}

			elseif(isset(self::$parameters['brandId'])) {
				
			$this->product_results = new ProductResults( "brand_id", self::$parameters['brandId'] );

			}

			elseif(isset(self::$parameters['_a']) && self::$parameters['_a'] == 'viewOffer') {
				
			$this->product_results = new ProductResults( "offer", "1" );

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

		self::$allowedKeys[] 	 = $filter_data[$i]['allowed_key'];
		// self::$keyTables[] 		 = $filter_data[$i]['table'];
		self::$filterVariables[] = $filter_data[$i]['var_name'];

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


		// public function build_breadcrumb() {
			
		// 	foreach( self::$parameters as $key => $value ) {

		// 		if( in_array( $key, self::$allowedKeys ) ) {

		// 			if( is_array( $value ) ) {
						
		// 				foreach( $value as $index => $id ) {
							
		// 				$bread[] = $this->breadcrumb_from_url( $key, $id );

		// 				}

		// 			}

		// 			else {
				
		// 			$bread[] = $this->breadcrumb_from_url( $key, $value );

		// 			}

		// 		}

		// 	}

		// }

		// public function breadcrumb_from_url( $key, $value ) {
			
		// global $db;

		// $table      = $this->data_by_allowed_key( 'table', $key );
		// $name_field = $this->data_by_allowed_key( 'name_field', $key );

		// $sql =

		// "SELECT " . self::$name_field . " AS name";
		// $sql .= " FROM " . $table;
		// $sql .= " WHERE " . $key . " = " . $value; 
		// $sql .= " LIMIT 1";

		// $name = $db->select( $sql );

		// return $name;

		// }

		// public function data_by_allowed_key( $field, $key ) {
			
		// global $db;

		// $sql = "SELECT $field as data FROM advanced_product_filtering WHERE allowed_key = $key LIMIT 1";

		// $data = $db->select( $sql );

		// return $data[0]['data'];

		// }
		
		public function merge_sql_query_parts() {

		$filters = implode( ' ', self::$sql_query_parts );

		$filters = str_replace( 'catId', 'cat_id', $filters );

		if ( empty( $filters ) ) {

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
			
			foreach(self::$filterVariables as $key => $var_name) {
				
				if(!empty($var_name)) {
				
				$get_filter = "get_$var_name";
				
				$this->filters[$var_name] = $this->$get_filter();
				$this->count[$var_name] = count($this->filters[$var_name]);
				
				}
			
			}

		}
		
		public function get_filter( $fields = array(), $table = array(), $join = array(), $order = '' ) {
			
		global $db;

		$fieldList = implode( ', ', $fields );

		$sql = "SELECT $fieldList FROM " . $table['tableName'];

		$sql .= !empty($table['prefix']) ? ' AS ' . $table['prefix'] : '';

		if( !empty( $join ) ) {

		$sql .= " INNER JOIN " . $join['joinTable'];

		$sql .= !empty( $join['prefix'] ) ? ' AS ' . $join['prefix'] : '';
		
		$sql .= " ON " . $join['leftMatch'] . " = " . $join['rightMatch'];

		}

		$sql .= " WHERE I.productId IN ('". $this->product_ids ."')";

		$sql .= self::$filters_query_string;

		$sql .= " GROUP BY " . $fields[ 0 ];

		$sql .= !empty( $order ) ? ' ORDER BY ' . $order : '';

		return $db->select( $sql );

		}
		

		/* CATEGORY  */
		public function get_cat($homepage=false) {

		return $this->get_filter( 

		/*  $fields */  array( 'C.cat_name', 'C.cat_id' ), 

		/*  $table  */  array( 'tableName' => 'CubeCart_category', 'prefix' => 'C' ), 

		/*  $join   */  array( 'joinTable' => 'CubeCart_inventory', 'prefix' => 'I', 'leftMatch' => 'C.cat_id', 'rightMatch' => 'I.cat_id' ) );
		
		}
		/* CATEGORY  */

		
		/* BRAND  */
		public function get_brand() {
		
		if($url_params['_a'] === 'viewBrand') { return false; }
		
			else {

			return $this->get_filter( 

			/*  $fields */  array( 'B.brand_name', 'B.brand_id' ), 

			/*  $table  */  array( 'tableName' => 'CubeCart_productbrands', 'prefix' => 'B' ), 

			/*  $join   */  array( 'joinTable' => 'CubeCart_inventory', 'prefix' => 'I', 'leftMatch' => 'B.brand_id', 'rightMatch' => 'I.brand_id' ) );

			}
		}
		/* BRAND  */


		/* PRICE RANGE  */
		public function get_price_ranges() {

		return $this->get_filter( 

		/*  $fields */  array( 'R.range_id', 'R.price_range' ), 

		/*  $table  */  array( 'tableName' => 'price_ranges', 'prefix' => 'R' ), 

		/*  $join   */  array( 'joinTable' => 'CubeCart_inventory', 'prefix' => 'I', 'leftMatch' => 'R.range_id', 'rightMatch' => 'I.price_range' ) );

		}
		/* PRICE RANGE  */

		
		
		/*  TYPE  */
		public function get_type() {

		return $this->get_filter( 

		/*  $fields */  array( 'I.type' ), 

		/*  $table  */  array( 'tableName' => 'CubeCart_inventory', 'prefix' => 'I' ) );

		}

		/* TYPE  */

		
		
		/* FINISH  */
		public function get_finish() {

		return $this->get_filter( 

		/*  $fields */  array( 'I.finish' ), 

		/*  $table  */  array( 'tableName' => 'CubeCart_inventory', 'prefix' => 'I' ) );

		}
		/* FINISH  */


		
		/* FIXING CENTRES  */
		public function get_fixing_centres() {
		
		return $this->get_filter( 

		/*  $fields */  array( 'I.fixing_centres' ), 

		/*  $table  */  array( 'tableName' => 'CubeCart_inventory', 'prefix' => 'I' )
		, array(), '(I.fixing_centres+00) ASC' );

		}

		/* FIXING CENTRES  */
		
		public function param_array_to_str( $parameters, $operator ) {

		$str = "";
		$count = 0;
			
			foreach( $parameters as $key => $value ):

			$count++;

				$str .= $key;

				if( is_array( $value ) ) {
						
					foreach($value as $val_key => $val_value):

					$str .= $val_key . "'" . $val_value . "'";					

					endforeach;

				}

				else {
						
				$str .= " = '" . $value . "'";

				}

			if(count($parameters) > $count) {
				
			$str .= " " . $operator . " ";

			}

			endforeach;

		return $str;

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