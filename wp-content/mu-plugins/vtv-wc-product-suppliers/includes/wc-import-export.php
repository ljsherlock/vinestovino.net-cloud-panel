<?php

/**
 * Custom product import column class. More columns can be easily added.
 * 
 * Goes hand-in-hand with WooCom_Export_Custom_Column.
 * 
 * https://github.com/woocommerce/woocommerce/wiki/Product-CSV-Importer-&-Exporter#adding-custom-import-columns-developers
 *
 * @class WooCom_Import_Custom_Column
 */
class WooCom_Import_Export_Custom_Column {

    /**
    * @var String $column_slug
    */
	public $column_slug = '';

	/**
    * @var String $column_name
    */
	public $column_name = '';

	/**
    * @var String $taxonomy
    */
	public $taxonomy = '';

	function __construct ($column_slug, $column_name, $taxonomy) {

		if ( isset( $column_slug, $column_name, $taxonomy ) ) {
			$this->column_slug = $column_slug;
			$this->column_name = $column_name;
			$this->taxonomy = $taxonomy;
		} else {
			return false;
		}

		add_filter( 'woocommerce_csv_product_import_mapping_default_columns', array( $this, 'add_column_to_mapping_screen' ) );
		add_filter( 'woocommerce_csv_product_import_mapping_options', array( $this, 'add_column_to_importer' ) );
		add_filter( 'woocommerce_product_import_pre_insert_product_object', array( $this, 'process_import' ), 10, 2 );

        // Export
        add_filter( 'woocommerce_product_export_column_names', array( $this, 'add_export_columns' ) );
		add_filter( 'woocommerce_product_export_product_default_columns', array( $this, 'add_export_columns' ) );
		add_filter( 'woocommerce_product_export_product_column_suppliers', array( $this, 'add_export_data_suppliers' ), 10, 2 );
	}
	
	/**
	 * Add automatic mapping support for 'Custom Column'. 
	 * This will automatically select the correct mapping for columns named 'Custom Column' or 'custom column'.
	 * 
	 * Add as many as you want.
	 *
	 * @param array $columns
	 * @return array $columns
	 */
	public function add_column_to_mapping_screen( $columns ) {
	
		// potential column name => column slug
		$columns[ $this->column_name ] = $this->column_slug;

		return $columns;
	}

	/**
	 * Register a 'Custom Column' column in the importer.
	 * 
	 * Add as many as you want.
	 *
	 * @param array $options
	 * @return array $options
	 */
	public function add_column_to_importer( $options ) {
		// column slug => column name
		$options[ $this->column_slug ] = $this->column_name;

		return $options;
	}

	/**
	 * Process the data read from the CSV file.
	 * 
	 * This function expects the custom taxonomy terms Suppliers and saves them to the product,
	 * but you can do anything you want here with the data.
	 *
	 * @param WC_Product $product_obj - Product being imported or updated.
	 * @param array $data - CSV data read for the product.
	 * 
	 * @return WC_Product $object
	 */
	function process_import( $product_obj, $data ) {

		if ( ! empty( $data[ $this->column_slug ] ) ) {

			// Convert comma-delimited string into array of strings
			$suppliers = explode( ",", $data[ $this->column_slug ] );

			// Create empty array to add terms to
			$terms_to_set = [];

			foreach ( $suppliers as $term ) {

				// Use term_exists to return the term object
				$term_exists = term_exists( $term, $this->taxonomy );

				// Addd term to the array.
				$terms_to_set[] = $term_exists['term_id'];
			}
			// Set the new selected terms for current product.
			wp_set_post_terms( $product_obj->get_id(), $terms_to_set, $this->taxonomy );
		} else {
			
			// if the column is empty and there IS currently values, if should erase it.
			// check if there are terms
			$term_obj_list = get_the_terms( $product_obj->get_id(), $this->taxonomy );

			if ( is_array( $term_obj_list ) ) {
				wp_set_post_terms( $product_obj->get_id(), '', $this->taxonomy );
			}
		}

		return $product_obj;
	}

    // ----------------------- EXPORT ----------------------------

	// https://stackoverflow.com/questions/68315439/woocommerce-product-export-add-multiple-meta-fields-using-function-add-export
	/**
	 * Add the custom columns to the exporter and the exporter column menu.
	 *
	 * @param array $columns
	 * @return array $columns
	 *
	 */
	public function add_export_columns( $columns ) {
		$columns[ $this->column_slug ] = $this->column_name;
		
		return $columns;
	}

	public function add_export_data_suppliers( $value, $product ) {

		$term_obj_list = get_the_terms( $product->get_id(), $this->taxonomy );

		if ( is_array( $term_obj_list ) ) {

			$terms_string = join(', ', wp_list_pluck($term_obj_list, 'name'));

			return $terms_string;
		}
		return '';
		
	}
}