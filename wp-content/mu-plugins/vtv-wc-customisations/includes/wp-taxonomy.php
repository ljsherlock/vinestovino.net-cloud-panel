<?php

class WP_Register_Taxonomy {

        /**
    * @var String $name Post type name
    */
    protected $name = '';

    /**
    * @var String $singular Nice Singular name
    */
    protected $singular = '';

    /**
    * @var String $plural Nice plural name
    */
    protected $plural = '';

    /**
    *   @var Array $args Query arguments
    */
    public $args = '';

    /**
    *   @var Array $args Query arguments
    */
    public $labels = '';


    function __construct ($name, $post_type, $singular, $plural) {

        $this->name = $name;
        $this->post_type = $post_type;
        $this->singular = $singular;
        $this->plural = $plural;

        $this->labels = array(
            'name' => __( $this->plural, 'taxonomy general name' ),
            'singular_name' => __( 'Topic', 'taxonomy singular name' ),
            'search_items' =>  __( "Search {$this->plural} " ),
            'popular_items' => __( "Popular {$this->plural}" ),
            'all_items' => __( "All {$this->plural}" ),
            'parent_item' => null,
            'parent_item_colon' => null,
            'edit_item' => __( "Edit {$this->singular}" ), 
            'update_item' => __("'Update {$this->singular}" ),
            'add_new_item' => __( "Add New {$this->singular}" ),
            'new_item_name' => __( "New {$this->singular} Name" ),
            'separate_items_with_commas' => __( "Separate {$this->name} with commas" ),
            'add_or_remove_items' => __( "Add or remove {$this->name}" ),
            'choose_from_most_used' => __( "Choose from the most used {$this->name}"),
            'menu_name' => __( $this->plural ),
        );

        $this->args = array( 
            'hierarchical' => true,
            'labels' => $this->labels,
            'show_ui' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'show_in_quick_edit' => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var' => true,
            'rewrite' => array( 'slug' => $this->name ),
        );
    }

    public function register_taxonomy () {
        register_taxonomy(
            $this->name, 
            $this->post_type, 
            $this->args
        );
    }
}