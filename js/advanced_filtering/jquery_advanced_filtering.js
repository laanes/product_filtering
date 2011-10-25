$(document).ready(function(){

if(jQuery('.active_filter').length > 0) {

var filters = ["category", "brand", "type", "finish", "fixing_centres"];

jQuery.each(filters, function(index, value) {

jQuery('.' + value + '_filter .active_filter').prependTo('.' + value + '_filter');

});

}

var filter_box_elements = jQuery('.filter_list li ul li');

jQuery.each(filter_box_elements, function(index, value) {

if ( filter_box_elements[index].innerHTML === "" ) {

filter_box_elements[index].remove();

}

});

/*  SHOW MORE / LESS CATEGORIES */

var category_filters = jQuery('.category_filter').find('ul li');

if(category_filters.length > 6 && jQuery('.active_filter').length > 0) {

for(i=6;i<=category_filters.length-1;i++) 

{jQuery(category_filters[i]).addClass('hiddenFilter');

}

jQuery('.category_filter ul').append('<li class="view_more_cats">more categories</li>');

}

jQuery('.view_more_cats').click(function(){

for(i=6;i<=category_filters.length-1;i++) {

jQuery(category_filters[i]).toggleClass('hiddenFilter');

}

var hidden_filter = jQuery('.category_filter').find('.hiddenFilter');

if(hidden_filter.length > 0) {

jQuery(this).html('more categories');

}

else {

jQuery(this).html('less categories');

}

});

/*  SHOW MORE / LESS CATEGORIES */


/*  SHOW MORE / LESS BRANDS */

var brand_filters = jQuery('.brand_filter').find('ul li');

if(brand_filters.length > 6) {

for(i=6;i<=brand_filters.length-1;i++) 

{jQuery(brand_filters[i]).addClass('hiddenFilter');

}

jQuery('.brand_filter ul').append('<li class="view_more_brands">more brands</li>');

}

jQuery('.view_more_brands').click(function(){

for(i=6;i<=brand_filters.length-1;i++) {

jQuery(brand_filters[i]).toggleClass('hiddenFilter');

}

var hidden_filter = jQuery('.brand_filter').find('.hiddenFilter');

if(hidden_filter.length > 0) {

jQuery(this).html('more brands');

}

else {

jQuery(this).html('less brands');

}

});

/*  SHOW MORE / LESS BRANDS */

/*  SHOW MORE / LESS TYPES */

var type_filters = jQuery('.type_filter').find('ul li');

if(type_filters.length > 6) {

for(i=6;i<=type_filters.length-1;i++) 

{jQuery(type_filters[i]).addClass('hiddenFilter');

}

jQuery('.type_filter ul').append('<li class="view_more_types">more types</li>');

}

jQuery('.view_more_types').click(function(){

for(i=6;i<=type_filters.length-1;i++) {

jQuery(type_filters[i]).toggleClass('hiddenFilter');

}

var hidden_filter = jQuery('.type_filter').find('.hiddenFilter');

if(hidden_filter.length > 0) {

jQuery(this).html('more types');

}

else {

jQuery(this).html('less types');

}

});

/*  SHOW MORE / LESS TYPES */

/*  SHOW MORE / LESS FINISHES */

var finish_filters = jQuery('.finish_filter').find('ul li');

if(finish_filters.length > 6) {

for(i=6;i<=finish_filters.length-1;i++) 

{jQuery(finish_filters[i]).addClass('hiddenFilter');

}

jQuery('.finish_filter ul').append('<li class="view_more_finishes">more finishes</li>');

}

jQuery('.view_more_finishes').click(function(){

for(i=6;i<=finish_filters.length-1;i++) {

jQuery(finish_filters[i]).toggleClass('hiddenFilter');

}

var hidden_filter = jQuery('.finish_filter').find('.hiddenFilter');

if(hidden_filter.length > 0) {

jQuery(this).html('more finishes');

}

else {

jQuery(this).html('less finishes');

}

});

/*  SHOW MORE / LESS FINISHES */



jQuery('.close_button').click(function(event){
event.preventDefault();
// Prevent the event from being notified
event.stopPropagation();
jQuery('.filter_box_container').toggleClass('filter_box_container_fixed');
jQuery('.close_filter_box_relative').toggleClass('close_filter_box');
});
jQuery('.filter_list_heading').click(function(){
jQuery(this).next()
.slideToggle();
});	
});