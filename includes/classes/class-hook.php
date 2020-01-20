<?php
/*
* @Author 		Pluginbazar
* Copyright: 	2015 Pluginbazar
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access

class VSWOO_Hooks {


	/**
	 * VSWOO_Hooks constructor.
	 */
	function __construct() {

		add_action( 'admin_init', array( $this, 'add_attribute_taxonomy_fields' ) );
		add_action( 'admin_notices', array( $this, 'plugin_dependency_check' ), 10 );
		add_filter( 'product_attributes_type_selector', array( $this, 'add_product_attribues_types' ), 10, 1 );
		add_action( 'woocommerce_product_option_terms', array( $this, 'product_option_terms' ), 10, 2 );
		add_filter( 'woocommerce_dropdown_variation_attribute_options_html', array(
			$this,
			'attribute_options_html'
		), 10, 2 );
		add_action( 'wp_footer', array( $this, 'dynamic_styles_content' ) );
		add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'loop_add_to_cart_link' ), 10, 2 );

		add_filter( 'woocommerce_admin_meta_boxes_prepare_attribute', array(
			$this,
			'save_attribute_meta_box'
		), 10, 3 );
		add_action( 'woocommerce_after_product_attribute_settings', array( $this, 'product_attribute_meta' ), 10, 2 );

		if ( class_exists( 'WooCommerce' ) ) {
			add_filter( 'activated_plugin', array( $this, 'plugin_activation' ), 10, 1 );
			add_filter( 'plugin_action_links_' . VSWOO_PLUGIN_FILE, array( $this, 'add_plugin_actions' ), 10, 1 );
		}

		add_filter( 'plugin_row_meta', array( $this, 'add_quick_links' ), 10, 2 );
		add_filter( 'plugin_action_links_' . VSWOO_PLUGIN_FILE, array( $this, 'add_quick_action_links' ), 10, 1 );
	}

	function add_quick_action_links( $links ) {

		$action_links = array(
			'responses' => sprintf( __( '<a href="%s">View Responses</a>', VSWOO_TEXTDOMAIN ), admin_url( 'admin.php?page=cf7-responses' ) ),
			'export'    => sprintf( __( '<a href="%s">Export</a>', VSWOO_TEXTDOMAIN ), admin_url( 'admin.php?page=cf7-responses&tab=export' ) ),
		);

		return array_merge( $action_links, $links );
	}


	/**
	 * Add quick links to the plugin list page
	 *
	 * @param $links
	 * @param $file
	 *
	 * @return array
	 */
	function add_quick_links( $links, $file ) {

		if ( VSWOO_PLUGIN_FILE === $file ) {

			$row_meta = array(
				'demo'    => sprintf( '<a class="quick-link-demo" href="%s">%s</a>',
					esc_url_raw( 'https://demo.pluginbazar.com/variation-swatches/' ),
					esc_html__( 'Try Demo', VSWOO_TEXTDOMAIN )
				),
				'support' => sprintf( '<a class="quick-link-support" href="%s">%s</a>',
					esc_url_raw( 'https://help.pluginbazar.com/forums/forum/variation-swatches-for-woocommerce/' ),
					esc_html__( 'Problem? Ask Direct Support', VSWOO_TEXTDOMAIN )
				),
				'pro'     => sprintf( '<a class="quick-link-pro" href="%s">%s</a>',
					esc_url_raw( 'https://codecanyon.net/item/variation-swatches-for-woocommerce/25116319' ),
					esc_html__( 'Get Pro', VSWOO_TEXTDOMAIN )
				),
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}


	/**
	 * Product Attribute Meta
	 *
	 * @param WC_Product_Attribute $attribute
	 * @param $i
	 */
	function product_attribute_meta( \WC_Product_Attribute $attribute, $i ) {

		if ( $attribute->is_taxonomy() ) {

			printf( '<tr><td colspan="2"><p class="vswoo-notice vswoo-notice-info">%s <a href="%s">%s</a></p></td></tr>',
				esc_html__( 'Please manage attributes from global scope.', VSWOO_TEXTDOMAIN ),
				esc_url( admin_url( 'edit.php?post_type=product&page=product_attributes' ) ),
				esc_html__( 'Update Attributes', VSWOO_TEXTDOMAIN ) );

			return;
		}

		include VSWOO_PLUGIN_DIR . 'templates/product-attribute-meta.php';
	}


	function save_attribute_meta_box( \ WC_Product_Attribute $attribute, $data, $i ) {

		if ( $attribute->is_taxonomy() ) {
			return $attribute;
		}

		$attribute_type           = isset( $data['attribute_type'][ $i ] ) ? sanitize_title( $data['attribute_type'][ $i ] ) : '';
		$attribute_names          = isset( $data['attribute_names'][ $i ] ) ? sanitize_title( $data['attribute_names'][ $i ] ) : '';
		$product_id               = isset( $data['post_ID'] ) && ! empty( $post_id = $data['post_ID'] ) ? $post_id : sanitize_text_field( $_POST['post_id'] );
		$vswoo_product_attributes = get_post_meta( $product_id, 'vswoo_product_attributes', true );
		$vswoo_product_attributes = empty( $vswoo_product_attributes ) ? array() : $vswoo_product_attributes;

		$attribute_options_val = isset( $data['attribute_options_val'][ $i ] ) ? $data['attribute_options_val'][ $i ] : array();

		if ( ! empty( $attribute_type ) ) {
			$vswoo_product_attributes[ $attribute_names ][ $i ]['type']  = $attribute_type;
			$vswoo_product_attributes[ $attribute_names ][ $i ]['value'] = $attribute_options_val;
		}

		update_post_meta( $product_id, 'vswoo_product_attributes', $vswoo_product_attributes );

		return $attribute;
	}


	/**
	 * Add Settings Menu at Plugin Menu
	 *
	 * @param $links
	 *
	 * @return array
	 */
	function add_plugin_actions( $links ) {

		$action_links = array(
			'settings' => sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=advanced-variation' ), esc_html__( 'Settings', VSWOO_TEXTDOMAIN ) ),
		);

		return array_merge( $action_links, $links );
	}


	/**
	 * On Plugin Activation Redirect to Settings Page
	 *
	 * @param $plugin
	 */
	function plugin_activation( $plugin ) {

		if ( $plugin == VSWOO_PLUGIN_FILE ) {
			exit( wp_redirect( admin_url( 'admin.php?page=advanced-variation' ) ) );
		}
	}


	/**
	 * Change the Add to cart Link on Shop Page
	 */
	function loop_add_to_cart_link( $add_to_cart_text, $product ) {

		if ( ! $product->is_type( 'variable' ) ) {
			return $add_to_cart_text;
		}

		$add_to_cart_text = preg_replace( '/href="[^"]*"/', '', $add_to_cart_text );

		return str_replace( array( '<a' ), array( '<a href="#vswoo-popup" data-effect="mfp-zoom-in"' ), $add_to_cart_text );
	}


	/**
	 * Dynamic Styling and Dynamic Content for Popup
	 */
	function dynamic_styles_content() {

		vswoo_get_template( 'dynamic-content.php' );
	}


	/**
	 * Display Attribute options HTML frontend
	 *
	 * @param $html
	 * @param $args
	 *
	 * @return false|string
	 */
	function attribute_options_html( $html, $args ) {

		ob_start();

		vswoo_get_template( 'single-variations.php', $args );

		return ob_get_clean();
	}


	/**
	 * Add Product Options
	 *
	 * @param $attribute_taxonomy
	 * @param $i
	 */
	function product_option_terms( $attribute_taxonomy, $i ) {

		global $thepostid;

		$attribute_taxonomies = array_map( function ( $attribute ) {
			return $attribute->attribute_type;
		}, wc_get_attribute_taxonomies() );

		if ( in_array( $attribute_taxonomy->attribute_type, $attribute_taxonomies ) && $attribute_taxonomy->attribute_type != 'select' ) {

			$taxonomy   = wc_attribute_taxonomy_name( $attribute_taxonomy->attribute_name );
			$product_id = is_null( $thepostid ) && isset( $_POST['post_id'] ) ? absint( sanitize_text_field( $_POST['post_id'] ) ) : $thepostid;
			$args       = array( 'orderby' => 'name', 'hide_empty' => 0, );

			?>
            <select multiple="multiple"
                    data-placeholder="<?php esc_attr_e( 'Select terms', VSWOO_TEXTDOMAIN ); ?>"
                    class="multiselect attribute_values wc-enhanced-select"
                    name="attribute_values[<?php echo $i; ?>][]">
				<?php
				$all_terms = get_terms( $taxonomy, apply_filters( 'woocommerce_product_attribute_terms', $args ) );
				if ( $all_terms ) :
					foreach ( $all_terms as $term ) :
						echo '<option value="' . esc_attr( $term->term_id ) . '" ' . selected( has_term( absint( $term->term_id ), $taxonomy, $product_id ), true, false ) . '>' . esc_attr( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) ) . '</option>';
					endforeach;
				endif;
				?>
            </select>

            <button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', VSWOO_TEXTDOMAIN ); ?></button>
            <button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', VSWOO_TEXTDOMAIN ); ?></button>

			<?php
		}
	}


	/**
	 * Add Custom Column Conent in Taxonomy Columns
	 *
	 * @param $content
	 * @param $column_name
	 * @param $term_id
	 */
	function add_taxonomy_columns_content( $content, $column_name, $term_id ) {

		if ( $column_name != 'vswoo_attr_col' ) {
			return;
		}

		printf( '<div class="vswoo-col-content">%s</div>', vswoo_get_attribute_content_by_term_id( $term_id ) );
	}


	/**
	 * Add Custom Columns in Taxonomy Columns
	 *
	 * @param $columns
	 *
	 * @return mixed
	 */
	function add_taxonomy_columns( $columns ) {

		$column_label = vswoo_get_attribute_col_label();

		foreach ( $columns as $col_key => $label ) {

			$__columns[ $col_key ] = $label;

			if ( $col_key == 'description' ) {
				$__columns['vswoo_attr_col'] = $column_label;
			}
		}

		return $__columns;
	}


	/**
	 * Update Taxonomy Field Data
	 *
	 * @param $term_id
	 */
	function save_attribute_taxonomy_fields( $term_id ) {

		$taxonomy = isset( $_POST['taxonomy'] ) ? sanitize_text_field( $_POST['taxonomy'] ) : '';

		if ( empty( $taxonomy ) || empty( $term_id ) ) {
			return;
		}

		$attribute_id = wc_attribute_taxonomy_id_by_name( $taxonomy );
		$attribute    = wc_get_attribute( $attribute_id );
		$field_id     = vswoo_get_attribute_field_id( $attribute->type );
		$field_val    = isset( $_POST[ $field_id ] ) ? sanitize_text_field( $_POST[ $field_id ] ) : '';

		update_term_meta( $term_id, $field_id, $field_val );
	}


	/**
	 * Generate Taxonomy Fields
	 *
	 * @param $term
	 *
	 * @throws Pick_error
	 */
	function attribute_fields( $term ) {

		global $pagenow;

		$pb_settings = new PB_Settings();
		$taxonomy    = ( $pagenow == 'term.php' && isset( $_GET['taxonomy'] ) ) ? sanitize_text_field( $_GET['taxonomy'] ) : $term;
		$att_id      = wc_attribute_taxonomy_id_by_name( $taxonomy );
		$attr        = wc_get_attribute( $att_id );
		$field_value = '';

		if ( empty( $attr->type ) || ! in_array( $attr->type, array(
				'type_colors',
				'type_images',
				'type_buttons'
			) ) ) {
			return;
		}


		if ( isset( $term->term_id ) ) {
			$field_id    = vswoo_get_attribute_field_id( $attr->type );
			$field_value = get_term_meta( $term->term_id, $field_id, true );
		}

		$options = array(
			array(
				'id'    => vswoo_get_attribute_field_id( $attr->type ),
				'type'  => vswoo_get_attribute_field_type( $attr->type ),
				'value' => $field_value,
			)
		);

		?>
        <tr class="form-field form-required">
            <th scope="row" valign="top">
                <label for="attribute_type"><?php echo esc_html( vswoo_get_attribute_field_label( $attr->type ) ); ?></label>
            </th>
            <td>
				<?php $pb_settings->generate_fields( array( array( 'options' => $options ) ), false, false ); ?>
                <p class="description"><?php echo esc_html( vswoo_get_attribute_field_description( $attr->type ) ); ?></p>
            </td>
        </tr>
		<?php
	}


	/**
	 * Add Attribute Fields
	 */
	function add_attribute_taxonomy_fields() {

		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		foreach ( wc_get_attribute_taxonomies() as $attribute ) {

			if ( empty( $attribute->attribute_name ) ) {
				continue;
			}

			add_action( wc_attribute_taxonomy_name( $attribute->attribute_name ) . '_add_form_fields', array(
				$this,
				'attribute_fields'
			), 100, 1 );
			add_action( wc_attribute_taxonomy_name( $attribute->attribute_name ) . '_edit_form_fields', array(
				$this,
				'attribute_fields'
			), 100, 1 );

			add_action( 'edited_' . wc_attribute_taxonomy_name( $attribute->attribute_name ), array(
				$this,
				'save_attribute_taxonomy_fields'
			), 10, 2 );
			add_action( 'create_' . wc_attribute_taxonomy_name( $attribute->attribute_name ), array(
				$this,
				'save_attribute_taxonomy_fields'
			), 10, 2 );

			add_filter( 'manage_edit-' . wc_attribute_taxonomy_name( $attribute->attribute_name ) . '_columns', array(
				$this,
				'add_taxonomy_columns'
			) );
			add_filter( 'manage_' . wc_attribute_taxonomy_name( $attribute->attribute_name ) . '_custom_column', array(
				$this,
				'add_taxonomy_columns_content'
			), 10, 3 );
		}
	}


	/**
	 * Add Attribute Types
	 *
	 * @param $types
	 *
	 * @return mixed
	 */
	function add_product_attribues_types( $types ) {

		$types['type_colors']  = esc_html__( 'Colors', VSWOO_TEXTDOMAIN );
		$types['type_images']  = esc_html__( 'Images', VSWOO_TEXTDOMAIN );
		$types['type_buttons'] = esc_html__( 'Buttons', VSWOO_TEXTDOMAIN );

		return $types;
	}


	function plugin_dependency_check() {

		// Check WooCommerce

		if ( ! class_exists( 'WooCommerce' ) ) {

			$error_message = sprintf( '<strong>%s</strong> %s <a href="%s">%s</a>',
				esc_html__( 'WooCommerce', VSWOO_TEXTDOMAIN ),
				esc_html__( 'Plugin is missing! WooCommerce Advanced Variation Swatches will be deactivated soon.', VSWOO_TEXTDOMAIN ),
				esc_url( '//wordpress.org/plugins/woocommerce/' ),
				esc_html__( 'Get WooCommerce', VSWOO_TEXTDOMAIN )
			);

			printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', $error_message );

			deactivate_plugins( VSWOO_PLUGIN_FILE );

			return;
		}
	}
}

new VSWOO_Hooks();