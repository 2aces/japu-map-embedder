<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.japuapp.com.br/
 * @since      0.1.0
 *
 * @package    Japu_Map_Embedder
 * @subpackage Japu_Map_Embedder/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Japu_Map_Embedder
 * @subpackage Japu_Map_Embedder/public
 * @author     Celso Bessa <devteam@japuapp.com.br>
 */
class Japu_Map_Embedder_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Japu_Map_Embedder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Japu_Map_Embedder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( is_page() || is_single() ){
			global $post;
			$add_japu_map = get_post_meta($post->ID, 'add_japu_map', true);
			if ( empty($add_japu_map) || true !== (bool)$add_japu_map) {
				return;
			}
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/japu-map-embedder-public.css', array(), $this->version, 'all');
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Japu_Map_Embedder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Japu_Map_Embedder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( is_page() || is_single() ){
			global $post;
			$add_japu_map = get_post_meta($post->ID, 'add_japu_map', true);
			if ( empty($add_japu_map) || true !== (bool)$add_japu_map) {
				return;
			}
			wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/japu-map-embedder-public.js', array('jquery'), $this->version, true);
		}

	}

	/**
	 * @summary        Adds japumap shortcode
	 *
	 * @description    Adds japumap shortcode, which will show an Japu - Rotas das Vertentes map inside an iframe. If partner Id is provide and the domain is authorized with Japu App, it may have custom CSS and information.
	 *
	 * @since     0.1.0
	 * @param     array    $atts    shortcode attributes
	 *                              $partnerid    a numeric id from Japu - Rota das Vertentes
	 * @return    string   $html    an iframe html markup
	 */
	public function japumap_shortcode($atts) {
	// Attributes
		$atts = shortcode_atts(
			array(
				'partnerid' => '0',
			),
			$atts,
			'japumap'
		);

		$atts['partnerid'] = absint($atts['partnerid']);
		if ( 0 === $atts['partnerid'] ) {
			$partnerid = absint(get_option('japu_partner_id', 0));
		}

		$html = '<iframe src="https://embed.japuapp.com.br/?partnerid=' . $atts['partnerid'] . '" sandbox="allow-same-origin allow-scripts allow-forms" style="width:100%;height:66vh;border:none;" class="japumap-iframe"></iframe>';
		$html .=  '<p class="japumap-credits"><small>' . __('Map provided by <a href="https://www.japuapp.com.br">Japu - Rotas das Vertentes</a>', $this->plugin_name) . '</small></p>';
		return $html;
	}

	/**
	 * @summary        register all shortcodes
	 *
	 * @description    register all plugin shortcodes
	 *
	 * @since     0.1.0
	 * @param     void
	 * @return    void
	 */
	public function register_shortcodes() {
		add_shortcode('japumap', array( $this, 'japumap_shortcode'));
	}

	/**
	 * @summary        appends a Japu Map iframe to the content
	 *
	 * @description    appends a Japu Map iframe to the content generated by the_content on posts and pages
	 *
	 * @since     0.1.0
	 * @param     string    $content    The post content
	 * @return    void      $content    The filtered post content
	 */
	public function filter_add_japu_map($content) {
		global $post;
		if ( 'post' !== $post->post_type && 'page' !== $post->post_type ){
			return $content;
		}
		$add_japu_map = get_post_meta($post->ID, 'add_japu_map', true);
		if ( empty($add_japu_map) || true !== (bool)$add_japu_map ){
			return $content;
		}
		$partnerid = absint( get_option('japu_partner_id', 0) );
		$partner_name = get_bloginfo('name');
		$markup = '<div class="japumap-wrapper">';
		$markup .= '<iframe src="https://embed.japuapp.com.br/?partnerid=' . $partnerid . '" sandbox="allow-same-origin allow-scripts allow-forms" style="border:none;" class="japumap-iframe"></iframe>';
		$markup .= '<div class="japumap-bottom-bar">';
		$markup .= '<p class="japumap-credits">';
		$markup .= __('by <a href="https://www.japuapp.com.br">Japu - Rotas das Vertentes</a>', $this->plugin_name);
		$markup .= '<a id="japumap-toggle-full-screen">' . sprintf( __('<span class="return-text">disable fullscreen and return to %s</span>', $this->plugin_name), $partner_name ) . __('<span class="toggle-full-screen-text">enable fullscreen map</span>', $this->plugin_name) . '</a>';
		$markup .= '</p>';
		$markup .= '</div>';
		$markup .= '</div>';
		$content .= $markup;
		return $content;
	}

}
