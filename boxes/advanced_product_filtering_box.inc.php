
<?php
// /*

// +--------------------------------------------------------------------------

// |	advanced_product_filtering_box.inc.php

// |   ========================================

// |	Advanced Product Filtering by Aare Laanesaar

// +--------------------------------------------------------------------------

// */

if (!defined('CC_INI_SET')) die('Access Denied');

/* Import classes and instantiate objects */
require_once('modules'.CC_DS.'3rdparty'.CC_DS.'Advanced_Product_Filtering'.CC_DS.'Advanced_Product_Filtering.php');

$current_url_query = $_SERVER['REQUEST_URI'];

$apf = new Advanced_Product_Filtering( $_GET , "I", $current_url_query);

if($apf->product_results->product_results) {

$box_content = new XTemplate ('boxes'.CC_DS.'advanced_product_filtering_box.tpl');

require_once('modules'.CC_DS.'3rdparty'.CC_DS.'Advanced_Product_Filtering'.CC_DS.'APF_URL.php');

$apf_url = new APF_URL($current_url_query);

/* Import classes and instantiate objects */

/* Get filters */
$cat 			= $apf->filters['cat'];
$type 			= $apf->filters['type'];
$brand 			= $apf->filters['brand'];
$finish 		= $apf->filters['finish'];
$price_ranges 	= $apf->filters['price_ranges'];
$fixing_centres = $apf->filters['fixing_centres'];

/* Get filters */

/* Filter box opening and closing tags */
$filter_box_open = "<div class=\"filter_box_container\">
<div class=\"filter_box_heading\">Filter Your Results
<div class=\"close_filter_box\"><a href=\"\" class=\"close_button\">--</a></div>
</div><ul class=\"filter_list\">";
$filter_box_close = "</ul></div>";
$box_content->assign('OPENING_TAGS', $filter_box_open);
$box_content->assign('CLOSING_TAGS', $filter_box_close);
/* Filter box opening and closing tags */

$box_content->parse('filter_box.filter_box_open');

/*  CATEGORIES  */
if($apf->count['cat'] > 0) {

if(!empty($cat[0]['cat_id'])) {

$box_content->parse('filter_box.category_filters_open');

}

for($i=0; $i<=$apf->count['cat']-1; $i++) {
	
$link = $apf_url->create_link("cat_id[]", $cat[$i]['cat_id']);

$prods_per_cat = $apf->count_prods("cat_id", $cat[$i]['cat_id'], false);

$box_content->assign('CAT_PRODS_NO', "(".$prods_per_cat[0]['product_count'].")");

$box_content->assign('CAT', $cat[$i]);

$box_content->assign('CAT_LINK', $link);

$filter_class = $apf_url->generate_filter_class("cat_id[]", $cat[$i]['cat_id']);

$box_content->assign('FILTER_CLASS', $filter_class);

$box_content->parse('filter_box.cat_filters');

}

if(!empty($cat[0]['cat_id'])) {

$box_content->parse('filter_box.category_filters_close');

}

}
/*  CATEGORIES  */

if($apf->showAllFilters()) {

/*  BRANDS  */
if($apf->count['brand'] > 0) {

if(!empty($brand[0]['brand_id'])) {

$box_content->parse('filter_box.brand_filters_open');

}

for($i=0; $i<=$apf->count['brand']-1; $i++) {

$link = $apf_url->create_link("brand_id[]", $brand[$i]['brand_id']);

$prods_per_brand = $apf->count_prods("brand_id", $brand[$i]['brand_id']);

if($prods_per_brand[0]['product_count'] > 0) {

$box_content->assign('BRAND_PRODS_NO', "(".$prods_per_brand[0]['product_count'].")");

$box_content->assign('BRAND', $brand[$i]);

$box_content->assign('BRAND_LINK', $link);

$filter_class = $apf_url->generate_filter_class("brand_id[]", $brand[$i]['brand_id']);

$box_content->assign('FILTER_CLASS', $filter_class);

$box_content->parse('filter_box.brand_filters');

}

}

if(!empty($brand[0]['brand_id'])) {

$box_content->parse('filter_box.brand_filters_close');

}

}
/*  BRANDS  */

/* TYPE  */
if($apf->count['type'] > 0) {

if(!empty($type[0]['type']) || !empty($type[1]['type'])) {

$box_content->parse('filter_box.type_filters_open');

}

for($i=0; $i<=$apf->count['type']-1; $i++) {

if(!empty($type[$i]['type'])) {

$prods_per_type = $apf->count_prods("type", $type[$i]['type']);

if($prods_per_type[0]['product_count'] > 0) {

$box_content->assign('TYPES_NO', "(".$prods_per_type[0]['product_count'].")");

$link = $apf_url->create_link("type[]", $type[$i]['type']);

$box_content->assign('TYPE', $type[$i]);

$box_content->assign('TYPE_LINK', $link);

$filter_class = $apf_url->generate_filter_class("type[]", $type[$i]['type']);

$box_content->assign('FILTER_CLASS', $filter_class);

$box_content->parse('filter_box.type_filters');

}

}

}

if(!empty($type[0]['type']) || !empty($type[1]['type'])) {

$box_content->parse('filter_box.type_filters_close');

}

}
/* TYPE  */

/* FINISH  */
if($apf->count['finish'] > 0) {

if(!empty($finish[0]['finish']) || !empty($finish[1]['finish'])) {

$box_content->parse('filter_box.finish_filters_open');

}

for($i=0; $i<=$apf->count['finish']-1; $i++) {

if(!empty($finish[$i]['finish'])) {

$prods_per_finish = $apf->count_prods("finish", $finish[$i]['finish']);

if($prods_per_finish[0]['product_count'] > 0) {

$box_content->assign('FINISHES_NO', "(".$prods_per_finish[0]['product_count'].")");

$link = $apf_url->create_link("finish[]", $finish[$i]['finish']);

$box_content->assign('FINISH', $finish[$i]);

$box_content->assign('FINISH_LINK', $link);

$filter_class = $apf_url->generate_filter_class("finish[]", $finish[$i]['finish']);

$box_content->assign('FILTER_CLASS', $filter_class);

$box_content->parse('filter_box.finish_filters');

}

}

}

if(!empty($finish[0]['finish']) || !empty($finish[1]['finish'])) {

$box_content->parse('filter_box.finish_filters_close');

}

}
/* FINISH  */

/* FIXING CENTRES  */
if($apf->count['fixing_centres'] > 0) {

if(!empty($fixing_centres[0]['fixing_centres']) || !empty($fixing_centres[1]['fixing_centres'])) {

$box_content->parse('filter_box.fixing_centres_open');

}

for($i=0; $i<=$apf->count['fixing_centres']-1; $i++) {

if(!empty($fixing_centres[$i]['fixing_centres'])) {

$prods_per_fixing_centres = $apf->count_prods("fixing_centres", $fixing_centres[$i]['fixing_centres']);

if($prods_per_fixing_centres[0]['product_count'] > 0) {

$box_content->assign('FIXING_CENTRES_NO', "(".$prods_per_fixing_centres[0]['product_count'].")");

$link = $apf_url->create_link("fixing_centres[]", $fixing_centres[$i]['fixing_centres']);

$box_content->assign('FIXING_CENTRES', $fixing_centres[$i]);

$box_content->assign('FIXING_CENTRES_LINK', $link);

$filter_class = $apf_url->generate_filter_class("fixing_centres[]", $fixing_centres[$i]['fixing_centres']);

$box_content->assign('FILTER_CLASS', $filter_class);

$box_content->parse('filter_box.fixing_centres_filters');

}

}

}

if(!empty($fixing_centres[0]['fixing_centres']) || !empty($fixing_centres[1]['fixing_centres'])) {

$box_content->parse('filter_box.fixing_centres_close');

}

}
/* FIXING CENTRES  */


/* PRICE_RANGES  */
if($apf->count['price_range'] > 0) {

if(!empty($price_ranges[0]['price_range'])) {

$box_content->parse('filter_box.price_filters_open');

}

for($i=0; $i<=$apf->count['price_range']-1; $i++) {

$prods_per_price = $apf->count_prods("price_range", $price_ranges[$i]['range_id']);

if($prods_per_price[0]['product_count'] > 0) {

$box_content->assign('PRICE_PRODS_NO', "(".$prods_per_price[0]['product_count'].")");

$link = $apf_url->create_link("price_range[]", $price_ranges[$i]['range_id']);

$box_content->assign('PRICE_RANGE', $price_ranges[$i]);

$box_content->assign('PRICE_RANGE_LINK', $link);

$filter_class = $apf_url->generate_filter_class("price_range[]", $price_ranges[$i]['range_id']);

$box_content->assign('FILTER_CLASS', $filter_class);

$box_content->parse('filter_box.price_range_filters');

}

}

if(!empty($price_ranges[0]['price_range'])) {

$box_content->parse('filter_box.price_filters_close');

}

}
/* PRICE_RANGES  */

/* STOCKED PRODUCTS */
$number_most_pop = $apf->count_prods( array( "pop" => "Y", "stock_level" => array( ">" => "0" ) ) );

if($number_most_pop[0]['product_count'] > 0) {

$most_popular = $apf_url->create_link("pop", "Y");

$box_content->assign('MOST_POPULAR_LINK', $most_popular);

$filter_class = $apf_url->generate_filter_class("pop", "Y");

$box_content->assign('FILTER_CLASS', $filter_class);

$box_content->assign('POP_PRODS_NO', "(".$number_most_pop[0]['product_count'].")");

$box_content->parse('filter_box.other_filters');

}
/* STOCKED PRODUCTS */

} 

/* CLEAR ALL FILTERS */
if($apf_url->has_parameters($current_url_query)) {

$clear_filters = $apf_url->remove_all_parameters($current_url_query);

$box_content->assign('CLEAR_FILTERS', '<a href="'.$clear_filters.'">clear all filters</a>');

$box_content->parse('filter_box.clear_all_filters');

}
/* CLEAR ALL FILTERS */

$box_content->parse('filter_box.filter_box_close');

$box_content->parse('filter_box');

$box_content = $box_content->text('filter_box');

}

?>