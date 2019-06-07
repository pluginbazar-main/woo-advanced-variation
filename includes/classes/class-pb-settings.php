<?php
/**
 * PB Settings
 *
 * Quick settings page generator for WordPress
 *
 * @package PB_Settings
 * @version 3.0.0
 * @author Pluginbazar
 * @copyright 2019 Pluginbazar.com
 * @see https://github.com/jaedm97/PB-Settings
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access


if ( ! class_exists( 'PB_Settings' ) ) {

	class PB_Settings {

		public $data = array();
		public $disabled_notice = null;
		private $options = array();

		/**
		 * PB_Settings constructor.
		 *
		 * @param array $args
		 */
		function __construct( $args = array() ) {

			$this->data = &$args;

			if ( $this->add_in_menu() ) {
				add_action( 'admin_menu', array( $this, 'add_menu_in_admin_menu' ), 12 );
			}

			$this->disabled_notice = $this->get_disabled_notice();
			$this->set_options();

			add_action( 'admin_init', array( $this, 'display_fields' ), 12 );
			add_filter( 'whitelist_options', array( $this, 'whitelist_options' ), 99, 1 );
		}


		/**
		 * Add Menu in WordPress Admin Menu
		 */
		function add_menu_in_admin_menu() {

			if ( "menu" == $this->get_menu_type() ) {
				$menu_ret = add_menu_page( $this->get_menu_name(), $this->get_menu_title(), $this->get_capability(), $this->get_menu_slug(), array(
					$this,
					'display_function'
				), $this->get_menu_icon(), $this->get_menu_position() );

				do_action( 'pb_settings_menu_added_' . $this->get_menu_slug(), $menu_ret );
			}

			if ( "submenu" == $this->get_menu_type() ) {
				$submenu_ret = add_submenu_page( $this->get_parent_slug(), $this->get_page_title(), $this->get_menu_title(), $this->get_capability(), $this->get_menu_slug(), array(
					$this,
					'display_function'
				) );

				do_action( 'pb_settings_submenu_added_' . $this->get_menu_slug(), $submenu_ret );
			}
		}


		/**
         * Generate Settings Fields
         *
		 * @param array $settings
		 * @param bool $post_id
		 * @param bool $custom_style
		 *
		 * @throws PB_Error
		 */
		function generate_fields( $settings = array(), $post_id = false, $custom_style = true ) {

			if ( ! is_array( $settings ) ) {
				throw new PB_Error( 'Invalid data provided !' );
			}

			$post_id = ! $post_id ? 0 : $post_id;

			foreach ( $settings as $key => $setting_section ) :

				if ( isset( $setting_section['title'] ) ) {
					printf( '<div style="padding: 0;font-size: 16px;margin: 10px 0;">%s</div>', $setting_section['title'] );
				}
				if ( isset( $setting_section['description'] ) ) {
					printf( '<p>%s</p>', $setting_section['description'] );
				}

				$options          = isset( $setting_section['options'] ) ? $setting_section['options'] : array();

				foreach ( $options as $option ) :

					$option_id = isset( $option['id'] ) ? $option['id'] : '';
					$option_title = isset( $option['title'] ) ? $option['title'] : '';

					if ( $post_id && ! empty( $post_id ) ) {
						$option['value'] = get_post_meta( $post_id, $option_id, true );
					}

					?>
                    <div class="wps-field">
                        <label for="<?php echo esc_attr( $option_id ); ?>"
                               class="wps-field-inline wps-field-title"><?php echo esc_html( $option_title ); ?></label>

                        <div class="wps-field-inline wps-field-inputs">
							<?php $this->field_generator( $option ); ?>
                        </div>

                    </div>
				<?php
				endforeach;

			endforeach;

			if ( $custom_style ) {
				printf( '<style>%s</style>', '.wps-field {padding: 10px 0;}.wps-field .wps-field-inline {display: inline-block;vertical-align: top;}.wps-field .wps-field-title {font-size: 14px;width: 120px;min-width: 120px;font-weight: 500;}.wps-field .wps-field-inputs {margin-left: 15px;width: 76%;min-width: 320px;} .wps-field .wps-field-inputs input[type=text], .wps-field .wps-field-inputs textarea, .wps-field .wps-field-inputs input[type=number]{border-radius:4px; padding: 7px 5px; height: inherit;}' );
			}
		}


		/**
		 * Display Settings Fields
		 */
		function display_fields() {

			foreach ( $this->get_settings_fields() as $key => $setting ):

				add_settings_section( $key, isset( $setting['title'] ) ? $setting['title'] : "", array(
					$this,
					'section_callback'
				), $this->get_current_page() );

				foreach ( $setting['options'] as $option ) :

					$option_id    = isset( $option['id'] ) ? $option['id'] : '';
					$option_title = isset( $option['title'] ) ? $option['title'] : '';

					if ( empty( $option_id ) ) {
						continue;
					}

					add_settings_field( $option_id, $option_title, array(
						$this,
						'field_generator'
					), $this->get_current_page(), $key, $option );

				endforeach;

			endforeach;
		}


		/**
         * Generate field automatically from $option
         *
		 * @param $option
		 */
		function field_generator( $option ) {

			$id      = isset( $option['id'] ) ? $option['id'] : "";
			$details = isset( $option['details'] ) ? $option['details'] : "";

			if ( empty( $id ) ) {
				return;
			}

			try {

				do_action( "pb_settings_before_$id", $option );

				if ( isset( $option['type'] ) && $option['type'] === 'select' ) {
					$this->generate_select( $option );
				} elseif ( isset( $option['type'] ) && $option['type'] === 'checkbox' ) {
					$this->generate_checkbox( $option );
				} elseif ( isset( $option['type'] ) && $option['type'] === 'radio' ) {
					$this->generate_radio( $option );
				} elseif ( isset( $option['type'] ) && $option['type'] === 'textarea' ) {
					$this->generate_textarea( $option );
				} elseif ( isset( $option['type'] ) && $option['type'] === 'number' ) {
					$this->generate_number( $option );
				} elseif ( isset( $option['type'] ) && $option['type'] === 'text' ) {
					$this->generate_text( $option );
				} elseif ( isset( $option['type'] ) && $option['type'] === 'colorpicker' ) {
					$this->generate_colorpicker( $option );
				} elseif ( isset( $option['type'] ) && $option['type'] === 'datepicker' ) {
					$this->generate_datepicker( $option );
				} elseif ( isset( $option['type'] ) && $option['type'] === 'select2' ) {
					$this->generate_select2( $option );
				} elseif ( isset( $option['type'] ) && $option['type'] === 'range' ) {
					$this->generate_range( $option );
				} elseif ( isset( $option['type'] ) && $option['type'] === 'media' ) {
					$this->generate_media( $option );
				} elseif ( isset( $option['type'] ) && $option['type'] === 'gallery' ) {
					$this->generate_gallery( $option );
				}

				if ( isset( $option['disabled'] ) && $option['disabled'] ) {
					printf( '<span style="background: #ffe390eb;margin-left: 10px;padding: 5px 12px;font-size: 12px;border-radius: 3px;color: #717171;">%s</span>', $this->disabled_notice );
				}

				do_action( "pb_settings_$id", $option );

				if ( ! empty( $details ) ) {
					echo "<p class='description'>$details</p>";
				}


				do_action( "pb_settings_after_$id", $option );
			} catch ( PB_Error $e ) {
				echo $e->get_error_message();
			}
		}


		/**
         * Generate Field - Gallery
		 * @param $option
		 */
		function generate_gallery( $option ) {

			$id       = isset( $option['id'] ) ? $option['id'] : "";
			$disabled = isset( $option['disabled'] ) && $option['disabled'] ? 'disabled' : '';
			$value    = isset( $option['value'] ) ? $option['value'] : get_option( $id );
			$value    = is_array( $value ) ? $value : array( $value );
			$value    = array_filter( $value );
			$html     = "";

			if ( empty( $value ) || ! $value ) {
				$value = isset( $option['default'] ) ? $option['default'] : $value;
			}

			wp_enqueue_media();
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-sortable' );

			foreach ( $value as $attachment_id ) {

				$media_url = wp_get_attachment_url( $attachment_id );

				$html .= "<div><span onclick='this.parentElement.remove()' class='dashicons dashicons-trash'></span><img src='{$media_url}' />";
				$html .= "<input type='hidden' name='{$id}[]' value='{$attachment_id}'/>";
				$html .= "</div>";
			}

			?>
            <div id="media_preview_<?php echo esc_attr( $id ); ?>">
				<?php echo esc_html( $html ); ?>
            </div>
            <div class='button' <?php echo esc_attr( $disabled ); ?>
                 id="media_upload_<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Select Images' ); ?></div>

            ?>
            <script>
                jQuery(document).ready(function ($) {

                    $('#media_upload_<?php echo $id; ?>').click(function () {
                        var send_attachment_bkp = wp.media.editor.send.attachment;
                        wp.media.editor.send.attachment = function (props, attachment) {

                            html = "<div><span onclick='this.parentElement.remove()' class='dashicons dashicons-trash'></span><img src='" + attachment.url + "' />";
                            html += "<input type='hidden' name='<?php echo $id; ?>[]' value='" + attachment.id + "'/>";
                            html += "</div>";

                            $('#media_preview_<?php echo $id; ?>').append(html);
                        }
                        wp.media.editor.open($(this));
                        wp.media.multiple = false;
                        return false;
                    });

                    $(function () {
                        $('#media_preview_<?php echo $id; ?>').sortable({
                            handle: 'img',
                            revert: false,
                            axis: "x",
                        });
                    });
                });
            </script>
            <style>
                #media_preview_<?php echo $id; ?> > div {
                    display: inline-block;
                    vertical-align: top;
                    width: 180px;
                    border: 1px solid #ddd;
                    padding: 12px;
                    margin: 0 10px 10px 0;
                    border-radius: 4px;
                    position: relative;
                }

                #media_preview_<?php echo $id; ?> > div:hover span {
                    display: block;
                }

                #media_preview_<?php echo $id; ?> > div > span {
                    display: none;
                    cursor: pointer;
                    background: #ddd;
                    padding: 2px;
                    position: absolute;
                    top: 0px;
                    left: 0;
                    font-size: 16px;
                    border-bottom-right-radius: 4px;
                    color: #f443369c;
                }

                #media_preview_<?php echo $id; ?> > div > img {
                    width: 100%;
                    cursor: move;
                }
            </style>
			<?php
		}


		/**
         * Generate Field - Media
         *
		 * @param $option
		 */
		function generate_media( $option ) {

			$id          = isset( $option['id'] ) ? $option['id'] : "";
			$unique_id   = str_replace( array( '[', ']' ), '_', $id );
			$value       = isset( $option['value'] ) ? $option['value'] : get_option( $id );
			$disabled    = isset( $option['disabled'] ) && $option['disabled'] ? 'disabled' : '';
			$media_url   = wp_get_attachment_url( $value );
			$media_type  = get_post_mime_type( $value );
			$media_title = get_the_title( $value );

			if ( empty( $value ) || ! $value ) {
				$value = isset( $option['default'] ) ? $option['default'] : $value;
			}

			wp_enqueue_media();

			?>
            <div class="media_preview"
                 style="width: 150px;margin-bottom: 10px;background: #d2d2d2;padding: 15px 5px;text-align: center;border-radius: 5px;">

				<?php if ( "audio/mpeg" == $media_type ) : ?>

                    <div id="media_preview_<?php echo esc_attr( $unique_id ); ?>"
                         class="dashicons dashicons-format-audio" style="font-size: 70px;display: inline;"></div>
                    <div><?php echo esc_html( $media_title ); ?></div>

				<?php else : ?>
                    <img id="media_preview_<?php echo esc_attr( $unique_id ); ?>"
                         src="<?php echo esc_url( $media_url ); ?>" style="width:100%"/>
				<?php endif; ?>

            </div>
            <input type="hidden" name="<?php echo esc_attr( $id ); ?>"
                   id="media_input_<?php echo esc_attr( $unique_id ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
            <div class="hidden" <?php echo esc_attr( $disabled ); ?>
                 id="media_upload_<?php echo esc_attr( $unique_id ); ?>"><?php esc_html_e( 'Upload' ); ?></div>

            <script>
                jQuery(document).ready(function ($) {
                    $("#media_upload_<?php echo esc_attr( $unique_id ); ?>").click(function () {
                        var send_attachment_bkp = wp.media.editor.send.attachment;
                        wp.media.editor.send.attachment = function (props, attachment) {
                            $("#media_preview_<?php echo esc_attr( $unique_id ); ?>").attr('src', attachment.url);
                            $("#media_input_<?php echo esc_attr( $unique_id ); ?>").val(attachment.id);
                            wp.media.editor.send.attachment = send_attachment_bkp;
                        }
                        wp.media.editor.open($(this));
                        return false;
                    });
                });
            </script>
			<?php
		}


		/**
         * Generate Field - Range
         *
		 * @param $option
		 */
		function generate_range( $option ) {

			$id       = isset( $option['id'] ) ? $option['id'] : "";
			$min      = isset( $option['min'] ) ? $option['min'] : 1;
			$max      = isset( $option['max'] ) ? $option['max'] : 100;
			$value    = isset( $option['value'] ) ? $option['value'] : get_option( $id );
			$disabled = isset( $option['disabled'] ) && $option['disabled'] ? 'disabled' : '';
			$value    = empty( $value ) ? $min : $value;

			if ( empty( $value ) || ! $value ) {
				$value = isset( $option['default'] ) ? $option['default'] : $value;
			}

			?>
            <input class="pick_range"
                   type="range" <?php echo esc_attr( $disabled ); ?>
                   min="<?php echo esc_attr( $min ); ?>"
                   max="<?php echo esc_attr( $max ); ?>"
                   name="<?php echo esc_attr( $id ); ?>"
                   id="<?php echo esc_attr( $id ); ?>"
                   value="<?php echo esc_attr( $value ); ?>">
            <span class="show_value"
                  id="<?php echo esc_attr( $id ); ?>_show_value"><?php echo esc_html( $value ); ?></span>
            <style>
                .pick_range {
                    -webkit-appearance: none;
                    width: 280px;
                    height: 7px;
                    background: #9a9a9a;
                    outline: none;
                }

                .show_value {
                    font-size: 17px;
                    margin-left: 8px;
                }

                .pick_range::-webkit-slider-thumb {
                    -webkit-appearance: none;
                    appearance: none;
                    width: 25px;
                    height: 25px;
                    border-radius: 50%;
                    background: #138E77;
                    cursor: pointer;
                }

                .pick_range::-moz-range-thumb {
                    width: 25px;
                    height: 25px;
                    border-radius: 50%;
                    background: #138E77;
                    cursor: pointer;
                }
                input[type=range]:disabled {
                    background: #fee498;
                }
            </style>
            <script>
                jQuery(document).ready(function ($) {
                    $('#<?php echo esc_attr( $id ); ?>').on('input', function (e) {
                        $('#<?php echo esc_attr( $id ); ?>_show_value').html($('#<?php echo esc_attr( $id ); ?>').val());
                    });
                })
            </script>
			<?php
		}


		/**
         * Generate Field - Select2
         *
		 * @param $option
		 */
		function generate_select2( $option ) {

			$id            = isset( $option['id'] ) ? $option['id'] : "";
			$args          = isset( $option['args'] ) ? $option['args'] : array();
			$args          = is_array( $args ) ? $args : $this->generate_args_from_string( $args, $option );
			$value         = isset( $option['value'] ) ? $option['value'] : get_option( $id );
			$multiple      = isset( $option['multiple'] ) ? $option['multiple'] : false;
			$required      = isset( $option['required'] ) ? $option['required'] : false;
			$disabled      = isset( $option['disabled'] ) && $option['disabled'] ? 'disabled' : '';
			$required      = $required ? "required='required'" : '';
			$name          = $multiple ? sprintf( '%s[]', $id ) : sprintf( '%s', $id );
			$multiple_text = $multiple ? 'multiple' : '';

			if ( empty( $value ) || ! $value ) {
				$value = isset( $option['default'] ) ? $option['default'] : $value;
			}

			wp_enqueue_style( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css' );
			wp_enqueue_script( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js', array( 'jquery' ) );

			if ( $multiple && ! is_array( $value ) ) {
				$value = array( $value );
			}
			if ( ! $multiple && is_array( $value ) ) {
				$value = reset( $value );
			}

			?>
            <select <?php echo esc_attr( $disabled ); ?> <?php echo esc_attr( $required ); ?> <?php echo esc_attr( $multiple_text ); ?>
                    name="<?php echo esc_attr( $name ); ?>"
                    id="<?php echo esc_attr( $id ) ?>">

				<?php

				if ( ! $multiple ) {
					printf( '<option value="">%s</option>', esc_html__( 'Select your choice' ) );
				}

				foreach ( $args as $key => $name ) {

					if ( $multiple ) {
						$selected = in_array( $key, $value ) ? "selected" : "";
					} else {
						$selected = $value == $key ? "selected" : "";
					}

					printf( '<option %s value="%s">%s</option>', $selected, $key, $name );
				}
				?>
            </select>
            <script>
                jQuery(document).ready(function ($) {
                    $("#<?php echo esc_attr( $id ); ?>").select2({
                        placeholder: "<?php esc_html__( 'Select your choice' ) ?>",
                        width: "320px",
                        allowClear: true
                    });
                });
            </script>
			<?php
		}


		/**
         * Generate Field - Datepicker
         *
		 * @param $option
		 */
		function generate_datepicker( $option ) {

			$id           = isset( $option['id'] ) ? $option['id'] : "";
			$placeholder  = isset( $option['placeholder'] ) ? $option['placeholder'] : "";
			$autocomplete = isset( $option['autocomplete'] ) ? $option['autocomplete'] : "";
			$value        = isset( $option['value'] ) ? $option['value'] : get_option( $id );
			$disabled     = isset( $option['disabled'] ) && $option['disabled'] ? 'disabled' : '';

			if ( empty( $value ) || ! $value ) {
				$value = isset( $option['default'] ) ? $option['default'] : $value;
			}

			wp_enqueue_style( 'jquery-ui', '//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
			wp_enqueue_script( 'jquery-ui-datepicker' );

			?>
            <input type="text" class="regular-text" <?php echo esc_attr( $disabled ); ?>
                   name="<?php echo esc_attr( $id ); ?>"
                   id="<?php echo esc_attr( $id ); ?>"
                   autocomplete="<?php echo esc_attr( $autocomplete ); ?>"
                   placeholder="<?php echo esc_attr( $placeholder ); ?>"
                   value="<?php echo esc_attr( $value ); ?>"/>

            <script>
                jQuery(document).ready(function ($) {
                    $("#<?php echo esc_attr( $id ); ?>").datepicker();
                });
            </script>
			<?php
		}


		/**
         * Generate Field - Color Picker
         *
		 * @param $option
		 */
		function generate_colorpicker( $option ) {

			$id          = isset( $option['id'] ) ? $option['id'] : "";
			$unique_id   = str_replace( array( '[', ']' ), '_', $id );
			$placeholder = isset( $option['placeholder'] ) ? $option['placeholder'] : "";
			$value       = isset( $option['value'] ) ? $option['value'] : get_option( $id );

			if ( empty( $value ) || ! $value ) {
				$value = isset( $option['default'] ) ? $option['default'] : $value;
			}

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

			?>
            <input type="text" class="regular-text"
                   name="<?php echo esc_attr( $id ); ?>"
                   id="<?php echo esc_attr( $unique_id ); ?>"
                   placeholder="<?php echo esc_attr( $placeholder ); ?>"
                   value="<?php echo esc_attr( $value ); ?>"/>

            <script>
                jQuery(document).ready(function ($) {
                    $('#<?php echo esc_attr( $unique_id ); ?>').wpColorPicker();

					<?php if( isset( $option['disabled'] ) && $option['disabled'] ) : ?>
                    $('#<?php echo esc_attr( $unique_id ); ?>').parent().parent().parent().find('button.wp-color-result').prop('disabled', true);
					<?php endif; ?>
                });
            </script>
			<?php
		}


		/**
         * Generate Field - Text
         *
		 * @param $option
		 */
		function generate_text( $option ) {

			$id           = isset( $option['id'] ) ? $option['id'] : "";
			$placeholder  = isset( $option['placeholder'] ) ? $option['placeholder'] : "";
			$autocomplete = isset( $option['autocomplete'] ) ? $option['autocomplete'] : "";
			$value        = isset( $option['value'] ) ? $option['value'] : get_option( $id );
			$required     = isset( $option['required'] ) ? $option['required'] : false;
			$required     = $required ? "required='required'" : '';
			$disabled     = isset( $option['disabled'] ) && $option['disabled'] ? 'disabled' : '';

			if ( empty( $value ) || ! $value ) {
				$value = isset( $option['default'] ) ? $option['default'] : $value;
			}

			?>
            <input type="text" <?php echo esc_attr( $disabled ); ?> <?php echo esc_attr( $required ); ?>
                   class="regular-text"
                   name="<?php echo esc_attr( $id ); ?>"
                   id="<?php echo esc_attr( $id ); ?>"
                   placeholder="<?php echo esc_attr( $placeholder ); ?>"
                   autocomplete="<?php echo esc_attr( $autocomplete ); ?>"
                   value="<?php echo esc_attr( $value ); ?>"/>
			<?php
		}


		/**
         * Generate Field - Number
         *
		 * @param $option
		 */
		function generate_number( $option ) {

			$id          = isset( $option['id'] ) ? $option['id'] : "";
			$placeholder = isset( $option['placeholder'] ) ? $option['placeholder'] : "";
			$value       = isset( $option['value'] ) ? $option['value'] : get_option( $id );
			$required    = isset( $option['required'] ) ? $option['required'] : false;
			$required    = $required ? "required='required'" : '';
			$disabled    = isset( $option['disabled'] ) && $option['disabled'] ? 'disabled' : '';

			if ( empty( $value ) || ! $value ) {
				$value = isset( $option['default'] ) ? $option['default'] : $value;
			}

			?>
            <input type="number"
                   class="regular-text" <?php echo esc_attr( $disabled ); ?> <?php echo esc_attr( $required ); ?>
                   name="<?php echo esc_attr( $id ); ?>"
                   id="<?php echo esc_attr( $id ); ?>"
                   placeholder="<?php echo esc_attr( $placeholder ); ?>"
                   value="<?php echo esc_attr( $value ); ?>"/>
			<?php
		}


		/**
         * Generate Field - Textarea
         *
		 * @param $option
		 */
		function generate_textarea( $option ) {

			$id          = isset( $option['id'] ) ? $option['id'] : "";
			$placeholder = isset( $option['placeholder'] ) ? $option['placeholder'] : "";
			$value       = isset( $option['value'] ) ? $option['value'] : get_option( $id );
			$required    = isset( $option['required'] ) ? $option['required'] : false;
			$required    = $required ? "required='required'" : '';
			$disabled    = isset( $option['disabled'] ) && $option['disabled'] ? 'disabled' : '';

			if ( empty( $value ) || ! $value ) {
				$value = isset( $option['default'] ) ? $option['default'] : $value;
			}

			?>
            <textarea cols="40" <?php echo esc_attr( $disabled ); ?> <?php echo esc_attr( $required ); ?>
                      rows="5"
                      name="<?php echo esc_attr( $id ); ?>"
                      id="<?php echo esc_attr( $id ); ?>"
                      placeholder="<?php echo esc_attr( $placeholder ); ?>">
                <?php echo esc_html( $value ); ?>
            </textarea>
			<?php
		}


		/**
         * Generate Field - Select
         *
		 * @param $option
		 */
		function generate_select( $option ) {

			$id       = isset( $option['id'] ) ? $option['id'] : "";
			$args     = isset( $option['args'] ) ? $option['args'] : array();
			$args     = is_array( $args ) ? $args : $this->generate_args_from_string( $args, $option );
			$value    = isset( $option['value'] ) ? $option['value'] : get_option( $id );
			$required = isset( $option['required'] ) ? $option['required'] : false;
			$required = $required ? "required='required'" : '';
			$disabled = isset( $option['disabled'] ) && $option['disabled'] ? 'disabled' : '';

			if ( empty( $value ) || ! $value ) {
				$value = isset( $option['default'] ) ? $option['default'] : $value;
			}

			?>
            <select <?php echo esc_attr( $disabled ); ?> <?php echo esc_attr( $required ); ?>
                    name="<?php echo esc_attr( $id ); ?>"
                    id="<?php echo esc_attr( $id ); ?>">

				<?php

				printf( '<option value="">%s</option>', esc_html__( 'Select your choice' ) );

				foreach ( $args as $key => $name ) {
					printf( '<option %s value="%s">%s</option>', $value == $key ? "selected" : "", $key, $name );
				}

				?>
            </select>
			<?php
		}


		/**
         * Generate Field - Checkbox
         *
		 * @param $option
		 */
		function generate_checkbox( $option ) {

			$id       = isset( $option['id'] ) ? $option['id'] : "";
			$args     = isset( $option['args'] ) ? $option['args'] : array();
			$args     = is_array( $args ) ? $args : $this->generate_args_from_string( $args, $option );
			$value    = isset( $option['value'] ) ? $option['value'] : get_option( $id );
			$disabled = isset( $option['disabled'] ) && $option['disabled'] ? 'disabled' : '';

			if ( empty( $value ) || ! $value ) {
				$value = isset( $option['default'] ) ? $option['default'] : $value;
			}

			?>
            <fieldset>
				<?php
				foreach ( $args as $key => $val ) {

					$checked = is_array( $value ) && in_array( $key, $value ) ? "checked" : "";
					printf( '<label for="%1$s-%2$s"><input %3$s %4$s type="checkbox" id="%1$s-%2$s" name="%1$s[]" value="%2$s"></label>%5$s<br>',
						$id, $key, $disabled, $checked, $val
					);
				}
				?>
            </fieldset>
			<?php
		}


		/**
         * Generate Field - Radio
         *
		 * @param $option
		 */
		function generate_radio( $option ) {

			$id       = isset( $option['id'] ) ? $option['id'] : "";
			$args     = isset( $option['args'] ) ? $option['args'] : array();
			$args     = is_array( $args ) ? $args : $this->generate_args_from_string( $args, $option );
			$value    = isset( $option['value'] ) ? $option['value'] : get_option( $id );
			$disabled = isset( $option['disabled'] ) && $option['disabled'] ? 'disabled' : '';

			if ( empty( $value ) || ! $value ) {
				$value = isset( $option['default'] ) ? $option['default'] : $value;
			}

			?>
            <fieldset>
				<?php
				foreach ( $args as $key => $val ) {

					$checked = is_array( $value ) && in_array( $key, $value ) ? "checked" : "";
					printf( '<label for="%1$s-%2$s"><input %3$s %4$s type="radio" id="%1$s-%2$s" name="%1$s[]" value="%2$s"></label>%5$s<br>',
						$id, $key, $disabled, $checked, $val
					);
				}
				?>
            </fieldset>
			<?php
		}


		/**
         * Section Callback
         *
		 * @param $section
		 */
		function section_callback( $section ) {

			$data        = isset( $section['callback'][0]->data ) ? $section['callback'][0]->data : array();
			$description = isset( $data['pages'][ $this->get_current_page() ]['page_settings'][ $section['id'] ]['description'] ) ? $data['pages'][ $this->get_current_page() ]['page_settings'][ $section['id'] ]['description'] : "";

			echo $description;
		}


		/**
         * Add new options to $whitelist_options
         *
		 * @param $whitelist_options
		 *
		 * @return mixed
		 */
		function whitelist_options( $whitelist_options ) {

			foreach ( $this->get_pages() as $page_id => $page ) :
				$page_settings = isset( $page['page_settings'] ) ? $page['page_settings'] : array();
				foreach ( $page_settings as $section ):
					foreach ( $section['options'] as $option ):
						$whitelist_options[ $page_id ][] = $option['id'];
					endforeach;
				endforeach;
			endforeach;

			return $whitelist_options;
		}


		/**
		 * Display Settings Tab Page
		 */
		function display_function() {

			global $pagenow;
			parse_str( $_SERVER['QUERY_STRING'], $nav_url_args );

			$tab_count = 0;

			?>
            <div class="wrap">
                <h2><?php echo esc_html( $this->get_menu_page_title() ); ?></h2><br>

				<?php settings_errors(); ?>

                <nav class="nav-tab-wrapper">
					<?php
					foreach ( $this->get_pages() as $page_id => $page ) {

						$tab_count ++;

						$active              = $this->get_current_page() == $page_id ? 'nav-tab-active' : '';
						$nav_url_args['tab'] = $page_id;
						$nav_menu_url        = http_build_query( $nav_url_args );
						$page_nav            = isset( $page['page_nav'] ) ? $page['page_nav'] : '';

						printf( '<a href="%s?%s" class="nav-tab %s">%s</a>', $pagenow, $nav_menu_url, $active, $page_nav );
					}
					?>
                </nav>

				<?php
				do_action( 'pb_settings_before_page_' . $this->get_current_page() );

				if ( $this->show_submit_button() ) {
					printf( '<form class="pb_settings_form" action="options.php" method="post">%s%s</form>',
						$this->get_settings_fields_html(), get_submit_button()
					);
				} else {
					print( $this->get_settings_fields_html() );
				}

				do_action( 'pb_settings_after_page_' . $this->get_current_page() );
				?>
            </div>
			<?php
		}


		/**
         * Return All Settings HTML
         *
		 * @return false|string
		 */
		function get_settings_fields_html() {

			ob_start();

			do_action( 'pb_settings_page_' . $this->get_current_page() );

			settings_fields( $this->get_current_page() );
			do_settings_sections( $this->get_current_page() );

			return ob_get_clean();
		}


		/**
         * Generate and return arguments from string
         *
		 * @param $string
		 * @param $option
		 *
		 * @return array|mixed|void
		 * @throws PB_Error
		 */
		function generate_args_from_string( $string, $option ) {

			if ( strpos( $string, 'PAGES' ) !== false ) {
				return $this->get_pages_array();
			}
			if ( strpos( $string, 'USERS' ) !== false ) {
				return $this->get_users_array();
			}
			if ( strpos( $string, 'TAX_' ) !== false ) {
				return $this->get_taxonomies_array( $string, $option );
			}
			if ( strpos( $string, 'POSTS_' ) !== false ) {
				return $this->get_posts_array( $string, $option );
			}

			return array();
		}


		/**
         * Return Posts as Array
         *
		 * @param $string
		 * @param $option
		 *
		 * @return array
		 * @throws PB_Error
		 */
		function get_posts_array( $string, $option ) {

			$arr_posts = array();

			preg_match_all( "/\%([^\]]*)\%/", $string, $matches );

			if ( isset( $matches[1][0] ) ) {
				$post_type = $matches[1][0];
			} else {
				$post_type = 'post';
			}

			if ( ! post_type_exists( $post_type ) ) {
				throw new PB_Error( "Post type <strong>$post_type</strong> doesn't exists!" );
			}

			$wp_query = isset( $option['wp_query'] ) ? $option['wp_query'] : array();
			$ppp      = isset( $wp_query['posts_per_page'] ) ? $option['posts_per_page'] : - 1;
			$wp_query = array_merge( $wp_query, array( 'post_type' => $post_type, 'posts_per_page' => $ppp ) );
			$posts    = get_posts( $wp_query );

			foreach ( $posts as $post ) {
				$arr_posts[ $post->ID ] = $post->post_title;
			}

			return $arr_posts;
		}


		/**
         * Get taxonomies as Array
         *
		 * @param $string
		 * @param $option
		 *
		 * @return array
		 * @throws PB_Error
		 */
		function get_taxonomies_array( $string, $option ) {

			$taxonomies = array();

			preg_match_all( "/\%([^\]]*)\%/", $string, $matches );

			if ( isset( $matches[1][0] ) ) {
				$taxonomy = $matches[1][0];
			} else {
				throw new PB_Error( 'Invalid taxonomy declaration !' );
			}

			if ( ! taxonomy_exists( $taxonomy ) ) {
				throw new PB_Error( "Taxonomy <strong>$taxonomy</strong> doesn't exists !" );
			}

			$terms = get_terms( $taxonomy, array(
				'hide_empty' => false,
			) );

			foreach ( $terms as $term ) {
				$taxonomies[ $term->term_id ] = $term->name;
			}

			return $taxonomies;
		}


		/**
         * Get pages as Array
         *
		 * @return mixed|void
		 */
		function get_pages_array() {

			$pages_array = array();
			foreach ( get_pages() as $page ) {
				$pages_array[ $page->ID ] = $page->post_title;
			}

			return apply_filters( 'pb_settings_filter_pages', $pages_array );
		}


		/**
         * Get users as Array
         *
		 * @return mixed|void
		 */
		function get_users_array() {

			$user_array = array();
			foreach ( get_users() as $user ) {
				$user_array[ $user->ID ] = $user->display_name;
			}

			return apply_filters( 'pb_settings_filter_users', $user_array );
		}


		/**
         * Return All options ids as Array
         *
		 * @return array
		 */
		function get_option_ids() {

			$option_ids = array_map( function ( $option ) {
				return isset( $option['id'] ) ? $option['id'] : '';
			}, $this->get_options() );

			return $option_ids;
		}


		/**
		 * Set options from Data object
		 */
		private function set_options() {

			foreach ( $this->get_pages() as $page ):
				$setting_sections = isset( $page['page_settings'] ) ? $page['page_settings'] : array();
				foreach ( $setting_sections as $setting_section ):
					if ( isset( $setting_section['options'] ) ) {
						$this->options = array_merge( $this->options, $setting_section['options'] );
					}
				endforeach;
			endforeach;
		}


		/**
         * Return Options
         *
		 * @return array
		 */
		function get_options() {
			return $this->options;
		}


		/**
         * Return whether Submit button to show or hide
         *
		 * @return bool
		 */
		private function show_submit_button() {
			return isset( $this->get_pages()[ $this->get_current_page() ]['show_submit'] )
				? $this->get_pages()[ $this->get_current_page() ]['show_submit']
				: true;
		}


		/**
         * Return Current Page
         *
		 * @return mixed|string
		 */
		function get_current_page() {

			$all_pages   = $this->get_pages();
			$page_keys   = array_keys( $all_pages );
			$default_tab = ! empty( $all_pages ) ? reset( $page_keys ) : "";

			return isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : $default_tab;
		}


		/**
         * Return menu type
         *
		 * @return mixed|string
		 */
		private function get_menu_type() {
			if ( isset( $this->data['menu_type'] ) ) {
				return $this->data['menu_type'];
			} else {
				return "main";
			}
		}


		/**
         * Return Pages
         *
		 * @return array|mixed
		 */
		private function get_pages() {
			if ( isset( $this->data['pages'] ) ) {
				$pages = $this->data['pages'];
			} else {
				return array();
			}

			$pages_sorted = array();
			$increment    = 0;

			foreach ( $pages as $page_key => $page ) {

				$increment += 5;
				$priority  = isset( $page['priority'] ) ? $page['priority'] : $increment;

				$pages_sorted[ $page_key ] = $priority;
			}
			array_multisort( $pages_sorted, SORT_ASC, $pages );

			return $pages;
		}


		/**
         * Return settings fields
         *
		 * @return array
		 */
		private function get_settings_fields() {
			if ( isset( $this->get_pages()[ $this->get_current_page() ]['page_settings'] ) ) {
				return $this->get_pages()[ $this->get_current_page() ]['page_settings'];
			} else {
				return array();
			}
		}


		/**
		 * @return mixed|string
		 */
		private function get_menu_position() {
			if ( isset( $this->data['position'] ) ) {
				return $this->data['position'];
			} else {
				return "";
			}
		}


		/**
		 * @return mixed|string
		 */
		private function get_menu_icon() {
			if ( isset( $this->data['menu_icon'] ) ) {
				return $this->data['menu_icon'];
			} else {
				return "";
			}
		}


		/**
         * Return menu slug
         *
		 * @return mixed|string
		 */
		function get_menu_slug() {
			if ( isset( $this->data['menu_slug'] ) ) {
				return $this->data['menu_slug'];
			} else {
				return "my-custom-settings";
			}
		}


		/**
         * Get user capability
         *
		 * @return mixed|string
		 */
		private function get_capability() {
			if ( isset( $this->data['capability'] ) ) {
				return $this->data['capability'];
			} else {
				return "manage_options";
			}
		}


		/**
         * Return menu page title
         *
		 * @return mixed|string
		 */
		private function get_menu_page_title() {
			if ( isset( $this->data['menu_page_title'] ) ) {
				return $this->data['menu_page_title'];
			} else {
				return "My Custom Menu";
			}
		}


		/**
         * Return menu name
         *
		 * @return mixed|string
		 */
		private function get_menu_name() {
			if ( isset( $this->data['menu_name'] ) ) {
				return $this->data['menu_name'];
			} else {
				return "Menu Name";
			}
		}


		/**
         * Return menu title
         *
		 * @return mixed|string
		 */
		private function get_menu_title() {
			if ( isset( $this->data['menu_title'] ) ) {
				return $this->data['menu_title'];
			} else {
				return "Menu Title";
			}
		}


		/**
         * Return menu page title
         *
		 * @return mixed|string
		 */
		private function get_page_title() {
			if ( isset( $this->data['page_title'] ) ) {
				return $this->data['page_title'];
			} else {
				return "Page Title";
			}
		}


		/**
         * Check whether to add in WordPress Admin menu or not
         *
		 * @return bool
		 */
		private function add_in_menu() {
			if ( isset( $this->data['add_in_menu'] ) && $this->data['add_in_menu'] ) {
				return true;
			} else {
				return false;
			}
		}


		/**
         * Return parent menu slug
         *
		 * @return mixed|string
		 */
		function get_parent_slug() {
			if ( isset( $this->data['parent_slug'] ) && $this->data['parent_slug'] ) {
				return $this->data['parent_slug'];
			} else {
				return "";
			}
		}


		/**
         * Return disabled notice
         *
		 * @return mixed|string
		 */
		function get_disabled_notice() {
			if ( isset( $this->data['disabled_notice'] ) && $this->data['disabled_notice'] ) {
				return $this->data['disabled_notice'];
			} else {
				return "";
			}
		}


		/**
		 * Return Option Value for Given Option ID
		 *
		 * @param bool $option_id
		 * @param string|mixed $default
		 *
		 * @return bool|mixed|void
		 */
		function get_option_value( $option_id = false, $default = '' ) {

			if ( ! $option_id || empty( $option_id ) ) {
				return false;
			}

			$option = array();
			foreach ( $this->get_options() as $__option ) {
				if ( ! isset( $__option['id'] ) || $__option['id'] != $option_id ) {
					continue;
				}
				$option = $__option;
			}

			// Check from DB
			$option_value = get_option( $option_id, '' );

			// Check from given value
			if ( empty( $option_value ) ) {
				$option_value = isset( $option['value'] ) ? $option['value'] : '';
			}

			// Check from default value
			if ( empty( $option_value ) ) {
				$option_value = isset( $option['default'] ) ? $option['default'] : '';
			}

			// Set given Default value
			if ( empty( $option_value ) ) {
				$option_value = $default;
			}

			return apply_filters( 'pb_settings_option_value', $option_value, $option_id, $option );
		}
	}

}


if ( ! class_exists( 'PB_Error' ) ) {
	/**
	 * Class PB_Error
	 */
	class PB_Error extends Exception {

		/**
		 * PB_Error constructor.
		 *
		 * @param $message
		 * @param int $code
		 * @param Exception|null $previous
		 */
		function __construct( $message, $code = 0, Exception $previous = null ) {
			parent::__construct( $message, $code, $previous );
		}

		/**
         * Return Error Message
         *
		 * @return string
		 */
		function get_error_message() {
		    return sprintf( '<p class="notice notice-error" style="padding: 10px;">%s</p>', $this->getMessage() );
		}
	}
}