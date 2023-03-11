<?php

namespace ORCOptions\Includes;

defined( 'ABSPATH' ) or die;

class Options {

	private static $options = array();
	/**
	 * Get the options from the database initializing any missing options and setting the values
	 * in the options array for program use.
	 */
	private static function initialize_options() {
		self::$options[ Config::PHONE ]     = esc_js( get_option( Config::PHONE ) );
		self::$options[ Config::TOLL_FREE ] = esc_js( get_option( Config::TOLL_FREE ) );
		self::$options[ Config::TEXT ]      = esc_js( get_option( Config::TEXT ) );
		self::$options[ Config::FAX ]       = esc_js( get_option( Config::FAX ) );

		self::$options[ Config::INTAKE_ID ]         = esc_js( get_option( Config::INTAKE_ID ) );
		self::$options[ Config::COMMUNICATIONS_ID ] = esc_js( get_option( Config::COMMUNICATIONS_ID ) );
		self::$options[ Config::HR_ID ]             = esc_js( get_option( Config::HR_ID ) );
		self::$options[ Config::ALUMNI_ID ]         = esc_js( get_option( Config::ALUMNI_ID ) );
		self::$options[ Config::WEBSITE_ID ]        = esc_js( get_option( Config::WEBSITE_ID ) );
		self::$options[ Config::PRIVACY_ID ]        = esc_js( get_option( Config::PRIVACY_ID ) );

		self::$options[ Config::MAIN ] = esc_js( get_option( Config::MAIN ) );
		self::$options[ Config::XMAS ] = esc_js( get_option( Config::XMAS ) );

		self::$options[ Config::GOOGLE ]     = esc_js( get_option( Config::GOOGLE ) );
		self::$options[ Config::GOOGLE_TAG ] = esc_js( get_option( Config::GOOGLE_TAG ) );
		self::$options[ Config::FACEBOOK ]   = esc_js( get_option( Config::FACEBOOK ) );
		self::$options[ Config::PIXEL ]      = esc_js( get_option( Config::PIXEL ) );
		self::$options[ Config::BING ]       = esc_js( get_option( Config::BING ) );
		self::$options[ Config::LINKEDIN ]   = esc_js( get_option( Config::LINKEDIN ) );
		self::$options[ Config::TWITTER ]    = esc_js( get_option( Config::TWITTER ) );

		self::$options[ Config::ORG ]   = esc_js( get_option( Config::ORG ) );
		self::$options[ Config::LOCAL ] = esc_js( get_option( Config::LOCAL ) );

		self::$options[ Config::ADMINISTRATIVE ] = esc_js( get_option( Config::ADMINISTRATIVE ) );
		self::$options[ Config::CLINICAL ]       = esc_js( get_option( Config::CLINICAL ) );
		self::$options[ Config::MEDICAL ]        = esc_js( get_option( Config::MEDICAL ) );
		self::$options[ Config::RECOVERY ]       = esc_js( get_option( Config::RECOVERY ) );
		self::$options[ Config::SUPPORT ]        = esc_js( get_option( Config::SUPPORT ) );
		self::$options[ Config::WELLNESS ]       = esc_js( get_option( Config::WELLNESS ) );
	}
	/**
	 * Initialize the options for the plugin.
	 */
	public static function initialize() {
		self::initialize_options();
	}
	/**
	 * Return all the options used by the plugin.
	 *
	 * @return array
	 */
	public static function getOptions() {
		return self::$options;
	}
	/**
	 * Return a single option used by the plugin.
	 *
	 * @param string $option
	 * @return string
	 */
	public static function getOption( $option ) {
		return self::$options[ $option ];
	}
	/**
	 * Return the plugin version.
	 *
	 * @return string
	 */
	public static function getVersion() {
		return Config::PLUGIN_VERSION;
	}


}
