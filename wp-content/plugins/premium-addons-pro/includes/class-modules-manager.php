<?php
/**
 * PAPRO Manager.
 */

namespace PremiumAddonsPro\Includes;

use PremiumAddonsPro\Base\Module_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Manager.
 */
final class Manager {

	/**
	 * Modules
	 *
	 * @var modules
	 */
	private $modules = array();

	/**
	 * Require Files.
	 *
	 * @since 1.6.1
	 * @access public
	 *
	 * @return void
	 */
	public function require_files() {
		require PREMIUM_PRO_ADDONS_PATH . 'base/module-base.php';
	}

	/**
	 * Register Modules.
	 *
	 * @since 1.6.1
	 * @access public
	 *
	 * @return void
	 */
	public function register_modules() {

		$modules = array(
			'premium-section-parallax',
			'premium-section-particles',
			'premium-section-gradient',
			'premium-section-kenburns',
			'premium-section-lottie',
			'premium-section-blob',
			'premium-global-cursor',
			'premium-global-badge',
			'premium-global-mscroll',
		);

		foreach ( $modules as $module_name ) {
			$class_name = str_replace( '-', ' ', $module_name );

			$class_name = str_replace( ' ', '', ucwords( $class_name ) );

			$class_name = 'PremiumAddonsPro\\Modules\\' . $class_name . '\Module';

			if ( $class_name::is_active() ) {
				$this->modules[ $module_name ] = $class_name::instance();
			}
		}

	}

	/**
	 * Run Modules Extender
	 *
	 * Extendes the free modules with extra options
	 *
	 * @since 2.6.0
	 * @access public
	 */
	public function run_modules_extender() {

		add_filter( 'papro_activated', '__return_true' );

		add_action( 'pa_floating_opacity_controls', array( $this, 'add_opacity_controls' ) );
		add_action( 'pa_floating_bg_controls', array( $this, 'add_bg_controls' ) );

		add_action( 'pa_floating_blur_controls', array( $this, 'add_blur_controls' ) );
		add_action( 'pa_floating_contrast_controls', array( $this, 'add_contrast_controls' ) );
		add_action( 'pa_floating_gs_controls', array( $this, 'add_gs_controls' ) );
		add_action( 'pa_floating_hue_controls', array( $this, 'add_hue_controls' ) );
		add_action( 'pa_floating_brightness_controls', array( $this, 'add_brightness_controls' ) );
		add_action( 'pa_floating_saturation_controls', array( $this, 'add_saturation_controls' ) );

		add_action( 'pa_custom_menu_controls', array( $this, 'add_custom_menu_controls' ), 10, 2 );

		// Extend Display Conditions Module.
		add_filter( 'pa_display_conditions', array( $this, 'extend_display_conditions_options' ) );
		add_filter( 'pa_display_conditions_keys', array( $this, 'extend_display_conditions_keys' ) );
		add_filter( 'pa_pro_display_conditions', array( $this, 'extend_pro_display_conditions' ) );

		// Extend Woo Product Listings Skins.
		add_filter( 'pa_pro_woo_skins', array( $this, 'extend_woo_skins' ) );

		// Extend Mega Menu - Random Badges.
		add_action( 'pa_rn_badges_controls', array( $this, 'add_random_badges_controls' ), 10, 2 );
		add_filter( 'pa_get_random_badges_settings', array( $this, 'get_random_badges_settings' ), 10 );

		// Extend Google Maps - Advanced Marker.
		add_action( 'pa_maps_marker_controls', array( $this, 'add_maps_marker_controls' ) );

	}

	/**
	 * Get random badges settings.
	 *
	 * @since 2.8.10
	 * @access public
	 *
	 * @param array $settings widget settings.
	 *
	 * @return array $badges_settings settings.
	 */
	public function get_random_badges_settings( $settings ) {

		$badges = $settings['rn_badges'];

		$badges_settings = array();

		foreach ( $badges as $index => $badge ) {

			$options = array(
				'id'       => $badge['_id'],
				'text'     => $badge['rn_badge_text'],
				'max'      => $badge['rn_badge_max'],
				'selector' => $badge['rn_badge_target'],
			);

			array_push( $badges_settings, $options );
		}

		return $badges_settings;

	}

	/**
	 * Add Random Badges Controls
	 *
	 * @since 2.8.10
	 * @access public
	 *
	 * @param object $element elementor element.
	 */
	public function add_random_badges_controls( $element ) {

		$element->add_control(
			'rn_badge_enabled',
			array(
				'label'       => __( 'Enable Random Badges', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'This option allows you to add badges that appear randomly on your menu items', 'premium-addons-for-elementor' ),
			)
		);

		$badges = new Repeater();

		$badges->add_control(
			'rn_badge_text',
			array(
				'label'   => __( 'Text', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'New', 'premium-addons-for-elementor' ),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$badges->add_control(
			'rn_badge_target',
			array(
				'label'   => __( 'CSS Selector', 'premium-addons-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$badges->add_control(
			'rn_badge_max',
			array(
				'label'       => __( 'Max Number to Apply This Badge', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set a maximum number that this badge should show.', 'premium-addons-for-elementor' ),
				'default'     => 3,

			)
		);

		$badges->add_control(
			'rn_badge_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$badges->add_control(
			'rn_badge_bg',
			array(
				'label'     => __( 'Backgroud Color', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'background: {{VALUE}} !important;',
				),
			)
		);

		$element->add_control(
			'rn_badges',
			array(
				'label'         => __( 'Badges', 'premium-addons-pro' ),
				'type'          => Controls_Manager::REPEATER,
				'show_label'    => true,
				'fields'        => $badges->get_controls(),
				'title_field'   => '{{{ rn_badge_text }}}',
				'separator'     => 'after',
				'prevent_empty' => false,
				'condition'     => array(
					'rn_badge_enabled' => 'yes',
				),
			)
		);
	}

	/**
	 * Add Custom Menu Controls
	 * Adds repeater controls for mega menu widget.
	 *
	 * @access public
	 * @since 2.7.6
	 *
	 * @param object $elem elementor element.
	 * @param object $repeater repeater element.
	 */
	public function add_custom_menu_controls( $elem, $repeater ) {

		$elem->add_control(
			'menu_items',
			array(
				'label'       => __( 'Menu Items', 'premium-addons-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'show_label'  => true,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'item_type' => 'menu',
						'text'      => __( 'Menu Item 1', 'premium-addons-pro' ),
					),
					array(
						'item_type' => 'submenu',
						'text'      => __( 'Sub Menu', 'premium-addons-pro' ),
					),
					array(
						'item_type' => 'menu',
						'text'      => __( 'Menu Item 2', 'premium-addons-pro' ),
					),
					array(
						'item_type' => 'submenu',
						'text'      => __( 'Sub Menu', 'premium-addons-pro' ),
					),
				),
				'title_field' => '{{{ text }}}',
				'separator'   => 'before',
				'condition'   => array(
					'menu_type' => 'custom',
				),
			)
		);

	}

	/**
	 * Extend woo skins.
	 * Removes the ( PRO ) label from woo skins' title.
	 *
	 * @access public
	 * @since 2.6.6
	 *
	 * @param string $skin skin title.
	 *
	 * @return string
	 */
	public function extend_woo_skins( $skin ) {
		return str_replace( ' ( PRO )', '', $skin );
	}

	/**
	 * Run Modules Extender
	 *
	 * Extendes the free modules with extra options
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param array $data conditions data.
	 */
	public function extend_display_conditions_options( $data ) {

		$conditions = $data;

		$conditions['urlparams']['label'] = __( 'URL', 'premium-addons-pro' );
		$conditions['misc']['label']      = __( 'Misc', 'premium-addons-pro' );

		if ( class_exists( 'woocommerce' ) ) {
			$conditions['woocommerce']['label'] = __( 'WooCommerce', 'premium-addons-pro' );
		}

		if ( class_exists( 'ACF' ) ) {
			$conditions['acf']['label'] = __( 'ACF', 'premium-addons-pro' );
		}

		$data = $conditions;

		return $data;

	}

	/**
	 * Extend Display Conditions Keys
	 *
	 * Extends display conditions modules keys used to register controls
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param array $keys conditions keys.
	 */
	public function extend_display_conditions_keys( $keys ) {

		$keys = array_merge(
			array(
				'url_string',
				'url_referer',
				'shortcode',
			),
			$keys
		);

		if ( class_exists( 'ACF' ) ) {

			$keys = array_merge(
				array(
					'acf_text',
					'acf_boolean',
					'acf_choice',
				),
				$keys
			);

		}

		if ( class_exists( 'woocommerce' ) ) {

			$keys = array_merge(
				array(
					'woo_cat_page',
					'woo_product_cat',
					'woo_product_price',
					'woo_product_stock',
					'woo_orders',
					'woo_category',
					'woo_last_purchase',
					'woo_total_price',
					'woo_cart_products',
				),
				$keys
			);

		}

		return $keys;
	}

	/**
	 * Changes the conditions for display conditions options
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param array $conditions controls conditions.
	 */
	public function extend_pro_display_conditions( $conditions ) {

		$options_conditions = array( '' );

		return $options_conditions;

	}

	/**
	 * Add Opacity Controls
	 *
	 * Extends Floating Effects Opacity controls.
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_opacity_controls( $elem ) {

		$elem->add_control(
			'premium_fe_opacity',
			array(
				'label'     => __( 'Value', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 50,
					),
					'unit'  => '%',
				),
				'labels'    => array(
					__( 'From', 'premium-addons-pro' ),
					__( 'To', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'         => 'yes',
					'premium_fe_opacity_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_opacity_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'         => 'yes',
					'premium_fe_opacity_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_opacity_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'         => 'yes',
					'premium_fe_opacity_switcher' => 'yes',
				),

			)
		);

	}

	/**
	 * Add Background Controls
	 *
	 * Extends Floating Effects Background controls.
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_bg_controls( $elem ) {

		$elem->add_control(
			'premium_fe_bg_color_from',
			array(
				'label'     => __( 'From', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_bg_color_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_bg_color_to',
			array(
				'label'     => __( 'To', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_bg_color_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_bg_color_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_bg_color_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_bg_color_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_bg_color_switcher' => 'yes',
				),

			)
		);

	}

	/**
	 * Add Blur Controls
	 *
	 * Extends Floating Effects Blur controls.
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_blur_controls( $elem ) {

		$elem->add_control(
			'premium_fe_blur_val',
			array(
				'label'     => __( 'Value', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 1,
					),
					'unit'  => 'px',
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 3,
						'step' => 0.1,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-pro' ),
					__( 'To', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'      => 'yes',
					'premium_fe_blur_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_blur_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'      => 'yes',
					'premium_fe_blur_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_blur_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'      => 'yes',
					'premium_fe_blur_switcher' => 'yes',
				),

			)
		);

	}

	/**
	 * Add Contrast Controls
	 *
	 * Extends Floating Effects Contrast controls.
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_contrast_controls( $elem ) {

		$elem->add_control(
			'premium_fe_contrast_val',
			array(
				'label'     => __( 'Value', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 50,
					),
					'unit'  => '%',
				),
				'range'     => array(
					'%' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 10,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-pro' ),
					__( 'To', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_contrast_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_contrast_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_contrast_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_contrast_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_contrast_switcher' => 'yes',
				),

			)
		);

	}

	/**
	 * Add Grayscale Controls
	 *
	 * Extends Floating Effects Grayscale controls.
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_gs_controls( $elem ) {

		$elem->add_control(
			'premium_fe_gScale_val',
			array(
				'label'     => __( 'Value', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 50,
					),
					'unit'  => '%',
				),
				'labels'    => array(
					__( 'From', 'premium-addons-pro' ),
					__( 'To', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'        => 'yes',
					'premium_fe_gScale_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_gScale_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'        => 'yes',
					'premium_fe_gScale_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_gScale_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'        => 'yes',
					'premium_fe_gScale_switcher' => 'yes',
				),

			)
		);

	}

	/**
	 * Add Hue Controls
	 *
	 * Extends Floating Effects Hue controls.
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_hue_controls( $elem ) {

		$elem->add_control(
			'premium_fe_hue_val',
			array(
				'label'     => __( 'Value', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 90,
					),
					'unit'  => 'deg',
				),
				'range'     => array(
					'deg' => array(
						'min'  => 0,
						'max'  => 360,
						'step' => 10,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-pro' ),
					__( 'To', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'     => 'yes',
					'premium_fe_hue_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_hue_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'     => 'yes',
					'premium_fe_hue_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_hue_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'     => 'yes',
					'premium_fe_hue_switcher' => 'yes',
				),

			)
		);

	}

	/**
	 * Add Brightness Controls
	 *
	 * Extends Floating Effects Brightness controls.
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_brightness_controls( $elem ) {

		$elem->add_control(
			'premium_fe_brightness_val',
			array(
				'label'     => __( 'Value', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 50,
					),
					'unit'  => '%',
				),
				'range'     => array(
					'%' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 10,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-pro' ),
					__( 'To', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'            => 'yes',
					'premium_fe_brightness_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_brightness_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'            => 'yes',
					'premium_fe_brightness_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_brightness_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'            => 'yes',
					'premium_fe_brightness_switcher' => 'yes',
				),

			)
		);

	}

	/**
	 * Add Saturation Controls
	 *
	 * Extends Floating Effects Saturation controls.
	 *
	 * @since 2.6.0
	 * @access public
	 *
	 * @param object $elem elementor element.
	 */
	public function add_saturation_controls( $elem ) {

		$elem->add_control(
			'premium_fe_saturate_val',
			array(
				'label'     => __( 'Value', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'sizes' => array(
						'from' => 0,
						'to'   => 50,
					),
					'unit'  => '%',
				),
				'range'     => array(
					'%' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 10,
					),
				),
				'labels'    => array(
					__( 'From', 'premium-addons-pro' ),
					__( 'To', 'premium-addons-pro' ),
				),
				'scales'    => 1,
				'handles'   => 'range',
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_saturate_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_saturate_duration',
			array(
				'label'     => __( 'Duration', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'default'   => array(
					'unit' => 'px',
					'size' => 1000,
				),
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_saturate_switcher' => 'yes',
				),
			)
		);

		$elem->add_control(
			'premium_fe_saturate_delay',
			array(
				'label'     => __( 'Delay', 'premium-addons-pro' ) . ' (ms)',
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					),
				),
				'condition' => array(
					'premium_fe_switcher'          => 'yes',
					'premium_fe_saturate_switcher' => 'yes',
				),

			)
		);

	}

	/**
	 * Add Maps Marker Controls
	 *
	 * @since 2.8.20
	 * @access public
	 *
	 * @param object $element elementor element.
	 */
	public function add_maps_marker_controls( $element ) {

		$element->add_control(
			'marker_skin',
			array(
				'label'     => __( 'Skin', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'skin1' => __( 'Google Default', 'premium-addons-for-elementor' ),
					'skin3' => __( 'Inline Skin', 'premium-addons-for-elementor' ),
					'skin2' => __( 'Block Skin', 'premium-addons-for-elementor' ),
				),
				'default'   => 'skin1',
				'condition' => array(
					'advanced_view' => 'yes',
				),
			)
		);

		$element->add_control(
			'pin_img',
			array(
				'label'     => __( 'Image', 'premium-addons-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'advanced_view' => 'yes',
				),
			)
		);

		$element->add_control(
			'pin_address',
			array(
				'label'       => __( 'Address', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '20 W 34th St., New York, NY, USA',
				'condition'   => array(
					'advanced_view' => 'yes',
				),
			)
		);

		$element->add_control(
			'pin_website',
			array(
				'label'       => __( 'Website', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'www.premiumaddons.com',
				'condition'   => array(
					'advanced_view' => 'yes',
				),
			)
		);

		$element->add_control(
			'pin_phone',
			array(
				'label'       => __( 'Phone Number', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '+12127363100',
				'condition'   => array(
					'advanced_view' => 'yes',
				),
			)
		);

		$element->add_control(
			'pin_hours',
			array(
				'label'       => __( 'Working Hours', 'premium-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '10AM-10PM',
				'condition'   => array(
					'advanced_view' => 'yes',
				),
			)
		);

	}


	/**
	 * Class Constructor
	 */
	public function __construct() {

		$this->require_files();
		$this->register_modules();

		$this->run_modules_extender();

	}

}
