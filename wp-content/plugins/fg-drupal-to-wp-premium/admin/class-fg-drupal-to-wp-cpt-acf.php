<?php

/**
 * ACF methods
 *
 * @link       https://www.fredericgilles.net/fg-drupal-to-wp/
 * @since      3.0.0
 *
 * @package    FG_Drupal_to_WordPress_Premium
 * @subpackage FG_Drupal_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Drupal_to_WordPress_CPT_ACF', false) ) {

	/**
	 * ACF class
	 *
	 * @package    FG_Drupal_to_WordPress_Premium
	 * @subpackage FG_Drupal_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Drupal_to_WordPress_CPT_ACF {
		
		private $plugin;
		private $custom_fields = array();
		
		/**
		 * Constructor
		 */
		public function __construct($plugin) {
			$this->plugin = $plugin;
			
			add_action('fgd2wp_pre_import', array($this, 'set_custom_fields'), 99);
		}
		
		/**
		 * Check if ACF is activated
		 */
		public function check_required_plugins() {
			if ( !defined('ACF') ) {
				$this->plugin->display_admin_warning(sprintf(__('The <a href="%s" target="_blank">Advanced Custom Fields plugin</a> is required to manage the custom post types, the custom taxonomies and the custom fields.', 'fgd2wpp'), 'https://wordpress.org/plugins/advanced-custom-fields/'));
			}
		}
		
		/**
		 * Delete the data
		 * 
		 * @since 3.28.0
		 */
		public function delete_data() {
			// Nothing to delete
		}
		
		/**
		 * Set the custom fields in an array
		 * 
		 * @since 3.4.0
		 */
		public function set_custom_fields() {
			$this->custom_fields = $this->get_acf_custom_fields();
		}
		
		/**
		 * Get the ACF custom fields
		 * 
		 * @since 3.4.0
		 * 
		 * @return array Fields
		 */
		private function get_acf_custom_fields() {
			global $wpdb;
			
			$fields = array();
			$sql = "
				SELECT p.post_name AS field_id, p.post_excerpt AS field_slug, pp.post_excerpt AS parent_slug, pg.post_excerpt AS group_slug
				FROM {$wpdb->posts} p
				LEFT JOIN {$wpdb->posts} pp ON pp.ID = p.post_parent AND pp.post_type IN ('acf-field', 'acf-field-group')
				LEFT JOIN {$wpdb->posts} pg ON pg.ID = pp.post_parent AND pg.post_type = 'acf-field-group'
				WHERE p.post_type = 'acf-field'
			";
			$results = $wpdb->get_results($sql, ARRAY_A);
			foreach ( $results as $row ) {
				$field_slug = $row['field_slug'];
				if ( !empty($row['parent_slug']) ) {
					// Subfield
					$field_slug = $row['parent_slug'] . '_' . $field_slug;
				}
				if ( !empty($row['group_slug']) ) {
					// Field group
					$field_slug = $row['group_slug'] . '_' . $field_slug;
				}
				$fields[$field_slug] = $row['field_id'];
			}
			return $fields;
		}
		
		/**
		 * Check if the repeating fields are supported with the current ACF version
		 * 
		 * @return bool Repeating fields supported
		 */
		public function is_repeating_fields_supported() {
			return defined('ACF_PRO');
		}
		
		/**
		 * Get the field prefix
		 * 
		 * @return string Field prefix
		 */
		public function get_field_prefix() {
			return '';
		}
		
		/**
		 * Register a builtin post type
		 *
		 * @param string $post_type Post type slug
		 * @param string $singular Singular post type name
		 * @param string $plural Plural post type name
		 * @param string $description Post type description
		 * @param array $taxonomies Taxonomies for this post type
		 */
		public function register_builtin_post_type($post_type, $singular, $plural, $description, $taxonomies) {
			// Builtin post types are not registered with ACF
		}
		
		/**
		 * Register a post type on ACF
		 *
		 * @param string $post_type Post type slug
		 * @param string $singular Singular label
		 * @param string $plural Plural label
		 * @param string $description Post type description
		 * @param array $taxonomies Taxonomies for this post type
		 * @param array $parent_post_types Parent post types
		 * @param bool $hierarchical Hierarchical post type?
		 */
		public function register_custom_post_type($post_type, $singular, $plural, $description, $taxonomies, $parent_post_types=array(), $hierarchical=false) {
			if ( !in_array($post_type, array('post', 'page', 'attachment')) ) {
				$acf_post_types = get_posts(array(
					'title' => $plural,
					'post_type' => 'acf-post-type',
				));
				if ( empty($acf_post_types) ) {
					if ( is_numeric($post_type) ) {
						// The post type must not be entirely numeric
						$post_type = '_' . $post_type;
					}
					$acf_post_type = array(
						'post_type' => $post_type,
						'advanced_configuration' => '',
						'import_source' =>  $this->plugin->get_plugin_name(),
						'import_date' =>  date('Y-m-d H:i:s'),
						'labels' => array(
							'name' => $plural,
							'singular_name' => $singular,
							'menu_name' => $plural,
							'all_items' => "All $plural",
							'edit_item' => "Edit $singular",
							'view_item' => "View $singular",
							'view_items' => "View $plural",
							'add_new_item' => "Add New $singular",
							'add_new' => '',
							'new_item' => "New $singular",
							'parent_item_colon' => "Parent $singular:",
							'search_items' => "Search $plural",
							'not_found' => "No $plural found",
							'not_found_in_trash' => "No $plural found in Trash",
							'archives' => "$singular Archives",
							'attributes' => "$singular Attributes",
							'featured_image' => '',
							'set_featured_image' => '',
							'remove_featured_image' => '',
							'use_featured_image' => '',
							'insert_into_item' => "Insert into $singular",
							'uploaded_to_this_item' => "Uploaded to this $singular",
							'filter_items_list' => "Filter $plural list",
							'filter_by_date' => "Filter $plural by date",
							'items_list_navigation' => "$plural list navigation",
							'items_list' => "$plural list",
							'item_published' => "$singular published.",
							'item_published_privately' => "$singular published privately.",
							'item_reverted_to_draft' => "$singular reverted to draft.",
							'item_scheduled' => "$singular scheduled.",
							'item_updated' => "$singular updated.",
							'item_link' => "$singular Link",
							'item_link_description' => "A link to a $singular.",
						),
						'description' => $description,
						'public' => 1,
						'hierarchical' => $hierarchical,
						'exclude_from_search' => '',
						'publicly_queryable' => 1,
						'show_ui' => 1,
						'show_in_menu' => 1,
						'admin_menu_parent' => '',
						'show_in_admin_bar' => 1,
						'show_in_nav_menus' => 1,
						'show_in_rest' => 1,
						'rest_base' => '',
						'rest_namespace' => 'wp/v2',
						'rest_controller_class' => 'WP_REST_Posts_Controller',
						'menu_position' => '',
						'menu_icon' => '',
						'rename_capabilities' => '',
						'singular_capability_name' => 'post',
						'plural_capability_name' => 'posts',
						'supports' => array('title', 'editor', 'thumbnail', 'author', 'custom-fields'),
						'taxonomies' => $taxonomies,
						'has_archive' => '',
						'has_archive_slug' => '',
						'rewrite' => array(
							'permalink_rewrite' => 'post_type_key',
							'with_front' => 1,
							'feeds' => 0,
							'pages' => 1,
						),
						'query_var' => 'post_type_key',
						'query_var_name' => '',
						'can_export' => 1,
						'delete_with_user' => '',
						'register_meta_box_cb' => '',
					);
					// Support Page attributes for hierarchical post type
					if ( $hierarchical ) {
						$acf_post_type['supports'][] = 'page-attributes';
					}
					$new_post = array(
						'post_type'			=> 'acf-post-type',
						'post_title'		=> $plural,
						'post_name'			=> uniqid('post_type_'),
						'post_status'		=> 'publish',
						'post_excerpt'		=> sanitize_title($plural),
						'post_content'		=> maybe_serialize($acf_post_type),
						'comment_status'	=> 'closed',
					);
					if ( wp_insert_post($new_post) ) {
						register_post_type($post_type, $acf_post_type);
					}
				}
			}
		}
		
		/**
		 * Register a builtin taxonomy on ACF
		 *
		 * @since 3.28.0
		 * 
		 * @param string $taxonomy Taxonomy slug
		 * @param string $singular Singular taxonomy name
		 * @param string $plural Plural taxonomy name
		 * @param string $description Taxonomy description
		 * @param array $post_types Associated post types
		 * @param bool $hierarchical Hierarchical taxonomy?
		 */
		public function register_builtin_taxonomy($taxonomy, $singular, $plural, $description, $post_types, $hierarchical) {
			// Builtin taxonomies are not registered with ACF
		}
		
		/**
		 * Register a taxonomy on ACF
		 *
		 * @param string $taxonomy Taxonomy slug
		 * @param string $singular Singular taxonomy name
		 * @param string $plural Plural taxonomy name
		 * @param string $description Taxonomy description
		 * @param array $post_types Associated post types
		 * @param bool $hierarchical Hierarchical taxonomy?
		 */
		public function register_custom_taxonomy($taxonomy, $singular, $plural, $description, $post_types=array(), $hierarchical=true) {
			$acf_taxonomy_posts = get_posts(array(
				'title' => $plural,
				'post_type' => 'acf-taxonomy',
			));
			if ( empty($acf_taxonomy_posts) ) {
				$acf_taxonomy = array(
					'taxonomy' => substr($taxonomy, 0, 32),
					'object_type' => $post_types,
					'advanced_configuration' => 0,
					'import_source' =>  $this->plugin->get_plugin_name(),
					'import_date' =>  date('Y-m-d H:i:s'),
					'labels' => array(
						'name' => $plural,
						'singular_name' => $singular,
						'menu_name' => $plural,
						'all_items' => "All $plural",
						'edit_item' => "Edit $singular",
						'view_item' => "View $singular",
						'update_item' => "Update $singular",
						'add_new_item' => "Add New $singular",
						'new_item_name' => "New $singular Name",
						'parent_item' => "Parent $singular",
						'parent_item_colon' => "Parent $singular:",
						'search_items' => "Search $plural",
						'most_used' => '',
						'not_found' => "No $plural found.",
						'no_terms' => "No $plural",
						'name_field_description' => '',
						'slug_field_description' => '',
						'parent_field_description' => '',
						'desc_field_description' => '',
						'filter_by_item' => "Filter by $singular",
						'items_list_navigation' => "$plural list navigation",
						'items_list' => "$plural list",
						'back_to_items' => "← Go to $plural",
						'item_link' => "$singular Link",
						'item_link_description' => "A link to a $singular",
					),
					'description' => $description,
					'capabilities' => array(
						'manage_terms' => 'manage_categories',
						'edit_terms' => 'manage_categories',
						'delete_terms' => 'manage_categories',
						'assign_terms' => 'edit_posts',
					),

					'public' => 1,
					'publicly_queryable' => 1,
					'hierarchical' => $hierarchical? 1 : 0,
					'show_ui' => 1,
					'show_in_menu' => 1,
					'show_in_nav_menus' => 1,
					'show_in_rest' => 1,
					'rest_base' => '',
					'rest_namespace' => 'wp/v2',
					'rest_controller_class' => 'WP_REST_Terms_Controller',
					'show_tagcloud' => 1,
					'show_in_quick_edit' => 1,
					'show_admin_column' => 1,
					'rewrite' => array(
						'permalink_rewrite' => 'taxonomy_key',
						'with_front' => 1,
						'rewrite_hierarchical' => 0,
					),
					'query_var' => 'post_type_key',
					'query_var_name' => '',
					'default_term' => array(
						'default_term_enabled' => 0,	
					),
					'sort' => 0,
					'meta_box' => 'default',
					'meta_box_cb' => '',
					'meta_box_sanitize_cb' => '',
				);
				$new_post = array(
					'post_type'			=> 'acf-taxonomy',
					'post_title'		=> $plural,
					'post_name'			=> uniqid('taxonomy_'),
					'post_status'		=> 'publish',
					'post_excerpt'		=> sanitize_title($plural),
					'post_content'		=> maybe_serialize($acf_taxonomy),
					'comment_status'	=> 'closed',
				);
				wp_insert_post($new_post);
				
				// Register the taxonomy on WordPress
				if ( class_exists('ACF_Taxonomy') ) {
					$ACF_Taxonomy = new ACF_Taxonomy();
					$args = $ACF_Taxonomy->get_taxonomy_args($acf_taxonomy);
					register_taxonomy($taxonomy, $acf_taxonomy['object_type'], $args);
				}
			}
		}
		
		/**
		 * Register the custom fields for a post type
		 *
		 * @param array $custom_fields Custom fields
		 * @param string $post_type Post type
		 * @return int Number of fields imported
		 */
		public function register_custom_post_fields($custom_fields, $post_type) {
			$fields_count = 0;
			if ( !empty($custom_fields) ) {
				// Create the ACF group
				$fields_group_id = $this->create_acf_group($post_type);

				// Create the ACF fields
				foreach ( $custom_fields as $field_slug => $field ) {
					if ( in_array($field_slug, array('body', 'excerpt')) ) {
						continue; // Don't register the body and excerpt fields
					}
					$custom_fields_count = apply_filters('fgd2wp_register_custom_post_field', 0, $field_slug, $field, $post_type, $fields_group_id); // Allow the add-ons to intercept the creation of the field
					if ( $custom_fields_count == 0 ) {
						if ( $field['type'] == 'daterange' ) {
							// Date range: two fields are created: date_start and date_end
							$field_id = $this->create_acf5_field($field_slug . '_start', $field, $post_type, $fields_group_id);
							$field_id = $this->create_acf5_field($field_slug . '_end', $field, $post_type, $fields_group_id);
						} else {
							$field_id = $this->create_acf5_field($field_slug, $field, $post_type, $fields_group_id);
						}
						if ( !is_wp_error($field_id) ) {
							$fields_count++;
						}
					} else {
						$fields_count += $custom_fields_count;
					}
				}
			}
			return $fields_count;
		}
		
		/**
		 * Create the ACF fields group
		 * 
		 * @param string $group_name Group name
		 * @param string $entity post_type | taxonomy
		 * @return int Fields group ID
		 */
		private function create_acf_group($group_name, $entity='post_type') {
			$meta_key = '_fgd2wp_old_fields_group_name';
			
			// Check if the group already exists
			$new_post_id = $this->plugin->get_wp_post_id_from_meta($meta_key, $group_name);
			
			if ( empty($new_post_id) ) {
				// Create a new group
				$group_title = ucfirst($group_name) . ' ' . __('fields', 'fgd2wpp');
				$group_slug = 'group_' . uniqid();
				$post_excerpt = sanitize_title($group_title);
				if ( $group_name == 'attachment' ) {
					// Attachment
					$param = 'attachment';
					$value = 'all';
				} else {
					// Post type
					$param = $entity;
					$value = $this->get_location_value($entity, $group_name);
				}
				$content = array(
					'location' => array(
						array(
							array(
								'param' => $param,
								'operator' => '==',
								'value' => $value,
							)
						)
					),
					'position' => 'normal',
					'style' => 'default',
					'label_placement' => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen' => '',
					'description' => '',
				);
				
				// Insert the post
				$new_post = array(
					'post_title'		=> $group_title,
					'post_name'			=> $group_slug,
					'post_content'		=> serialize($content),
					'post_excerpt'		=> $post_excerpt,
					'post_type'			=> 'acf-field-group',
					'post_status'		=> 'publish',
					'comment_status'	=> 'closed',
					'ping_status'		=> 'closed',
				);
				$new_post_id = wp_insert_post($new_post, true);
				if ( !is_wp_error($new_post_id) ) {
					add_post_meta($new_post_id, $meta_key, $group_name, true);
				}
			}
			return $new_post_id;
		}
		
		/**
		 * Get the location value used to create the ACF group
		 * 
		 * @since 3.20.0
		 * 
		 * @param string $entity post_type | taxonomy | user_form
		 * @param string $group_name Group name
		 * @return string Value
		 */
		private function get_location_value($entity, $group_name) {
			switch ( $entity ) {
				case 'user_form': // User
					$value = 'all';
					break;
				case 'taxonomy': // Taxonomy
					$value = taxonomy_exists($group_name)? $group_name : 'all';
					break;
				default: // Post type
					$value = $group_name;
			}
			return $value;
		}
		
		/**
		 * Create an ACF field (version 5)
		 * 
		 * @param string $field_slug Field slug
		 * @param array $field Field data
		 * @param string $post_type Post type
		 * @param int $fields_group_id Fields group ID
		 * @return int Field ID
		 */
		public function create_acf5_field($field_slug, $field, $post_type, $fields_group_id) {
			$post_parent = $fields_group_id;
			$title = $field['label'];
			$module = isset($field['module'])? $field['module'] : '';
			$field_type = $this->map_acf_field_type($this->plugin->map_custom_field_type($field['type'], $field['label'], $module), $field);
			if ( isset($field['taxonomy']) && ($post_type != 'collection') ) {
				$field_slug = $field['taxonomy'] . '-' . $field_slug;
			}
			
			$do_not_create_field = ($field_type == 'taxonomy') && ($post_type != 'collection') && ($field['node_type'] != '%'); // Don't import the taxonomy relationships as a field except for collections and taxonomies
			$do_not_create_field = apply_filters('fgd2wp_do_not_create_field', $do_not_create_field, $field, $post_type);
			if ( $do_not_create_field ) {
				return;
			}
			
			$order = isset($field['order'])? $field['order'] : 0;
			
			// Repetitive fields
			if ( $this->is_repeater_field($field) ) {
				$parent_id = $this->create_repeater_field($title, $field_slug, $order, $post_type, $fields_group_id);
				if ( !is_wp_error($parent_id) && ($parent_id != 0) ) {
					$post_parent = $parent_id;
					
					// Field collection or Paragraphs field
					if ( isset($field['collection']) ) {
						do_action('fgd2wp_post_insert_collection_field', $parent_id, $field['collection']);
						return;
					}

				}
			}
				
			// Content
			$content = array(
				'type' => $field_type,
				'instructions' => '',
				'required' => isset($field['required']) && !empty($field['required'])? 1 : 0,
				'default_value' => isset($field['default_value'][0]['value'])? $field['default_value'][0]['value'] : '',
				'placeholder' => isset($field['description'])? $field['description'] : '',
			);
			// Multiple select
			if ( isset($field['cardinality']) && ($field['cardinality'] != 1) ) {
				$content['multiple'] = 1;
			}
			// Choices
			if ( isset($field['options']) && !empty($field['options']) ) {
				if ( is_array($field['options']) ) {
					$choices = $field['options'];
				} else {
					$choices = array();
					$values = explode("\r", $field['options']);
					foreach ( $values as $item ) {
						$item = trim($item);
						list($item_key, $item_value) = explode('|', $item);
						$choices[$item_key] = $item_value;
					}
				}
				$content['choices'] = $choices;
				if ( !$content['required'] ) {
					$content['allow_null'] = 1; // Allow no value for radio boxes
				}
			}
			
			// Post object
			if ( $field_type == 'post_object' ) {
				$content['post_type'] = array();
				if ( isset($field['referenceable_types']) ) {
					foreach ( $field['referenceable_types'] as $referenceable_type ) {
						$content['post_type'][] = $this->plugin->map_post_type($referenceable_type);
					}
				}
			}
			
			// Taxonomy
			if ( $field_type == 'taxonomy' ) {
				$content['taxonomy'] = '';
				if ( isset($field['referenceable_types']) ) {
					foreach ( $field['referenceable_types'] as $referenceable_type ) {
						$content['taxonomy'] = $this->plugin->map_taxonomy($referenceable_type);
					}
				} elseif ( isset($field['taxonomy']) && !empty($field['taxonomy']) ) {
					$content['taxonomy'] = $field['taxonomy'];
				}
				$content['field_type'] = isset($content['multiple']) && $content['multiple']? 'checkbox' : 'select';
			}
			
			$field_id = $this->insert_acf_field($title, $field_slug, $order, $post_type, $post_parent, $content);
			do_action('fgd2wp_post_create_acf5_field', $field_id, $field_slug, $field, $post_type, $fields_group_id);
			return $field_id;
		}
		
		/**
		 * Get the ACF field type of a custom field
		 * 
		 * @since 3.40.0
		 * 
		 * @param array $custom_field Custom field
		 * @return string Field type
		 */
		public function get_acf_field_type($custom_field) {
			$module = isset($custom_field['module'])? $custom_field['module'] : '';
			$custom_field_type = $this->map_acf_field_type($this->plugin->map_custom_field_type($custom_field['type'], $custom_field['label'], $module), $custom_field);
			return $custom_field_type;
		}
		
		/**
		 * Map the Drupal field type to an ACF field type
		 * 
		 * @param string $field_type Field type
		 * @param array $field Field
		 * @return string ACF field type
		 */
		public function map_acf_field_type($field_type, $field) {
			switch ( $field_type ) {
				case 'textfield':
				case 'phone':
					$acf_type = 'text';
					break;
				case 'numeric':
					$acf_type = 'number';
					break;
				case 'checkbox':
					$acf_type = 'true_false';
					break;
				case 'checkboxes':
					$acf_type = 'checkbox';
					break;
				case 'date':
					$acf_type = 'date_picker';
					break;
				case 'datetime':
					$acf_type = 'date_time_picker';
					break;
				case 'time':
					$acf_type = 'time_picker';
					break;
				case 'url':
					if ( $this->plugin->premium_options['links'] == 'as_links' ) {
						$acf_type = 'link';
					} else {
						$acf_type = 'url';
					}
					break;
				case 'color':
					$acf_type = 'color_picker';
					break;
				case 'embed':
				case 'video':
					$acf_type = 'url';
					break;
				case 'image':
					$acf_type = (isset($field['repetitive']) && $field['repetitive'])? 'gallery' : 'image';
					break;
				case 'nodereference':
					if ( isset($field['target_type']) ) {
						switch ( $field['target_type'] ) {
							case 'taxonomy_term':
								$acf_type = 'taxonomy';
								break;
							case 'user':
								$acf_type = 'user';
								break;
							case 'documento':
								$acf_type = 'file';
								break;
							default:
								$acf_type = 'post_object';
						}
					} elseif ( isset($field['entity_type']) ) {
						switch ( $field['entity_type'] ) {
							case 'taxonomy':
								$acf_type = 'taxonomy';
								break;
							case 'user':
								$acf_type = 'user';
								break;
							default:
								$acf_type = 'post_object';
						}
					} else {
						$acf_type = 'post_object';
					}
					break;
				case 'group':
					$acf_type = 'group';
					break;
				default:
					$acf_type = $field_type;
			}
			$acf_type = apply_filters('fgd2wp_map_acf_field_type', $acf_type, $field_type, $field);
			return $acf_type;
		}
		
		/**
		 * Is it a repeater field?
		 * 
		 * @since 3.36.0
		 * 
		 * @param array $field Field
		 * @return bool
		 */
		private function is_repeater_field($field) {
			$is_repeater_field = isset($field['repetitive']) && $field['repetitive']
				&& $this->is_repeating_fields_supported()
				&& (!isset($field['type']) || !in_array($field['type'], array('node_reference', 'entity_reference', 'entityreference', 'image')))
				&& (!isset($field['module']) || ($field['module'] != 'image'))
				&& (!isset($field['entity_type']) || ($field['entity_type'] != 'user'));
			return apply_filters('fgd2wp_is_repeater_field', $is_repeater_field, $field);
		}
		
		/**
		 * Create a repeater field (ACF Pro)
		 * 
		 * @param string $title Field title
		 * @param string $field_slug Field slug
		 * @param int $order Order
		 * @param string $post_type Post type
		 * @param int $fields_group_id Fields group ID
		 * @return int Field ID
		 */
		private function create_repeater_field($title, $field_slug, $order, $post_type, $fields_group_id) {
			$content = array(
				'type' => 'repeater',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'collapsed' => '',
				'min' => '',
				'max' => '',
				'layout' => 'table',
				'button_label' => '',
			);
			$field_slug = 'collection-' . $field_slug;
			
			return $this->insert_acf_field($title, $field_slug, $order, $post_type, $fields_group_id, $content);
		}
		
		/**
		 * Create an ACF field
		 * 
		 * @param string $title Field title
		 * @param string $field_slug Field slug
		 * @param int $order Order
		 * @param string $post_type Post type
		 * @param int $parent_id Parent ID
		 * @param array $content Content
		 * @return int Field ID
		 */
		public function insert_acf_field($title, $field_slug, $order, $post_type, $parent_id, $content) {
			$meta_key = '_fgd2wp_old_field_name';
			$meta_value = $post_type . '-' . $field_slug . '-' . $parent_id;
			
			// Check if the field already exists
			$new_post_id = $this->plugin->get_wp_post_id_from_meta($meta_key, $meta_value);
			
			if ( empty($new_post_id) ) {

				// Insert the post
				$field_key = 'field_' . uniqid();
				$new_post = array(
					'post_title'		=> $title,
					'post_name'			=> $field_key,
					'post_content'		=> serialize($content),
					'post_excerpt'		=> $field_slug,
					'post_type'			=> 'acf-field',
					'post_parent'		=> $parent_id,
					'menu_order'		=> $order,
					'post_status'		=> 'publish',
					'comment_status'	=> 'closed',
					'ping_status'		=> 'closed',
				);
				$new_post_id = wp_insert_post($new_post, true);
				
				if ( !is_wp_error($new_post_id) ) {
					add_post_meta($new_post_id, $meta_key, $meta_value, true); // To avoid importing the same field
				}
			}
			return $new_post_id;
		}
		
		/**
		 * Register a custom taxonomy field
		 * 
		 * @param string $custom_field_name Custom field name
		 * @param array $custom_field_data Custom field data
		 */
		public function register_custom_taxonomy_field($custom_field_name, $custom_field_data) {
			// Create the ACF group
			$fields_group_id = $this->create_acf_group($this->plugin->map_taxonomy($custom_field_data['taxonomy']), 'taxonomy');
			// Create the field
			$field_slug = sanitize_key(FG_Drupal_to_WordPress_Tools::convert_to_latin(remove_accents(preg_replace('/^field_/', '', $custom_field_data['field_name']))));
			$this->create_acf5_field($field_slug, $custom_field_data, $custom_field_data['taxonomy'], $fields_group_id);
		}
		
		/**
		 * Register the user fields
		 * 
		 * @param array $custom_fields Custom user fields
		 * @return array Fields IDs
		 */
		public function register_custom_user_fields($custom_fields) {
			$fields_ids = array();
			// Create the ACF group
			$fields_group_id = $this->create_acf_group('user', 'user_form');
			foreach ( $custom_fields as $field_slug => $custom_field ) {
				// Create the field
				$fields_group_id = apply_filters('fgd2wp_user_parent_group_id', $fields_group_id, $custom_field);
				$field_id = $this->create_acf5_field($field_slug, $custom_field, 'user', $fields_group_id);
				if ( !empty($field_id) ) {
					$fields_ids[$field_slug] = $field_id;
				}
			}
			return $fields_ids;
		}
		
		/**
		 * Register the post types relationships
		 * 
		 * @param array $relationships Node Types Relationships
		 */
		public function register_post_types_relationships($relationships) {
			// ACF doesn't manage post types relationships
		}
		
		/**
		 * Get the custom post types
		 * 
		 * @return array Custom post types
		 */
		public function get_custom_post_types() {
			$custom_post_types = array();
			$acf_custom_post_types = get_posts(array(
				'post_type' => 'acf-post-type',
				'numberposts' => -1,
				'orderby' => 'ID',
				'order' => 'ASC',
			));
			foreach ( $acf_custom_post_types as $acf_custom_post_type ) {
				$post_type_object = maybe_unserialize($acf_custom_post_type->post_content);
				if ( isset($post_type_object['post_type']) ) {
					$custom_post_types[$post_type_object['post_type']] = $post_type_object;
				}
			}
			return $custom_post_types;
		}
		
		/**
		 * Get the post type name
		 * 
		 * @param array $post_type_object Post type object
		 * @return string Post type name
		 */
		public function get_post_type_name($post_type_object) {
			return $post_type_object['labels']['name'];
		}
		
		/**
		 * Get the custom taxonomies
		 * 
		 * @return array Custom taxonomies
		 */
		public function get_custom_taxonomies() {
			$custom_taxonomies = array();
			$acf_custom_taxonomies = get_posts(array('post_type' => 'acf-taxonomy'));
			foreach ( $acf_custom_taxonomies as $acf_custom_taxonomy ) {
				$taxonomy_object = maybe_unserialize($acf_custom_taxonomy->post_content);
				if ( isset($taxonomy_object['taxonomy']) ) {
					$custom_taxonomies[$taxonomy_object['taxonomy']] = $taxonomy_object;
				}
			}
			return $custom_taxonomies;
		}
		
		/**
		 * Get the taxonomy name
		 * 
		 * @param array $taxonomy_object Taxonomy object
		 * @return string Taxonomy name
		 */
		public function get_taxonomy_name($taxonomy_object) {
			return $taxonomy_object['labels']['name'];
		}
		
		/**
		 * Add a custom field value as a post meta
		 * 
		 * @since 3.4.0
		 * 
		 * @param int $new_post_id WordPress post ID
		 * @param string $custom_field_name Field name
		 * @param array $custom_field Field data
		 * @param array $custom_field_values Field values
		 * @param date $date Date
		 */
		public function set_custom_post_field($new_post_id, $custom_field_name, $custom_field, $custom_field_values, $date='') {
			$matches = array();
			$index = 0;
			$new_custom_field_name = $custom_field_name;
			// Repeater field
			$is_repeater = $this->is_repeater_field($custom_field);
			if ( $is_repeater ) {
				$repeater_field_name = 'collection-' . $custom_field_name;
				if ( preg_match('/^(collection-.*_\d+_)(.*)/', $custom_field_name, $matches) ) {
					// Repeater inside repeater
					$parent_field = $matches[1];
					$child_field = $matches[2];
					$repeater_field_name = $parent_field . 'collection-' . $child_field;
					$new_custom_field_name = $child_field;
				}
				$index = intval(get_post_meta($new_post_id, $repeater_field_name, true));
				$meta_key_like = $repeater_field_name . '\_%\_' . $new_custom_field_name;
				$current_meta_values = $this->plugin->get_post_meta_like($new_post_id, $meta_key_like);
				$custom_field_unit_name = $repeater_field_name . '_%d_' . $new_custom_field_name;
				$new_custom_field_name = $repeater_field_name . '_' . $new_custom_field_name;
			} else {
				$custom_field_unit_name = $new_custom_field_name;
			}
			$field_group = $custom_field['node_type'] . '-fields';
			$new_custom_field_name = $field_group . '_' . $new_custom_field_name;
			
			$custom_field_type = $this->get_acf_field_type($custom_field);
			$meta_values = $this->convert_custom_field_to_meta_values($custom_field, $custom_field_type, $custom_field_values, $date, $new_post_id);
			if ( $custom_field_type == 'gallery' ) {
				// Gallery
				$meta_key = sprintf($custom_field_unit_name, $index++);
				update_post_meta($new_post_id, $meta_key, $meta_values);
				if ( isset($this->custom_fields[$new_custom_field_name]) ) {
					update_post_meta($new_post_id, '_' . $meta_key, $this->custom_fields[$new_custom_field_name]);
				}
			} else {
				if ( $custom_field['type'] == 'daterange' ) {
					// Date range: import the start and end date
					$custom_field_name_start = $custom_field_name . '_start';
					$meta_key_start = sprintf(preg_replace('/' . preg_quote($custom_field_name) . '/', $custom_field_name_start, $custom_field_unit_name), $index);
					$custom_field_name_end = $custom_field_name . '_end';
					$meta_key_end = sprintf(preg_replace('/' . preg_quote($custom_field_name) . '/', $custom_field_name_end, $custom_field_unit_name), $index);
					if ( isset($meta_values[0]) ) {
						// Start date
						$meta_value = $meta_values[0];
						$index++;
						update_post_meta($new_post_id, $meta_key_start, $meta_value);
						$stored_field_name = preg_replace('/' . preg_quote($custom_field_name) . '/', $custom_field_name_start, $new_custom_field_name);
						if ( isset($this->custom_fields[$stored_field_name]) ) {
							update_post_meta($new_post_id, '_' . $meta_key_start, $this->custom_fields[$stored_field_name]);
						}
					}
					if ( isset($meta_values[1]) ) {
						// End date
						$meta_value = $meta_values[1];
						update_post_meta($new_post_id, $meta_key_end, $meta_value);
						$stored_field_name = preg_replace('/' . preg_quote($custom_field_name) . '/', $custom_field_name_end, $new_custom_field_name);
						if ( isset($this->custom_fields[$stored_field_name]) ) {
							update_post_meta($new_post_id, '_' . $meta_key_end, $this->custom_fields[$stored_field_name]);
						}
					}
				} else {
					foreach ( $meta_values as $meta_value ) {
						if ( $is_repeater && in_array($meta_value, $current_meta_values) ) {
							// The value already exists
							continue;
						}
						$meta_key = sprintf($custom_field_unit_name, $index++);
						$previous_value = get_post_meta($new_post_id, $meta_key, true);
						$meta_value = $this->append_value($previous_value, $meta_value);
						update_post_meta($new_post_id, $meta_key, $meta_value);

						//_put_contents('/var/www/new-gtt-prod/htdocs/import-logs.txt', "ELSE " . $new_post_id . " " . $meta_key . " " .$meta_value. "\n", FILE_APPEND);


						if ( isset($this->custom_fields[$new_custom_field_name]) ) {
							update_post_meta($new_post_id, '_' . $meta_key, $this->custom_fields[$new_custom_field_name]);
						}
					}
				}
			}
			
			// Repeater field
			if ( $is_repeater ) {
				// Update the last index of the repeater field
				if ( $custom_field['type'] == 'daterange' ) {
					// Date range
					update_post_meta($new_post_id, $repeater_field_name . '_start', $index);
					update_post_meta($new_post_id, $repeater_field_name . '_end', $index);
				} else {
					// Other fields
					update_post_meta($new_post_id, $repeater_field_name, $index);
					$full_repeater_field_name = $field_group . '_' . $repeater_field_name;
					if ( isset($this->custom_fields[$full_repeater_field_name]) ) {
						update_post_meta($new_post_id, '_' . $repeater_field_name, $this->custom_fields[$full_repeater_field_name]);
					}
				}
			}
		}
		
		/**
		 * Append a value to the previous value(s)
		 * 
		 * @since 3.43.0
		 * 
		 * @param mixed $previous_value Previous value(s)
		 * @param mixed $meta_value Meta value to append
		 * @return array Meta value
		 */
		private function append_value($previous_value, $meta_value) {
			if ( is_scalar($meta_value) ) {
				$meta_value = addslashes($this->plugin->replace_media_shortcodes(stripslashes($meta_value)));
			}
			if ( !empty($previous_value) ) {
				if ( !is_array($previous_value) ) {
					// Make previous value as array
					$previous_value = array($previous_value);
				}
				// Append the new value
				if ( !is_array($meta_value) ) {
					$meta_value = array($meta_value);
				}
				$meta_value = array_merge($previous_value, $meta_value);
			}
			return $meta_value;
		}
		
		/**
		 * Add a custom field value as a term meta
		 * 
		 * @since 3.4.0
		 * 
		 * @param int $new_term_id WordPress term ID
		 * @param string $custom_field_name Field name
		 * @param array $custom_field Field data
		 * @param array $custom_field_values Field values
		 */
		public function set_custom_term_field($new_term_id, $custom_field_name, $custom_field, $custom_field_values) {
			$meta_key = $custom_field_name;
			$custom_field_type = $this->get_acf_field_type($custom_field);
			$meta_values = $this->convert_custom_field_to_meta_values($custom_field, $custom_field_type, $custom_field_values);
			foreach ( $meta_values as $meta_value ) {
				$previous_value = get_term_meta($new_term_id, $meta_key, true);
				$meta_value = $this->append_value($previous_value, $meta_value);
				update_term_meta($new_term_id, $meta_key, $meta_value);
				if ( isset($this->custom_fields[$meta_key]) ) {
					update_term_meta($new_term_id, '_' . $meta_key, $this->custom_fields[$meta_key]);
				}
			}
		}
		
		/**
		 * Add a custom field value as a user meta
		 * 
		 * @since 3.4.0
		 * 
		 * @param int $new_user_id WordPress user ID
		 * @param string $custom_field_name Field name
		 * @param array $custom_field Field data
		 * @param array $custom_field_values Field values
		 * @param date $date Date
		 */
		public function set_custom_user_field($new_user_id, $custom_field_name, $custom_field, $custom_field_values, $date='') {
			$meta_key = apply_filters('fgd2wp_get_user_meta_key', $custom_field_name, $custom_field);
			$custom_field_type = $this->get_acf_field_type($custom_field);
			$meta_values = $this->convert_custom_field_to_meta_values($custom_field, $custom_field_type, $custom_field_values, $date);
			foreach ( $meta_values as $meta_value ) {
				update_user_meta($new_user_id, $meta_key, $meta_value);
				if ( isset($this->custom_fields[$meta_key]) ) {
					update_user_meta($new_user_id, '_' . $meta_key, $this->custom_fields[$meta_key]);
				}
			}
		}
		
		/**
		 * Convert custom field values to meta values
		 * 
		 * @param array $custom_field Field data
		 * @param string $custom_field_type Field type
		 * @param array $custom_field_values Field values
		 * @param date $date Date
		 * @param int $new_post_id WordPress post ID
		 * @return array Meta values
		 */
		private function convert_custom_field_to_meta_values($custom_field, $custom_field_type, $custom_field_values, $date='', $new_post_id='') {
			$meta_values = array();
			switch ( $custom_field_type ) {
				// Date
				case 'date_picker':
				case 'date_time_picker':
					foreach ( $custom_field_values as $custom_field_value ) {
						if ( is_array($custom_field_value) ) {
							foreach ( $custom_field_value as $field_name => $subvalue ) {
								if ( !preg_match('/_rrule$/', $field_name) ) { // Don't import the "rule" field
									$meta_values[] = $this->convert_to_date($subvalue);
								}
							}
						} else {
							$meta_values[] = $this->convert_to_date($custom_field_value);
						}
					}
					break;

				// Image
				case 'image':
				case 'gallery':
				case 'file':
					if ( !$this->plugin->plugin_options['skip_media'] ) {
						foreach ( $custom_field_values as $file ) {
							$attachment_id = $this->plugin->import_attachment($new_post_id, $file, $date);
							if ( $attachment_id ) {
								// Set the field value
								$meta_values[] = $attachment_id;
							}
						}
					}
					break;

				// URL
				case 'url':
					foreach ( $custom_field_values as $custom_field_value ) {
						$url = isset($custom_field_value['url'])? $custom_field_value['url'] : (isset($custom_field_value['uri'])? $custom_field_value['uri'] : '');
						$url = $this->plugin->get_path_from_uri($url);
						$meta_value = $url;
						if ( !$this->plugin->plugin_options['skip_media'] ) {
							if ( preg_match('#' . preg_quote($this->plugin->plugin_options['url']) . '#', $url) ) {
								// Import the file
								$file_date = isset($custom_field_value['timestamp'])? date('Y-m-d H:i:s', $custom_field_value['timestamp']) : $date;
								$attachment_id = $this->plugin->import_media($custom_field_value['filename'], $url, $file_date);
								if ( $attachment_id ) {
									$meta_value = wp_get_attachment_url($attachment_id);
								}
							}
						}
						$meta_values[] = $meta_value;
					}
					break;
					
				// Link
				case 'link':
					foreach ( $custom_field_values as $custom_field_value ) {
						$title = isset($custom_field_value['title'])? $custom_field_value['title'] : (isset($custom_field_value['filename'])? $custom_field_value['filename'] : '');
						$url = isset($custom_field_value['url'])? $custom_field_value['url'] : (isset($custom_field_value['uri'])? $custom_field_value['uri'] : '');
						$meta_values[] = array(
							'title' => $title,
							'url' => $this->plugin->get_path_from_uri($url),
							'target' => '_blank',
						);
					}
					break;
					
				// Checkbox or select boxes
				case 'checkbox':
				case 'select':
					if ( isset($custom_field['options']) && is_array($custom_field['options']) ) {
						$options = array_keys($custom_field['options']);
						$acf_values = array();
						foreach ( $custom_field_values as $values ) {
							if ( is_array($values) ) {
								foreach ( $values as $value ) {
									if ( in_array($value, $options) ) {
										$acf_values[] = $value;
									}
								}
							} elseif ( is_scalar($values) ) {
								if ( in_array($values, $options) ) {
									$acf_values[] = $values;
								}
							}
						}
						$meta_values[] = $acf_values;
					}
					break;
				
				// Post object
				case 'post_object':
					$meta_values = $custom_field_values;
					break;
				
				// Taxonomy
				case 'taxonomy':
					if ( is_array($custom_field_values) ) {
						foreach ( $custom_field_values as $custom_field_value ) {
							if ( is_array($custom_field_value) ) {
								foreach ( $custom_field_value as $value ) {
									if ( isset($this->plugin->imported_taxonomies[$value]) ) {
										$meta_values[] = $this->plugin->imported_taxonomies[$value];
									}
								}
							} else {
								if ( isset($this->plugin->imported_taxonomies[$custom_field_value]) ) {
									$meta_values[] = $this->plugin->imported_taxonomies[$custom_field_value];
								}
							}
						}
					}
					break;
				
				// User
				case 'user':
					if ( is_array($custom_field_values) ) {
						foreach ( $custom_field_values as $custom_field_value ) {
							if ( is_array($custom_field_value) ) {
								foreach ( $custom_field_value as $value ) {
									if ( isset($this->plugin->imported_users[$value]) ) {
										$meta_values[] = $this->plugin->imported_users[$value];
									}
								}
							} else {
								if ( isset($this->plugin->imported_users[$custom_field_value]) ) {
									$meta_values[] = $this->plugin->imported_users[$custom_field_value];
								}
							}
						}
					}
					break;
				
				default:
					if ( is_array($custom_field_values) ) {
						foreach ( $custom_field_values as $custom_field_value ) {
							if ( is_array($custom_field_value) ) {
								$acf_value = implode("<br />\n", $custom_field_value);
							} else {
								$acf_value = $custom_field_value;
							}
							$acf_value = $this->plugin->replace_media_links($acf_value, $date);

							$meta_values[] = $acf_value;
						}
					} else {
						$meta_values[] = $custom_field_values;
					}
			}
			return apply_filters('fgd2wp_convert_custom_field_to_meta_values', $meta_values, $custom_field, $custom_field_values, $new_post_id, $date);
		}
		
		/**
		 * Convert a date with a MySQL format
		 * 
		 * @param mixed $date Date
		 * @return date Date
		 */
		private function convert_to_date($date) {
			if ( empty($date) ) {
				$formatted_date = $date;
			} elseif ( is_numeric($date) ) {
				$formatted_date = date('Y-m-d H:i:s', $date);
			} else {
				$formatted_date = preg_replace('/-00/', '-01', $date); // For dates with month=00 or day=00
				$formatted_date = preg_replace('/T/', ' ', $formatted_date); // For ISO date
			}
			return $formatted_date;
		}
		
		/**
		 * Set the user picture
		 * 
		 * @param int $user_id User ID
		 * @param int $image_id Image ID
		 */
		public function set_user_picture($user_id, $image_id) {
			add_user_meta($user_id, $this->get_field_prefix() . 'picture', $image_id);
		}
		
		/**
		 * Set a post relationship
		 * 
		 * @since 3.4.0
		 * 
		 * @param int $post_id Post ID
		 * @param string $custom_field_name Custom field name
		 * @param int $related_id Related post ID
		 * @param array $custom_field Custom field
		 * @param string $relationship_slug Relationship slug (Toolset only)
		 */
		public function set_post_relationship($post_id, $custom_field_name, $related_id, $custom_field, $relationship_slug) {
			$this->set_custom_post_field($post_id, $custom_field_name, $custom_field, array($related_id));
		}
		
	}
}
