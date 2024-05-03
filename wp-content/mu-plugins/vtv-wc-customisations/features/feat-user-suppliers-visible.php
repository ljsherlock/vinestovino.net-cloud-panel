<?php

/**
 * Feature: User Suppliers Visible 
 * 
 * - [x] Toggle suppliers visible user from the profile edit page.
 * - [ ] Show and hide products with matching suppliers 
 */

class User_Suppluers_Visible {
    
    public function __construct () {
        /**
         * View profile page of any user.
         * Confirm fields are rendered
         */
        add_filter( 'show_user_profile', array( $this, 'add_html_profile_fields' ) );
        add_filter( 'edit_user_profile', array( $this, 'add_html_profile_fields' ) );

        /**
         * View profile page of any user.
         * Change all fields
         * Confirm that changes have been saved.
         */
        add_filter( 'personal_options_update', array( $this, 'save_html_profile_fields' ) );
        add_filter( 'edit_user_profile_update', array( $this, 'save_html_profile_fields' ) );      
        
        
        add_filter( 'pre_get_posts', array( $this, 'exclude_single_posts_home' ) );      
    }

    /**
     * @param User_Object $user 
     * 
     * @return void 
     */
    public function add_html_profile_fields ( $user ) {

        $suppliers = get_terms( array(
            'taxonomy'   => 'suppliers',
            'hide_empty' => false
        ) );

        $suppliers_visible = explode(', ', get_user_meta( $user->ID, 'visible_suppliers', true ) );

        ?>
        <h3><?php _e("Suppliers Visible", "blank"); ?></h3>
        <p>A checked supplier is visible and visa versa.</p>
    
        <table class="form-table" style="max-width 300px;">
            <tr>
                <td> 
                    <?php

                    foreach ( $suppliers as $supplier ) {
                    ?>
                        <label style="margin:0 8px 8px 0; display: inline-block;"> 
                                <input
                                    type="checkbox"
                                    name="suppliers[]"
                                    value="<?= $supplier->name ?>"
                                    <?= ( ! in_array($supplier->name, $suppliers_visible) ? 'checked="checked"' : "") ?>
                                    />
                                <?= $supplier->name ?> 
                        </label>
                
                    <?php } ?>
                </td>
            </tr>
        </table>
        <?php 
    }

    /**
     * @param int $user_id 
     * 
     * @return void | false
     */
    public function save_html_profile_fields ( $user_id ) {
    
        if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
            return;
        }
        
        if ( ! current_user_can( 'edit_user', $user_id ) ) { 
            return false; 
        }

        if( is_array( $_POST['suppliers'] ) ) {

            $suppliers = get_terms( array(
                'taxonomy'   => 'suppliers',
                'hide_empty' => false
            ) );

            $terms_string = wp_list_pluck($suppliers, 'name');
            $terms_to_add = [];

            foreach ( $terms_string as $term ) {

                if ( ! in_array( $term, $_POST['suppliers'] )) {
                    $terms_to_add[] = $term;
                }
            }

            update_user_meta( $user_id, 'visible_suppliers', implode( ', ', $terms_to_add ) );
        } else {
            update_user_meta( $user_id, 'visible_suppliers', '' );
        }
    }

    public function get_user_hidden_terms ( $user_id ) {

        return get_user_meta( $user_id, 'visible_suppliers', true );
    }

    function exclude_single_posts_home ( $query ) {

        if ( is_admin() ) {
            return;
        }

        if( $query->is_main_query() ) {
            // && isset($query->query['post_type']) 
            // && $query->query['post_type'] === 'product' 

            $current_user_id = get_current_user_id();
            $terms = explode(', ', $this->get_user_hidden_terms( $current_user_id ) );
            
            $add_to_tax_query = array(
                array(
                    "taxonomy" => "suppliers",
                    "field" => "slug",
                    "terms" => $terms,
                    "operator" => "NOT IN",
                ), 
            );

            $query->set( 'tax_query', $add_to_tax_query );
        }

        return $query;
    }
        

}

$User_Suppluers_Visible =  new User_Suppluers_Visible();
