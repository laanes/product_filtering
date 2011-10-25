<?php
// /*

// +--------------------------------------------------------------------------

// |	advanced_product_filtering_box.inc.php

// |   ========================================

// |	Advanced Product Filtering

// +--------------------------------------------------------------------------

// */

if (!defined('CC_INI_SET')) die('Access Denied');

$box_content = new XTemplate ('boxes'.CC_DS.'advanced_product_filtering_box.tpl');

require_once('modules'.CC_DS.'3rdparty'.CC_DS.'Advanced_Product_Filtering'.CC_DS.'APF_DB_CAT.php');
require_once('modules'.CC_DS.'3rdparty'.CC_DS.'Advanced_Product_Filtering'.CC_DS.'APF_URL.php');

$current_url_query = $_SERVER['REQUEST_URI'];

$apf = new APF_DB_CAT($_GET, "I", $_REQUEST['catId']);
$apfu = new APF_URL($current_url_query);

/* Get filters from database */

$brand = $apf->get_brands();

$cat = $apf->get_categories();

$price_ranges = $apf->get_price_ranges();

$type = $apf->get_type();

$finish = $apf->get_finish();

$fixing_centres = $apf->get_fixing_centres();

$product_results = $apf->product_results;

/* Get filters from database */

if(!empty($product_results)) {

/* CATEGORY */
$max_cats = count($cat);

$box_content->parse('filter_box.cat_filters_heading');

for($i=0; $i<=$max_cats-1; $i++) {

$link = $apfu->create_link("cat_id", $cat[$i]['cat_id'], $i);
$prods_per_cat = $apf->count_prods("cat_id", $cat[$i]['cat_id']);

$box_content->assign('CAT_PRODS_NO', "(".$prods_per_cat[0]['product_count'].")");

$box_content->assign('CAT', $cat[$i]);

$box_content->assign('CAT_LINK', $link);

// $filter_class = $apfu->generate_filter_class("cat_id", $cat[$i]['cat_id'], $i);

$filter_class = "active_filter";

$box_content->assign('FILTER_CLASS', $filter_class);

$box_content->parse('filter_box.cat_filters');

}
/* CATEGORY */


/* BRAND */
$max_brands = count($brand);

$box_content->parse('filter_box.brand_filters_heading');

for($i=0; $i<=$max_brands-1; $i++) {

$link = $apfu->create_link("brand_id", $brand[$i]['brand_id'], $i);
$prods_per_brand = $apf->count_prods("brand_id", $brand[$i]['brand_id']);
if($prods_per_brand[0]['product_count'] > 0) {

$box_content->assign('BRAND_PRODS_NO', "(".$prods_per_brand[0]['product_count'].")");

$box_content->assign('BRAND', $brand[$i]);

$box_content->assign('BRAND_LINK', $link);

$filter_class = $apfu->generate_filter_class("brand_id", $brand[$i]['brand_id'], $i);

$box_content->assign('FILTER_CLASS', $filter_class);

$box_content->parse('filter_box.brand_filters');
}

}
/* BRAND */


/* TYPE  */

if(count($type) > 0) {

$box_content->parse('filter_box.type_filters_heading');

for($i=0; $i<=count($type)-1; $i++) {

if(!empty($type[$i]['type'])) {

$prods_per_type = $apf->count_prods("type", $type[$i]['type']);

if($prods_per_type[0]['product_count'] > 0) {

$box_content->assign('TYPES_NO', "(".$prods_per_type[0]['product_count'].")");

$link = $apfu->create_link("type", $type[$i]['type']);

$box_content->assign('TYPE', $type[$i]);

$box_content->assign('TYPE_LINK', $link);

$filter_class = $apfu->generate_filter_class("type", $type[$i]['type']);

$box_content->assign('FILTER_CLASS', $filter_class);

$box_content->parse('filter_box.type_filters');

}

}

}

}
/* TYPE  */

/* FINISH  */

if(count($finish) > 0) {

$box_content->parse('filter_box.finish_filters_heading');

for($i=0; $i<=count($finish)-1; $i++) {

if(!empty($finish[$i]['finish'])) {

$prods_per_finish = $apf->count_prods("finish", $finish[$i]['finish']);

if($prods_per_finish[0]['product_count'] > 0) {

$box_content->assign('FINISHES_NO', "(".$prods_per_finish[0]['product_count'].")");

$link = $apfu->create_link("finish", $finish[$i]['finish']);

$box_content->assign('FINISH', $finish[$i]);

$box_content->assign('FINISH_LINK', $link);

$filter_class = $apfu->generate_filter_class("finish", $finish[$i]['finish']);

$box_content->assign('FILTER_CLASS', $filter_class);

$box_content->parse('filter_box.finish_filters');

}

}

}

}
/* FINISH  */


/* FIXING CENTRES  */

if(count($fixing_centres) > 0) {

$box_content->parse('filter_box.fixing_centres_filters_heading');

for($i=0; $i<=count($fixing_centres)-1; $i++) {

if(!empty($fixing_centres[$i]['fixing_centres'])) {

$prods_per_fixing_centres = $apf->count_prods("fixing_centres", $fixing_centres[$i]['fixing_centres']);

if($prods_per_fixing_centres[0]['product_count'] > 0) {

$box_content->assign('FIXING_CENTRES_NO', "(".$prods_per_fixing_centres[0]['product_count'].")");

$link = $apfu->create_link("fixing_centres", $fixing_centres[$i]['fixing_centres']);

$box_content->assign('FIXING_CENTRES', $fixing_centres[$i]);

$box_content->assign('FIXING_CENTRES_LINK', $link);

$filter_class = $apfu->generate_filter_class("fixing_centres", $fixing_centres[$i]['fixing_centres']);

$box_content->assign('FILTER_CLASS', $filter_class);

$box_content->parse('filter_box.fixing_centres_filters');

}

}

}

}
/* FIXING CENTRES  */


/* PRICE_RANGES */
for($i=0; $i<=count($price_ranges)-1; $i++) {
$prods_per_price = $apf->count_prods("price_range", $price_ranges[$i]['range_id']);
if($prods_per_price[0]['product_count'] > 0) {
$box_content->assign('PRICE_PRODS_NO', "(".$prods_per_price[0]['product_count'].")");

$link = $apfu->create_link("price_range", $price_ranges[$i]['range_id'], $i);
$box_content->assign('PRICE_RANGE', $price_ranges[$i]);
$box_content->assign('PRICE_RANGE_LINK', $link);
$filter_class = $apfu->generate_filter_class("price_range", $price_ranges[$i]['range_id'], $i);
$box_content->assign('FILTER_CLASS', $filter_class);
$box_content->parse('filter_box.price_range_filters');
}
}
$number_most_pop = $apf->count_prods("pop", "Y");
if($number_most_pop[0]['product_count'] > 0) {
$most_popular = $apfu->create_link("pop", "Y");
$box_content->assign('MOST_POPULAR_LINK', $most_popular);
$filter_class = $apfu->generate_filter_class("pop", "Y");
$box_content->assign('FILTER_CLASS', $filter_class);
$box_content->assign('POP_PRODS_NO', $number_most_pop[0]['product_count']);
$box_content->parse('filter_box.other_filters');
}
if($apfu->has_parameters($current_url_query)) {
$clear_filters = $apfu->remove_all_parameters($current_url_query);
$box_content->assign('CLEAR_FILTERS', '<a href="'.$clear_filters.'">clear all filters</a>');
$box_content->parse('filter_box.clear_all_filters');
}
$box_content->parse('filter_box');
}
$box_content = $box_content->text('filter_box');

?>