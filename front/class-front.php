<?php
/**
 * The public area functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Code_Inserter
 * @subpackage Code_Inserter/Front
 */

namespace Code_Inserter\Front;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       /
 * @since      1.0.0
 *
 * @package    Code_Inserter
 * @subpackage Code_Inserter/Front
 */
class Front {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name
	 */
	private $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version
	 */
	private $version;

	/**
	 * The plugin settings for current domain.
	 *
	 * @var array $options
	 */
	private $options;

	/**
	 *  Amp status: true if current page is amp.
	 *
	 * @var bool $amp
	 */
	private $amp;

	/**
	 * Public constructor.
	 *
	 * @param string $plugin_name The unique identifier of this plugin.
	 * @param string $version     The current version of the plugin.
	 * @param array  $options     The plugin settings for current domain.
	 * @param bool   $amp         Amp status: true if current page is amp.
	 */
	public function __construct( string $plugin_name, string $version, array $options, bool $amp ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$host              = str_replace( 'www.', '', filter_var( isset( $_SERVER['HTTP_HOST'] ) ? wp_unslash( $_SERVER['HTTP_HOST'] ) : '', FILTER_SANITIZE_URL ) );
		$this->options     = ! empty( $options[ $host ] ) ? $options[ $host ] : [];
		$this->amp         = $amp;
	}

	/**
	 * Run front actions and filters
	 */
	public function hooks() {
		add_action( 'the_content', [ $this, 'content' ], 0 );
		add_action( 'template_redirect', [ $this, 'buffer_start' ], 0 );
		add_action( 'shutdown', [ $this, 'buffer_end' ], 1000 );
	}

	/**
	 * Buffer start
	 *
	 * @add_action('template_redirect', 'buffer_start')
	 */
	public function buffer_start() {
		ob_start( [ &$this, 'buffer' ] );
	}

	/**
	 * Buffer flush
	 *
	 * @add_action('shutdown', 'buffer_end')
	 */
	public function buffer_end() {
		if ( ! empty( ob_get_level() ) ) {
			ob_end_flush();
		}
	}

	/**
	 * Add text to page content
	 *
	 * @param string $html HTML string.
	 *
	 * @return string
	 */
	private function buffer( string $html ): string {
		if ( ! empty( $this->options['header_after']['content'] ) ) {
			if ( false === $this->amp || 0 === $this->options['header_after']['disable_on_amp'] ) {
				$html = str_replace( '<head>', '<head>' . $this->options['header_after']['content'], $html );
			}
		}
		if ( ! empty( $this->options['header']['content'] ) ) {
			if ( false === $this->amp || 0 === $this->options['header']['disable_on_amp'] ) {
				$html = str_replace( '</head>', $this->options['header']['content'] . '</head>', $html );
			}
		}
		if ( ! empty( $this->options['title_after_post']['content'] ) ) {
			if ( is_singular( 'post' ) && false === $this->amp || is_singular( 'post' ) && 0 === $this->options['title_after_post']['disable_on_amp'] ) {
				$html = preg_replace( '/(\<h1.*?\/h1\>)/', '$1' . wpautop( $this->options['title_after_post']['content'], false ), $html );
			}
		}

		if ( ! empty( $this->options['title_after_page']['content'] ) ) {
			if ( is_singular( ( 'page' ) ) || is_front_page() || is_home() ) {
				if ( false === $this->amp || 0 === $this->options['title_after_page']['disable_on_amp'] ) {
					$html = preg_replace( '/(\<h1.*?\/h1\>)/', '$1' . wpautop( $this->options['title_after_page']['content'], false ), $html );
				}
			}
		}

		if ( ! empty( $this->options['title_after_category']['content'] ) ) {
			if ( is_category() && false === $this->amp || is_category() && 0 === $this->options['title_after_category']['disable_on_amp'] ) {
				$html = preg_replace( '/(\<h1.*?\/h1\>)/', '$1' . wpautop( $this->options['title_after_category']['content'], false ), $html );
			}
		}

		if ( ! empty( $this->options['body']['content'] ) ) {
			if ( false === $this->amp || 0 === $this->options['body']['disable_on_amp'] ) {
				$html = preg_replace( '/(\<body.*?\>)/', '$1' . wpautop( $this->options['body']['content'], false ), $html );
			}
		}
		if ( ! empty( $this->options['body_before']['content'] ) ) {
			if ( false === $this->amp || 0 === $this->options['body_before']['disable_on_amp'] ) {
				$html = preg_replace( '/(\<\/body.*?\>)/', wpautop( $this->options['body_before']['content'], false ) . '$1', $html );
			}
		}

		if ( ! empty( $this->options['footer']['content'] ) ) {
			if ( false === $this->amp || 0 === $this->options['footer']['disable_on_amp'] ) {
				$html = preg_replace( '/(\<\/footer.*?\>)/', wpautop( $this->options['footer']['content'], false ) . '$1', $html );
			}
		}

		if ( ! empty( $this->options['gtm']['content'] ) ) {
			$code = '<!-- Google Tag Manager -->
                        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push(
                        {\'gtm.start\': new Date().getTime(),event:\'gtm.js\'}
                        );var f=d.getElementsByTagName(s)[0],
                        j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=
                        \'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);
                        })(window,document,\'script\',\'dataLayer\',\'' . $this->options['gtm']['content'] . '\');</script>
                        <!-- End Google Tag Manager -->';
			$html = preg_replace( '/(\<head\>)/', '$1' . $code, $html );

			$code = '<!-- Google Tag Manager (noscript) -->
                        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . $this->options['gtm']['content'] . '"
                        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
                        <!-- End Google Tag Manager (noscript) -->';

			$html = preg_replace( '/(\<body.*?\>)/', '$1' . $code, $html );
		}

		return $html;
	}

	/**
	 * Insert code after and before content
	 *
	 * @param string $content Content from wp editor.
	 *
	 * @return string
	 *
	 * @add_action('the_content', 'content')
	 */
	public function content( string $content ): string {
		if ( is_singular( 'page' ) || is_singular( 'post' ) || is_front_page() || is_home() ) {
			if ( ! empty( $this->options['before_content']['content'] ) && false === $this->amp || ! empty( $this->options['before_content']['content'] ) && 0 === $this->options['before_content']['disable_on_amp'] ) {
				$content = $this->options['before_content']['content'] . $content;
			}
			if ( ! empty( $this->options['after_content']['content'] ) && false === $this->amp || ! empty( $this->options['after_content']['content'] ) && 0 === $this->options['after_content']['disable_on_amp'] ) {
				$content .= $this->options['after_content']['content'];
			}

			return $content;
		}

		return $content;
	}

}
