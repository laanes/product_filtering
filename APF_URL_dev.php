<?php include_once('Advanced_Product_Filtering.php'); ?>
<?php

class APF_URL extends Advanced_Product_Filtering {

		public $request_url;
		public $separator;
		public static $regex_list;
		public static $allowedKeys;
		
		public function __construct($request_url) {
		
		self::get_allowed_keys();
		self::generate_filter_regex();
		$this->request_url = $request_url;
		$this->separator = (strpos($request_url, '?')) ? '&' : '?';
		
		}
		
		public static function generate_filter_regex() {
		
		$regex_list = implode('|', self::$allowedKeys);
		
		self::$regex_list = $regex_list;
		
		return $regex_list;
			
		}
		
		private function get_allowed_keys() {

		global $db;

		$query = "SELECT allowed_key, filter_name FROM advanced_product_filtering";

		$filter_data = $db->select($query);
		
		for($i=0;$i<=count($filter_data)-1;$i++) {

		self::$allowedKeys[] = $filter_data[$i]['allowed_key'];

		}

		}

		public function generate_filter_class($name="", $value="") {
		
		$name = self::escape_square_brackets($name);
		
		$value = $this->bulletProof($value);
		
		$value = str_replace('+', '\+', $value);
			
		$pattern = "[\?&]".$name."=".$value;
		
		if(!preg_match("#$pattern#i", $this->request_url)) {
		
		$class = "passive_filter";
		
		}
		
		else {
		
		$class = "active_filter";
		
		}
		
		return $class;
		
		}
		
		public function create_link( $parameters ) {	
		
		$value = $this->bulletProof($value);
		
		$separator = ( strpos( $this->request_url, '?' ) ) ? "&" : "?";
		
		$parameter = $this->param_array_to_str( $parameters, $separator, $esc_symb=true );
		
		$pattern = "[\?&]";

		if($square_brackets) {

		$pattern .= .self::escape_square_brackets($name)."=".$value;

		}
		
		if(preg_match("#$pattern#i", $this->request_url) == false) {
		
		$link = $this->request_url.$parameter;
		
		}
		
		else {
		
		$link = preg_replace("#$pattern#i", "", $this->request_url);
		
		}
		
		return $link;
		
		}
		
		public function remove_all_parameters($url) {
		
		$filters = self::$regex_list;
		
		$url = preg_replace("#[\?&]($filters)\[?\d?\]?=.*#", '', $url);
		
		return $url;
		
		}
		
		public function has_parameters($url) {
		
		$filters = self::$regex_list;
		
		$url = preg_match("#[\?&]($filters)\[?\d?\]?=.*#", $url);
		
		return $url;
		
		}
		
		public static function sanitize_filter_name($value){
		
		$value = preg_replace('#[^\w_]+#', '', $value);
		
		return $value;
		
		}
		
		public static function escape_square_brackets($value) {
		
		$value = str_replace('[', '\[?\d?', $value);
		$value = str_replace(']', '\]?', $value);
		
		return $value;
		
		}
		
}

?>