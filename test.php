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
  static function make_labels($singular,$plural=false,$args=array()) {
    if ($plural===false)
      $plural = $singular . 's';
    elseif ($plural===true)
      $plural = $singular;
    $defaults = array(
      'name'              =>_x($plural,'post type general name'),
      'singular_name'      =>_x($singular,'post type singular name'),
      'add_new'            =>_x('Add New',$singular),
      'add_new_item'      =>__("Add New $singular"),
      'edit_item'          =>__("Edit $singular"),
      'new_item'          =>__("New $singular"),
      'view_item'          =>__("View $singular"),
      'search_items'      =>__("Search $plural"),
      'not_found'          =>__("No $plural Found"),
      'not_found_in_trash'=>__("No $plural Found in Trash"),
      'parent_item_colon' =>'',
    );
    return wp_parse_args($args,$defaults);
  }
  static function action_add_meta_boxes_post($post) {
    add_meta_box(
      'little-promo-boxes',   // Metabox Name, used as the "id" for a wrapping div
      'Little Promo Boxes',   // Metabox Title, visible to the user
      array(__CLASS__,'the_little_promo_boxes_metabox'), // Callback function
      'post',                 // Add to the Edit screen for Post Types of 'post'  
      'side',                 // Show it in the sidebar (if center then it would be 'normal'
      'low'                   // Show it below metaboxes that specify 'high'
    );
  }
  static function the_little_promo_boxes_metabox($post) {
    $pto = get_post_type_object('promo-box');
    $default_options = array(
      'post_type' => 'promo-box',
      'show_option_none' => "Select a {$pto->labels->singular_name}",
    );
    $promo_boxes = get_post_meta($post->ID,'_promo_boxes',true);
    for($i=0; $i<=2; $i++) {
      wp_dropdown_pages(array_merge($default_options,array(
        'id'       => "promo_box_{$i}",
        'name'     => 'promo_boxes[]',
        'selected' => (empty($promo_boxes[$i]) ? 0 : $promo_boxes[$i]),
      )));
    }
  }
  static function filter_wp_insert_post_data($data, $postarr) {
    update_post_meta($postarr['ID'],'_promo_boxes',$postarr['promo_boxes']);
    return $data;
  }
  static function get_promo_boxes($post=false) {
    static $promo_boxes=array();
    if (!$post)
      $post = $GLOBALS['post'];
    if (!isset($promo_boxes[$post->ID])) {
      $promo_boxes[$post->ID] = get_post_meta($post->ID,'_promo_boxes',true);
      $index = 0;
      foreach($promo_boxes[$post->ID] as $promo_box_id) {
        $promo_boxes[$post->ID][$index++] = (is_numeric($promo_box_id) ? get_post($promo_box_id) : false);
      }
    }
    return $promo_boxes[$post->ID];
  }
  static function get_promo_box($number,$post=false) {
    $promo_boxes = self::get_promo_boxes($post);
    return $promo_boxes[$number-1];
  }
}
LittlePromoBoxes::on_load();


?>