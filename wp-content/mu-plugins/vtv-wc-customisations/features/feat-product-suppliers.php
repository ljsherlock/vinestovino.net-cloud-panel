<?php

include_once( realpath(  __DIR__ . '/../includes/wp-taxonomy.php' ) );
include_once( realpath(  __DIR__ . '/../includes/wc-import-export.php' )  );

class WC_Product_Suppliers {

    public function __construct () {

        add_action( 'init', array( $this, 'wp_create_suppliers_taxonomy' ), 0 );
        
        $this->add_suppliers_taxonomy_to_import_export ();
    }

    /**
     * Create the Suppliers taxonomy.
     */
    public function wp_create_suppliers_taxonomy () {
        $Suppliers_Taxonomy = new WP_Register_Taxonomy(
            'suppliers',
            'product',
            'Supplier',
            'Suppliers'
        );
        
        $Suppliers_Taxonomy->register_taxonomy();
    }

    /**
     * Add the Suppliers taxonomy to the WooCommerce import and export.
     */
    public function add_suppliers_taxonomy_to_import_export () {

        $WooCom_Import_Export_Custom_Column = new WooCom_Import_Export_Custom_Column(
            'suppliers',
            'Suppliers',
            'suppliers' // taxonomy
        );
    }
}