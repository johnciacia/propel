<?php

class LittlePromoBoxes {
  static function on_load() {
    add_action('init',array(__CLASS__,'action_init'));
    add_action('add_meta_boxes_post',array(__CLASS__,'action_add_meta_boxes_post'));
    add_filter('wp_insert_post_data',array(__CLASS__,'filter_wp_insert_post_data'),10,2);
  }
  
  static function action_init() {
    register_post_type('promo-box',array(
      'labels'          => self::make_labels('Promo Box','Promo Boxes'),
      'public_queryable'=> false,
      'hierarchical'    => true,  // IMPORTANT!!! wp_dropdown_pages() requires 'hierarchical'=>true
      'show_ui'         => true,
      'query_var'       => false,
      'supports'        => array('title','editor','thumbnail','custom-fields'),
      'show_in_nav_menus'=>true,
      'exclude_from_search'=>true,
    ));
  }

}
?>