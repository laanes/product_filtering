<?php require_once('Advanced_Product_Filtering.php');
class ProductResults extends Advanced_Product_Filtering {

		public $product_results = array();
		
		public $product_ids = array();
		
		public function __construct($parameter, $id, $searchStr="") {
		
		$this->product_results($parameter, $id, $searchStr);
		
		$this->set_product_ids();
		
		}
		
		public function set_product_ids() {
		
		for($i=0; $i<=count($this->product_results)-1; $i++) {
		
		$product_ids[] = $this->product_results[$i]['productId'];
		
		}

		$product_ids = array_unique($product_ids);
		$product_ids = implode('\',\'', $product_ids);
		
		$this->product_ids = $product_ids;
		
		}

		public function product_results($parameter="", $id="", $searchStr="") {
		
		global $db;
		
		if(!empty($searchStr)) {
		
		$this->product_results = $this->get_search_results($searchStr);
		
		}
		
		elseif(!empty($parameter) && !empty($id)) {
		
		if(is_array($id)) {
		
		$query = "SELECT productId FROM CubeCart_inventory WHERE $parameter IN (".$this->create_csv_id_list($id).")";
		
		}
		
		else {
		
		$query = "SELECT productId FROM CubeCart_inventory WHERE $parameter = $id";
		
		}
	
		$this->product_results = $db->select($query);
		
		}
		
		// Safeguard - if still no product results at this point, return false and don't fire the filter box.
		
		if(empty($this->product_results)) {
		
		return false;
		
		}
		
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