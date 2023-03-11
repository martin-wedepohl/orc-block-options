<?php
/*
Plugin Name: Orchard Recovery Center Options
Plugin URI:
Description: Optional information used in Orchard Recovery Center website
Version: 3.0.0
Author: Martin Wedepohl
Author URI: https://wedepohlengineering.com
License: GPLv2 or later
*/

namespace ORCOptions;

defined( 'ABSPATH' ) || die;

require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

use ORCOptions\Includes\Config;
use ORCOptions\Includes\Settings;
use ORCOptions\Includes\Options;

/**
 * Class for all the options in the ORC plugin
 */
class ORCOptions {

	/**
	 * Class constructor
	 */
	public function __construct() {

		register_activation_hook( __FILE__, array( $this, 'orc_options_activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'orc_options_deactivation' ) );
		add_action( 'theme_prefix_rewrite_flush', array( $this, 'theme_prefix_rewrite_flush' ) );

		// Enqueue styles and scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'orc_options_admin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'orc_options_add_scripts' ) );

		// Add the custom ORC code.
		add_action( 'init', array( $this, 'orc_custom_init_actions' ) );

		// Allow widgets to use short codes.
		add_filter( 'widget_text', 'do_shortcode' );

		// Allow excerpts in pages.
		add_post_type_support( 'page', 'excerpt' );

		add_action( 'init', array( $this, 'orc_page_settings' ) );
		add_action( 'admin_init', array( $this, 'orc_disable_comments_post_types_support' ) );
		add_filter( 'comments_open', array( $this, 'orc_disable_comments_status' ) );
		add_filter( 'pings_open', array( $this, 'orc_disable_comments_status' ) );
		add_filter( 'comments_array', array( $this, 'orc_disable_comments_hide_existing_comments', 10, 2 ) );
		add_action( 'admin_menu', array( $this, 'orc_disable_comments_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'orc_disable_comments_admin_menu_redirect' ) );
		add_action( 'admin_init', array( $this, 'orc_disable_comments_dashboard' ) );
		add_action( 'admin_init', array( $this, 'orc_disable_comments_admin_bar' ) );
		add_action( 'wp_head', array( $this, 'orc_add_googleanalytics' ), 0 );
		add_action( 'wp_head', array( $this, 'orc_add_schemas' ) );
		add_action( 'wp_head', array( $this, 'orc_add_facebookpixel' ) );
		add_action( 'wp_head', array( $this, 'orc_add_bingtracking' ) );
		add_action( 'wp_footer', array( $this, 'orc_add_linkedin' ) );
		add_action( 'wp_footer', array( $this, 'orc_add_twitter' ) );
		add_filter( 'wpseo_json_ld_output', '__return_false' );
		remove_action( 'wp_head', array( $this, 'rsd_link' ) );
		remove_action( 'wp_head', array( $this, 'wlwmanifest_link' ) );
		remove_action( 'wp_head', array( $this, 'wp_generator' ) );
		remove_action( 'wp_head', array( $this, 'wp_shortlink_wp_head' ) );
		remove_action( 'wp_head', array( $this, 'feed_links', 2 ) );
		remove_action( 'wp_head', array( $this, 'feed_links_extra', 3 ) );
		add_filter( 'em_content', array( $this, 'my_em_custom_content' ) );

		Options::initialize();
		$this->register_settings_page();
	}

	/**
	 * Called on plugin activation
	 * Enables the cron to delete messages in the flamingo plugin
	 */
	public function orc_options_activation() {
		flush_rewrite_rules();
	}

	/**
	 * Called on plugin deactivation
	 * Turn off the cron to delete messages in the flamingo plugin
	 */
	public function orc_options_deactivation() {
		flush_rewrite_rules();
	}

	/**
	 * Flush the database
	 */
	public function theme_prefix_rewrite_flush() {
		flush_rewrite_rules();
	}

	/**
	 * Called to enqueue the admin styles used in this plugin
	 */
	public function orc_options_admin_styles() {
		if ( is_admin() ) {
			wp_enqueue_style( 'orc_options', plugins_url( 'dist/css/orc_options.min.css', __FILE__ ), array(), \filemtime( plugin_dir_path( __FILE__ ) . '/dist/css/orc_options.min.css' ) );
		}
	}

	/**
	 * Add any scripts used by this plugin
	 */
	public function orc_options_add_scripts() {
		wp_enqueue_script( 'orc-contacthandler', plugins_url( 'dist/js/orc.contacthandler.min.js', __FILE__ ), array(), \filemtime( plugin_dir_path( __FILE__ ) . '/dist/js/orc.contacthandler.min.js' ), true );
		// Pass the contact form permalink to the script.
		$data = 'const contactdata = ' . wp_json_encode( array( 'contactuspage' => esc_url( get_permalink( get_page_by_title( 'Contact Us' ) ) ) ) );
		wp_add_inline_script( 'orc-contacthandler', $data, 'before' );

		if ( is_page( 'tour' ) || is_front_page() ) {
			wp_enqueue_style( 'orc-videos', plugins_url( 'dist/css/orc_videos.min.css', __FILE__ ), array(), \filemtime( plugin_dir_path( __FILE__ ) . '/dist/css/orc_videos.min.css' ) );
			wp_enqueue_script( 'orc-videos', plugins_url( 'dist/js/orc.videos.min.js', __FILE__ ), array(), \filemtime( plugin_dir_path( __FILE__ ) . '/dist/js/orc.videos.min.js' ), true );
			// Pass Video URL's into the script to display the videos.
			$videourls = array(
				'mainVideo' => get_option( 'orc_options_mainvideo' ),
				'xmasVideo' => get_option( 'orc_options_xmasvideo' ),
			);
			$data      = 'const videourls = ' . wp_json_encode( $videourls );
			wp_add_inline_script( 'orc-videos', $data, 'before' );
		}

		if ( is_page( 'staff' ) ) {
			wp_enqueue_style( 'orc-staff', plugins_url( 'dist/css/orc_staff.min.css', __FILE__ ), array(), \filemtime( plugin_dir_path( __FILE__ ) . '/dist/css/orc_staff.min.css' ) );
			wp_enqueue_script( 'orc-staff', plugins_url( 'dist/js/orc.staff.min.js', __FILE__ ), array(), \filemtime( plugin_dir_path( __FILE__ ) . '/dist/js/orc.staff.min.js' ), true );
			// Pass the excepts to the staff script.
			$excerpts = array(
				'administrative' => get_option( 'orc_options_staff_administrative_excerpt' ),
				'clinical'       => get_option( 'orc_options_staff_clinical_excerpt' ),
				'medical'        => get_option( 'orc_options_staff_medical_excerpt' ),
				'recovery'       => get_option( 'orc_options_staff_recovery_excerpt' ),
				'support'        => get_option( 'orc_options_staff_support_excerpt' ),
				'wellness'       => get_option( 'orc_options_staff_wellness_excerpt' ),
			);
			$data     = 'const staffexcerpt = ' . wp_json_encode( $excerpts );
			wp_add_inline_script( 'orc-staff', $data, 'before' );
		}
	}

	public function orc_custom_init_actions() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/shortcodes/orc-options-shortcodes.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/shortcodes/orc-options-staff-shortcodes.php';
		// require_once plugin_dir_path( __FILE__ ) . 'includes/vc-elements/orc-carousel-elements.php';
		// require_once plugin_dir_path( __FILE__ ) . 'includes/vc-elements/orc-contact-elements.php';
		// require_once plugin_dir_path( __FILE__ ) . 'includes/vc-elements/orc-post-elements.php';
	}

	/**
	 * Helper function for registering the settings page
	 */
	public function register_settings_page() {
		if ( is_admin() ) {
			$plugin_name = plugin_basename( __FILE__ );
			new Settings( $plugin_name );
		}
	}

	/**
	 * Allow categories and tags in pages
	 */
	public function orc_page_settings() {
		// Add tag metabox to page.
		register_taxonomy_for_object_type( 'post_tag', 'page' );
		// Add category metabox to page.
		register_taxonomy_for_object_type( 'category', 'page' );
	}

	/**
	 * Disable comments and pingbacks for the entire site
	 */
	public function orc_disable_comments_post_types_support() {
		$post_types = get_post_types();
		foreach ( $post_types as $post_type ) {
			if ( post_type_supports( $post_type, 'comments' ) ) {
				remove_post_type_support( $post_type, 'comments' );
				remove_post_type_support( $post_type, 'trackbacks' );
			}
		}
	}

	/**
	 * Close comments on the front-end
	 */
	public function orc_disable_comments_status() {
		return false;
	}

	/**
	 * Hide existing comments
	 *
	 * @param array $comments Array of comments.
	 */
	public function orc_disable_comments_hide_existing_comments( $comments ) {
		$comments = array();
		return $comments;
	}

	/**
	 * Remove comments page in menu
	 */
	public function orc_disable_comments_admin_menu() {
		remove_menu_page( 'edit-comments.php' );
	}

	/**
	 * Redirect any user trying to access comments page
	 */
	public function orc_disable_comments_admin_menu_redirect() {
		global $pagenow;
		if ( 'edit-comments.php' === $pagenow ) {
			wp_safe_redirect( admin_url() );
			exit();
		}
	}

	/**
	 * Remove comments metabox from dashboard
	 */
	public function orc_disable_comments_dashboard() {
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
	}

	/**
	 * Remove comments links from admin bar
	 */
	public function orc_disable_comments_admin_bar() {
		if ( is_admin_bar_showing() ) {
			remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
		}
	}

	/**
	 * Add Google Analytics Code
	 */
	public function orc_add_googleanalytics() {
		$orc_options_google_analytics = Options::getOption( 'orc_options_google_analytics' );
		$orc_options_google_tag       = Options::getOption( 'orc_options_google_tag' );
		$orc_options_wpcf7id          = Options::getOption( 'orc_options_wpcf7id' );
		$orc_options_wpcf7id_comm     = Options::getOption( 'orc_options_wpcf7id_comm' );
		$orc_options_wpcf7id_hr       = Options::getOption( 'orc_options_wpcf7id_hr' );
		$orc_options_wpcf7id_alumni   = Options::getOption( 'orc_options_wpcf7id_alumni' );
		$orc_options_wpcf7id_website  = Options::getOption( 'orc_options_wpcf7id_website' );
		$orc_options_wpcf7id_privacy  = Options::getOption( 'orc_options_wpcf7id_privacy' );

		if ( strlen( $orc_options_google_tag ) > 0 ) {
			?>
			<!-- Google Tag Manager -->
			<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
			new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
			j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
			'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
			})(window,document,'script','dataLayer','<?php echo esc_attr( $orc_options_google_tag ); ?>');</script>
			<!-- End Google Tag Manager -->
			<?php
		}

		if ( strlen( $orc_options_google_analytics ) > 0 ) {
			?>
			<!-- Global site tag (gtag.js) - Google Analytics -->
			<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $orc_options_google_analytics ); ?>"></script>
			<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());

			gtag('config', '<?php echo esc_attr( $orc_options_google_analytics ); ?>');
			</script>
			<!-- Contact Form 7 hook into Google Analytics -->
			<script>
				document.addEventListener('wpcf7mailsent', function (event) {
					var analytics_event = 'Contact Form - Unknown';

					if(<?php echo esc_attr( $orc_options_wpcf7id ); ?> == event.detail.contactFormId) {
						analytics_event = 'contact_intake';
					} else if(<?php echo esc_attr( $orc_options_wpcf7id_comm ); ?> == event.detail.contactFormId) {
						analytics_event = 'contact_communications';
					} else if(<?php echo esc_attr( $orc_options_wpcf7id_hr ); ?> == event.detail.contactFormId) {
						analytics_event = 'contact_hr';
					} else if(<?php echo esc_attr( $orc_options_wpcf7id_alumni ); ?> == event.detail.contactFormId) {
						analytics_event = 'contact_alumni';
					} else if(<?php echo esc_attr( $orc_options_wpcf7id_website ); ?> == event.detail.contactFormId) {
						analytics_event = 'contact_website';
					} else if(<?php echo esc_attr( $orc_options_wpcf7id_privacy ); ?> == event.detail.contactFormId) {
						analytics_event = 'contact_privacy';
					}

					gtag('event', analytics_event, { 'method': 'email' });
				}, false);
			</script>
			<!-- End Contact Form 7 hook -->
			<?php
		}
	}

	/**
	 * Add Required Schema's
	 * Organizational Schema is only on the home page
	 */
	public function orc_add_schemas() {
		$orc_options_org_schema   = html_entity_decode( Options::getOption( 'orc_options_org_schema' ) );
		$orc_options_org_schema   = str_replace( '\n', '', $orc_options_org_schema );
		$orc_options_org_schema   = preg_replace( '/\R+|"[^"]*"(*SKIP)(*FAIL)|\s*/', '', $orc_options_org_schema );
		$orc_options_local_schema = html_entity_decode( Options::getOption( 'orc_options_local_schema' ) );
		$orc_options_local_schema = str_replace( '\n', '', $orc_options_local_schema );
		$orc_options_local_schema = preg_replace( '/\R+|"[^"]*"(*SKIP)(*FAIL)|\s*/', '', $orc_options_local_schema );

		if ( is_front_page() && strlen( $orc_options_org_schema ) > 0 ) {
			?>
			<!-- Organizational Schema -->
			<script type="application/ld+json">
				<?php echo $orc_options_org_schema; ?>
			</script>
			<?php
		}

		if ( strlen( $orc_options_local_schema ) > 0 ) {
			?>
			<!-- Organizational Schema -->
			<script type="application/ld+json">
				<?php echo $orc_options_local_schema; ?>
			</script>
			<?php
		}
	}

	/**
	 * Add Facebook Pixel Code
	 */
	public function orc_add_facebookpixel() {
		// Add facebook ads manager verification code
		?>
			<meta name="facebook-domain-verification" content="dkhdmer276azt3wehibac33x2e8bde" />
		<?php

		// Add facebook pixel code (actually using plugin for this right now)
		$orc_options_facebookpixel = Options::getOption( 'orc_options_facebookpixel' );

		if ( strlen( $orc_options_facebookpixel ) > 0 ) {
			?>
			<!-- Facebook Pixel Code -->
			<script>
				var facebook_pixel = '<?php echo esc_attr( $orc_options_facebookpixel ); ?>';
				!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t, s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
				fbq('init', facebook_pixel);
				fbq('track', 'PageView');
			</script>
			<noscript>
				<img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?php echo esc_attr( $orc_options_facebookpixel ); ?>&amp;ev=PageView&amp;noscript=1" />
			</noscript>
			<!-- End Facebook Pixel Code -->
			<?php
		}
	}

	/**
	 * Add Bing Tracking Code
	 */
	public function orc_add_bingtracking() {
		$orc_options_bing = Options::getOption( 'orc_options_bing' );

		if ( strlen( $orc_options_bing ) > 0 ) {
			?>
			<!-- Bing Tracking Code -->
			<script>
				var bing = "<?php echo esc_attr( $orc_options_bing ); ?>";
				(function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){var o={ti:bing};o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")},n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function(){var s=this.readyState;s&&s!=="loaded"&&s!=="complete"||(f(),n.onload=n.onreadystatechange=null)},i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})(window,document,"script","//bat.bing.com/bat.js","uetq");
			</script>
			<!-- End Bing Tracking Code -->
			<?php
		}
	}

	/**
	 * Add LinkedIn Partner Code
	 */
	public function orc_add_linkedin() {
		$orc_options_linkedin = Options::getOption( 'orc_options_linkedin' );

		if ( strlen( $orc_options_linkedin ) > 0 ) {
			?>
			<!-- LinkedIn Partner Code -->
			<script>
				_linkedin_partner_id = "<?php echo esc_attr( $orc_options_linkedin ); ?>";
				window._linkedin_data_partner_ids=window._linkedin_data_partner_ids||[];window._linkedin_data_partner_ids.push(_linkedin_partner_id);
				(function(){var s=document.getElementsByTagName("script")[0];var b=document.createElement("script");b.type="text/javascript";b.async=true;b.src="https://snap.licdn.com/li.lms-analytics/insight.min.js";s.parentNode.insertBefore(b,s);})();
			</script>
			<noscript>
				<img height="1" width="1" style="display:none;" alt="" src="https://dc.ads.linkedin.com/collect/?pid=<?php echo esc_attr( $orc_options_linkedin ); ?>&fmt=gif" />
			</noscript>
			<!-- End LinkedIn Partner Code -->
			<?php
		}
	}

	/**
	 * Add Twitter Universal Website Tag
	 */
	public function orc_add_twitter() {
		$orc_options_twitter = Options::getOption( 'orc_options_twitter' );

		if ( strlen( $orc_options_twitter ) > 0 ) {
			?>
			<!-- Twitter universal website tag code -->
			<script>
				var twitter = '<?php echo esc_attr( $orc_options_twitter ); ?>';
				!function(e,t,n,s,u,a){e.twq||(s=e.twq=function(){s.exe?s.exe.apply(s,arguments):s.queue.push(arguments);},s.version='1.1',s.queue=[],u=t.createElement(n),u.async=!0,u.src='//static.ads-twitter.com/uwt.js',a=t.getElementsByTagName(n)[0],a.parentNode.insertBefore(u,a))}(window,document,'script');
				// Insert Twitter Pixel ID and Standard Event data below
				twq('init',twitter);
				twq('track','PageView');
			</script>
			<!-- End Twitter universal website tag code -->
			<?php
		}
	}

	/**
	 * Reoccurring events create a post for each of the events.
	 * This will replace the individual links with one link for the event
	 *
	 * @param string $content - Original page content.
	 * @return string $content - Modified page content.
	 */
	public function my_em_custom_content( $content ) {

		// Modify the specific dates to point to just one event.
		$content = preg_replace( '/(events\/foo-west-vancouver-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'alumni/foo-meetings/#westvan', $content, -1, $count );
		$content = preg_replace( '/(events\/foo-calgary-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'alumni/foo-meetings/#calgary', $content, -1, $count );
		$content = preg_replace( '/(events\/foo-fraser-valley-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'alumni/foo-meetings/#fraservalley', $content, -1, $count );
		$content = preg_replace( '/(events\/foo-victoria-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'alumni/foo-meetings/#vancouverisland', $content, -1, $count );

		$content = preg_replace( '/(events\/alumni-meeting-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'alumni/#alumnimeeting', $content, -1, $count );

		$content = preg_replace( '/(events\/shame-drama-triangle-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'programs/family-programs/family-program-schedule/', $content, -1, $count );
		$content = preg_replace( '/(events\/underlying-issues-addiction-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'programs/family-programs/family-program-schedule/', $content, -1, $count );
		$content = preg_replace( '/(events\/family-long-weekend-rebuilding-trust-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'programs/family-programs/family-program-schedule/', $content, -1, $count );
		$content = preg_replace( '/(events\/family-long-weekend-addiction-family-disease-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'programs/family-programs/family-program-schedule/', $content, -1, $count );
		$content = preg_replace( '/(events\/codependency-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'programs/family-programs/family-program-schedule/', $content, -1, $count );
		$content = preg_replace( '/(events\/addiction-101-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'programs/family-programs/family-program-schedule/', $content, -1, $count );
		$content = preg_replace( '/(events\/recovery-relapse-prevention-[0-9]{4}-[0-9]{2}-[0-9]{2}\/)/', 'programs/family-programs/family-program-schedule/', $content, -1, $count );

		// Whatever happens, you must return the $content variable, altered or not.
		return $content;
	}
}
new ORCOptions();

// Include all the custom post types.
require_once plugin_dir_path( __FILE__ ) . 'includes/cpt/staff_member_cpt.php';
