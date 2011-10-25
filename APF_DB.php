<?php

class APF_DB {

		public $parameters = array();
		
		public $allowedKeys;
		
		public $sql_query_parts = array();
		
		public $prefix;
		
		public $filters_query_string;
		
		public $search_results = array();
		
		public $cat_ids_in_search;
		
		public $brand_ids_in_search;
		
		public $product_ids;
		
		public $cats_query;
		
		
		public function __construct($GET, $prefix="", $searchStr="") {
		
		$this->search_results = $this->get_search_results($searchStr);
		
		(isset($prefix)) ? $this->prefix = $prefix."." : $this->prefix = "";
		
		$this->initialize($GET, $searchStr="");
		
		}
		
		
		public function initialize($GET, $searchStr="") {
		
		$this->parameters = $GET;
		
		$this->get_ids_in_search();
		
		$this->get_allowed_keys();
		
		$this->create_sql_query_parts();
		
		$this->build_filters_query_string();
		
		}
		
		public function get_ids_in_search() {
		
		for($i=0; $i<=count($this->search_results)-1; $i++) {
		
		$search_cat_ids[] = $this->search_results[$i]['cat_id'];
		$search_brand_ids[] = $this->search_results[$i]['brand_id'];
		$search_product_ids[] = $this->search_results[$i]['productId'];
		
		}
		
		$u_search_cat_ids = array_unique($search_cat_ids);
		$u_search_brand_ids = array_unique($search_brand_ids);
		$u_search_product_ids = array_unique($search_product_ids);
		
		$cat_ids = implode('\',\'', $u_search_cat_ids);
		$brand_ids = implode('\',\'', $u_search_brand_ids);
		$product_ids = implode('\',\'', $u_search_product_ids);
		
		$this->cat_ids_in_search = $cat_ids;
		$this->brand_ids_in_search = $brand_ids;
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

		if (empty($filters)) {

		$this->filters_query_string = "";
		}
		else {
		$this->filters_query_string = $filters;
		}
		}
		
		public function get_categories($total_params=4, $parameter="") {
		global $db;
		
		// if(count($this->parameters) == $total_params && isset($this->parameters[$parameter])) {

		// $url_filter = "";

		// }

		// else {

		$url_filter = $this->filters_query_string;

		// }
		
		$query = "SELECT C.cat_name, C.cat_id FROM CubeCart_category as C INNER JOIN CubeCart_inventory as I ON C.cat_id = I.cat_id WHERE I.cat_id IN ('". $this->cat_ids_in_search ."') $url_filter GROUP BY C.cat_name";
		
		$cats = $db->select($query);
		
		return $cats;
		
		}
		
		/* BRAND  */
		public function get_brands($total_params=4, $parameter="") {
		
		global $db;

		$url_filter = $this->filters_query_string;

		$query = "SELECT B.brand_name, B.brand_id FROM CubeCart_productbrands as B INNER JOIN CubeCart_inventory as I ON B.brand_id = I.brand_id WHERE I.brand_id IN ('". $this->brand_ids_in_search ."') $url_filter GROUP BY B.brand_name";
		$brands = $db->select($query);
		return $brands;
		}
		/* BRAND  */
	
		/* PRICE RANGE  */
		public function get_price_ranges($total_params=4, $parameter="") {
		global $db;
	
		$url_filter = $this->filters_query_string;

		$query = "SELECT R.range_id, R.price_range FROM price_ranges as R INNER JOIN CubeCart_inventory as I ON R.range_id = I.price_range WHERE I.productId IN ('". $this->product_ids ."') $url_filter GROUP BY R.range_id";
		$ranges = $db->select($query);
		
		return $ranges;
		
		}
		/* PRICE RANGE  */
		
		
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
		
		// if(!empty($this->filters_query_string) && count($this->parameters) > 4) {
		
		$url_filter = $this->filters_query_string;
		
		// }
		
		// else {
		
		// $url_filter = "";
		
		// }
		
		if(!empty($this->cat_ids_in_search)){
		
		$condition = "productId IN ('".$this->product_ids."') AND $parameter = '$id' $url_filter";
		}
		
		
		$query = "SELECT COUNT(productId) as product_count FROM CubeCart_inventory as I WHERE $condition";
		$prod_count = $db->select($query);
		return $prod_count;
		
		}
		
		public function get_search_results($searchStr="") {
		
		global $db;
		
		$searchStr = trim(preg_replace(array('#^or\s#i','#^and\s#i'),'', $searchStr));
		#:dazza:# Relevant Search
		$pattern ="/([a-zA-Z0-9])+-([a-zA-Z0-9_-])+/";
		preg_match($pattern, $searchStr, $matches);
		if ($matches == TRUE){

		if (strlen($matches[0])>4) {
		$searchStr = str_replace("$matches[0]", "\"$matches[0]\"", $searchStr);
		}}
		$searchStr = strtoupper($searchStr);
		
		$searchwords = split ( "[ ,]", sanitizeVar($searchStr));   
		foreach ($searchwords as $word) {
		$searchArray[] = $word;
			if (strlen($word)>3) {
		$searchword[] = $word;
		}
		if (strlen($word)<4) {
		$searchsmall[] = $word;
		}
		preg_match($pattern, $word, $wordmatches);
		if ($wordmatches == TRUE && (strlen($word)<5)){
		$searchsmall[] = $word;
		}
		}
		$noKeys2 = count($searchsmall);

		$noKeys = count($searchArray);
		$noKeys1 = $noKeys - $noKeys2;

		if ($noKeys2 == TRUE) {
		$like = '';
		for ($i=0; $i<$noKeys2; $i++) {
		$ucSearchTerm = $searchsmall[$i];
		if ($ucSearchTerm == TRUE){
		$like .= "AND (I.name LIKE '%".$searchsmall[$i]."%' OR I.description LIKE '%".$searchsmall[$i]."%')";
		}
		}
		}
		
		$indexes = $db->getFulltextIndex('inventory', 'I'); //array('inventory', 'inv_lang'));
		
		$where[] = "C.cat_id = I.cat_id";
		$where[] = "C.hide = '0'";
		$where[] = "(C.cat_desc != '##HIDDEN##' OR C.cat_desc IS NULL)";
		$where[] = "I.disabled = '0' ";
		#:dazza:# Relevant Search
		$whereString = sprintf('AND %s%s', implode(' AND ', $where), $like);
		
		if (is_array($indexes)) {
		sort($indexes);

		$mode = ' IN BOOLEAN MODE';

		if (!empty($searchStr)) {

		if (empty($orderSort)) {
		$orderSort = " ORDER BY productCode ASC";
		}
		$matchString = sprintf(" (0.9 * ( MATCH (%s) AGAINST(%s%s)) + (0.2 * (MATCH (I.description) AGAINST (%2\$s%3\$s))))", "I.name", $db->mySQLsafe($searchStr), $mode); 	

		$matchString1 = sprintf(" MATCH (%s) AGAINST(%s%s)", implode(',', $indexes), $db->mySQLsafe($searchStr), $mode); 


		#:dazza:# Relevant Search
		$search = sprintf("SELECT DISTINCT(I.productId), I.*, %2\$s AS SearchScore FROM %1\$sCubeCart_inventory AS I , %1\$sCubeCart_category AS C WHERE (%6\$s) >= %4\$s ".$prod_filters." AND C.cat_id > 0 %3\$s GROUP BY I.productId %5\$s", $glob['dbprefix'], $matchString, $whereString, $noKeys1, $orderSort, $matchString1);

		// var_dump($search);

		} 


		else {

		$search = sprintf("SELECT DISTINCT(I.productId), I.* FROM %1\$sCubeCart_inventory AS I, %1\$sCubeCart_category AS C WHERE I.cat_id > 0 %2\$s %3\$s GROUP BY I.productId", $glob['dbprefix'], $whereString, $orderSort);

		}	
		
		$productListQuery = $search;
		
		## Moved into if to stop MySQL error on index failure
		
		$productResults = $db->select($productListQuery);
		
		return $productResults;
		
		}
		
		}
		
}
		
?>