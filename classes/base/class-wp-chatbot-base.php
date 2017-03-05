<?php


if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'WP_Chatbot_base' ) ):

class WP_Chatbot_base {


  /**
   * The one and only plugin instance
   */
  protected static $instance;

  /**
   * The plugin directory without trailing slash
   */
  public $path;

  /**
   * The plugin url without trailing slash
   */
  public $url;

	/**
	 * The plugin name
	 */
	public $name;

  /**
   * The plugin slug
   */
  public $slug;

  /**
   * The plugin version
   */
  public $version;


  /**
   * Constructor of the class
   *
   * Does some lifing, then calls init().
   * All plugin construct functionality should be in method init().
   */
  protected function __construct() {

    $this->path = untrailingslashit( plugin_dir_path( $this->get_class_filename( ) ) );
    $this->url  = untrailingslashit( plugin_dir_path( $this->get_class_filename( ) ) );
		$this->name = str_replace( '_', ' ', get_class( $this ) );
    $this->slug = strtolower( str_replace( '_', '-', get_class( $this ) ) );
    $this->version = '1.0.0';

    // l18n
    add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

  }

  /**
   * Get the filename of the file of the last inherited class
   * From: http://stackoverflow.com/questions/3014254/how-to-get-the-path-of-a-derived-class-from-an-inherited-method
   */
  protected function get_class_filename() {
    $reflection_class = new ReflectionClass( get_class( $this ) );
    return $reflection_class->getFileName( );
  }

  public function load_plugin_textdomain() {

    load_plugin_textdomain(
      $this->slug,
      false,
      $this->path . '/languages/'
    );

  }


	public function require( $filename ){

		$filepath = $this->path . ( '/' == $filename[0] ? '' : '/' ) . $filename;

		if( file_exists( $filepath ) )
			require_once $filepath;

	}

}



endif; // WP_Chatbot_base exists
