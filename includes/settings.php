<?php

namespace ORCOptions\Includes;

defined( 'ABSPATH' ) or die;

class Settings {
	private $OptionsPage = '';

	public function __construct( $plugin_name ) {
		add_action( 'admin_menu', array( $this, 'addMenu' ) );
		add_action( 'admin_init', array( $this, 'registerSettingsPage' ) );
		add_filter( 'plugin_action_links_' . $plugin_name, array( $this, 'addSettingsLink' ) );
		add_filter( 'custom_menu_order', array( $this, 'reorder_admin_menu' ) );
	}
	/**
	 * Add the menu to the WordPress menu.
	 *
	 * Can be displayed under a sub menu or as a primary menu.
	 */
	public function addMenu() {

		$this->OptionsPage = add_menu_page(
			esc_html__( 'Orchard Recovery Center Options Settings', 'orc-options' ),
			esc_html__( 'ORC Options', 'orc-options' ),
			Config::CAPABILITY,
			Config::MENU_SLUG,
			array( $this, 'createMenuPage' ),
			'dashicons-lightbulb',
			25
		);

		add_submenu_page(
			Config::MENU_SLUG,
			esc_html__( 'Orchard Recovery Center Options', 'orc-options' ),
			esc_html__( 'Options', 'orc-options' ),
			Config::CAPABILITY,
			Config::MENU_SLUG,
			array( $this, 'createMenuPage' )
		);

		add_submenu_page(
			Config::MENU_SLUG,
			esc_html__( 'Orchard Recovery Center Custom Post Types Information', 'orc-options' ),
			esc_html__( 'Information', 'orc-options' ),
			Config::CAPABILITY,
			Config::MENU_SLUG . '-info',
			array( $this, 'displayInfo' )
		);

	}

	/**
	 * Create the menu page that will show all the options associated with the plugin.
	 */
	public function createMenuPage() {
		if ( ! current_user_can( Config::CAPABILITY ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page', 'orc-options' ) );
		}
		printf( '<div class="wrap"><h2>%s</h2><form action="options.php" method="post">', esc_html__( 'ORC Plugin Options', 'orc-options' ) );

		settings_fields( Config::OPTION_GROUP );
		do_settings_sections( Config::MENU_SLUG );
		submit_button();
		settings_errors();

		printf( '</form></div> <!-- /.wrap -->' );
		printf( '<div class="wrap"><p>%s %s</p></div> <!-- /.wrap -->', esc_html__( 'Plugin Version:', 'orc-options' ), Config::getVersion() );
	}

	/**
	 * Reorder the submenu
	 *
	 * Options and Information will be the last menu items, but we want them first
	 *
	 * @global array $submenu
	 * @param type $menu_ord
	 * @return type
	 */
	function reorder_admin_menu( $menu_ord ) {
		global $submenu;

		$arr        = array();
		$totalitems = count( $submenu[ Config::MENU_SLUG ] );
		$arr[]      = $submenu[ Config::MENU_SLUG ][ $totalitems - 2 ];  // Options
		$arr[]      = $submenu[ Config::MENU_SLUG ][ $totalitems - 1 ];  // Information

		$totalitems = $totalitems - 2;
		for ( $item = 0; $item < $totalitems; $item++ ) {
			$arr[] = $submenu[ Config::MENU_SLUG ][ $item ];
		}
		$submenu[ Config::MENU_SLUG ] = $arr;

		return $menu_ord;
	}

	/**
	 * Display the Orchard Recovery Center Information Page
	 */
	public function displayInfo() {
		?>

<div class="wrap">
	<h1>Orchard Recovery Options</h1>
	<h2>Contains the following Options</h2>
	<ul class="orc_Options_list">
	   <li>
			<h3>Phone Numbers:</h3><br/>
			<ul>
				<li>Local Phone Number - Greater Vancouver Area Phone Number</li>
				<li>Toll Free Phone Number - Toll Free North American Phone Number</li>
				<li>Text/Mobile Number - Number to receive text messages from the website on</li>
				<li>Fax Number - Greater Vancouver Area Fax Number</li>
			</ul>
		</li>
	   <li>
			<h3>Email Addresses (actually the Contact Form 7 ID\'s for each email address):</h3><br/>
			<ul>
				<li>Contact Form 7 ID (Intake) - Admissions Contact Form</li>
				<li>Contact Form 7 ID (Communications) - Communications/Social Media Contact Form</li>
				<li>Contact Form 7 ID (Human Resources) - Human Resources Contact Form</li>
				<li>Contact Form 7 ID (Alumni) - Alumni Contact Form</li>
				<li>Contact Form 7 ID (Website) - Website Administrator Contact Form</li>
				<li>Contact Form 7 ID (Privacy) - Privacy Contact Form</li>
			</ul>
		</li>
	   <li>
			<h3>Number of days to keep Contact Form 7 Emails</h3><br/>
			<ul>
				<li>Number of days before the emails are deleted</li>
			</ul>
		</li>
	   <li>
			<h3>YouTube URL\'s for Website embedded Video\'s:</h3><br/>
			<ul>
				<li>Main Video ID (shown on Home and Media Pages)</li>
				<li>Xmas Video ID (shown on the Home Page at Christmas)</li>
			</ul>
		</li>
	   <li>
			<h3>Google:</h3><br/>
			<ul>
				<li>Google Analytics Tracking Code</li>
			</ul>
		</li>
	   <li>
			<h3>Facebook:</h3><br/>
			<ul>
				<li>Facebook App ID (for displaying Orchard Recovery Center Facebook Feed on the Media Page)</li>
				<li>Facebook Pixel Code</li>
			</ul>
		</li>
	   <li>
			<h3>Bing:</h3><br/>
			<ul>
				<li>Bing Tracking Code</li>
			</ul>
		</li>
	   <li>
			<h3>LinkedIn:</h3><br/>
			<ul>
				<li>LinkedIn Partner Code</li>
			</ul>
		</li>
	   <li>
			<h3>Twitter:</h3><br/>
			<ul>
				<li>Twitter Universal Website Tag</li>
			</ul>
		</li>
	   <li>
			<h3>Schema\':</h3><br/>
			<ul>
				<li>Organization Schema (Displayed on Home Page ONLY)</li>
				<li>Local Business Schema (Displayed on ALL Pages)</li>
			</ul>
		</li>
	   <li>
			<h3>Staff Excerpts:</h3><br/>
			<ul>
				<li>Administrative</li>
				<li>Clinical</li>
				<li>Medical</li>
				<li>Recovery Coach</li>
				<li>Support</li>
				<li>Wellness</li>
			</ul>
		</li>
	</ul>
	<h2>Shortcodes</h2>
	<ul class="orc_Options_list">
		<li>
			<h3>Create an image link with srcset information for the Post/Page Featured Image - needed mainly for svg files<br/><br/>[orc_image postid=P rounding="O" width="W" height="H"]</h3>
			<ul class="orc_Options_list">
				<li>P = Post/Page ID (REQUIRED)</li>
				<li>O = true/false for rounded image - Default = false</li>
				<li>W,H = Width/Hight of the image in pixels - Default = thumbnail width/hight</li>
			</ul>
		</li>
		<li>
			<h3>Display a Facebook feed (requires Facebook App ID be set)<br/><br/>[orc_facebook height="H" width=W tabs="T" small_header="S" hide_cover="D" show_facepile="F"]</h3>
			<ul class="orc_Options_list"><br>
				<li>H,W = Height/Width of feed - Default = 500px high, 340 px wide</li>
				<li>T = Facebook Tabs to show - Default = timeline,events</li>
				<li>S = Small feed header - Default = false</li>
				<li>D = Hide feed cover - Default = false</li>
				<li>F = Show faces of people who like page - Default = true</li>
			</ul>
		</li>
		<li>
			<h3>Display the family programming from the events<br/><br/>[orc_family_programs title="T" monthsbefore="B" monthsafter="A"]</h3>
			<ul class="orc_Options_list"><br>
				<li>T = Title for the display - Default = ""</li>
				<li>B = Number of months to diplay before current date - Default = 0</li>
				<li>A = Number of months to display after current date - Default = 2</li>
			</ul>
		</li>
		<li>
			<h3>Display the department that is being emailed<br/><br/>[orc_department emailto="D"]</h3>
			<ul class="orc_Options_list"><br>
				<li>D = Department (intakedepartment, communicationsdepartment, hrdepartment) - Default = ""</li>
			</ul>
		</li>
		<li>
			<h3>Get the contact form ID that is being emailed<br/><br/>[orc_email_id emailto="D"]</h3>
			<ul class="orc_Options_list"><br>
				<li>D = Department (intakedepartment, communicationsdepartment, hrdepartment) - Default = "intakedepartment"</li>
			</ul>
		</li>
		<li>
			<h3>Convert a date to the number of years from today<br/><br/>[orc_date_to_years year="Y" month="M" day="D"]</h3>
			<ul class="orc_Options_list"><br>
				<li>Y = Year - Default = Current Year</li>
				<li>M = Month - Default = Current Month Number</li>
				<li>D = Day - Default = Current Day of Month</li>
			</ul>
		</li>
	</ul>
	<h2>Visual Composer Elements</h2>
	<p>These components are available through the Visual Composer GUI to build website pages (RECOMMENDED) or via the shortcode shown below</p>
	<ul class="orc_Options_list">
		<li>
			<h3>Create a Carousel of different pages (customized for each element)<br/><br/>[orc_carousel posttype="P" homepage="H" linkimage="L" prevnext="N" dots="N" autoplay="A" speed=S pauseonhover="V" loopcarousel="O" christmas="C" margin="M" width="W" height="H" margintop="T" marginbottom="B" marginleft="L" marginright="R" roundimg="I" colsdesktop="D" colstablet="T" colsmobile="M" css="C"]</h3>
			<ul class="orc_Options_list">
				<li>
				   P - Post/Page Type - From the Category of the page (Required)<br/>
					&nbsp;&nbsp;- tours (ORC Tour Types)<br/>
					&nbsp;&nbsp;- program-types (Programs)<br/>
					&nbsp;&nbsp;- staff (Staff Members)<br/>
					&nbsp;&nbsp;- testimonials (Testimonials)<br/>
					&nbsp;&nbsp;- video-posts (Video\'s)<br/>
					&nbsp;&nbsp;- make-history (Vision Make History)<br/>
					&nbsp;&nbsp;- community (Vision Comunity)<br/>
				</li>
				<li>H - On Home Page - Default = false</li>
				<li>L - Link image to permalink - Default = false</li>
				<li>N - Show prev/next arrows - Default = false</li>
				<li>D - Show dots - Default = false</li>
				<li>A - Autoplay carousel - Default = false</li>
				<li>S - Autoplay speed - Default = 7000 (7 seconds)</li>
				<li>V - Pause carousel on hover - Default = false</li>
				<li>O - Loop carousel - Default = false</li>
				<li>C - Is Christmas testimonial - Default = false (only valid for posttype=testimonial and homepage="true")</li>
				<li>M - Margin between elements - Default 30px (Carousel base uses right margin)</li>
				<li>W,H - Width/Hight of image - Default 200px</li>
				<li>I - Make image round - Default = false</li>
				<li>D,T,M - Number of colums/items on Desktop/Tablet/Mobile - Default 5 (Desktop), 3 (Tablet), 1 (Mobile)</li>
				<li>C - Visual composer Design Options CSS</li>
			</ul>
		</li>
		<li>
			<h3>Display ORC contact information<br/><br/>[orc_contact contacts="C" makelink="M" fonticon="F" prefix="P" suffix="S" alignment="A" hardbreak="B" colorpicker="H" linkclass="C"]</h3>
			<ul class="orc_Options_list">
				<li>C - Contact type (phone, tollfree, mobile, fax, intake, communications, hr)</li>
				<li>M - Link text to phone, sms or email program - Default = false</li>
				<li>F - Display fontawesome icon - Default = false</li>
				<li>P,S - Prefix/Suffix text - Default = ""</li>
				<li>A - Alignment (alignleft, alignright, aligncenter) - Default = ""</li>
				<li>B - Append HTML break after text - Default = false</li>
				<li>H - HTML Hex color including # - Default = "#000000"</li>
				<li>L - Class for the normal/hover for the link - Default = ""</li>
			</ul>
		</li>
		<li>
			<h3>Display ORC post information - Careers, Press Media and Testimonials<br/><br/>[orc_post post="P" alignment_left="AL" alignment_right="AR" width_left="WL" width_right="WR" offset_left="OL" offset_right="OR" css="C"</h3>
			<ul class="orc_Options_list">
				<li>P - Post type (careers, press-media, testimonials)</li>
				<li>AL,AR - Alignment (alignmentleft, alignmentcenter, alignmentright)</li>
				<li>WL,WR - Number of columns left/right (1-12) - Press Media and Testimonials only</li>
				<li>OL,OR - Visual Composer offset/width/hide - Press Media and Testimonials only</li>
				<li>C - Visual Composer Design Options CSS - Press Media and Testimonials only</li>
			</ul>
		</li>
	</ul>
</div>

		<?php
	} // displayInfo


	/**
	 * Register the settings page with settings sections and fields.
	 */
	public function registerSettingsPage() {
		register_setting( Config::OPTION_GROUP, Config::PHONE, 'sanitize_text_field' );
		register_setting( Config::OPTION_GROUP, Config::TOLL_FREE, 'sanitize_text_field' );
		register_setting( Config::OPTION_GROUP, Config::TEXT, 'sanitize_text_field' );
		register_setting( Config::OPTION_GROUP, Config::FAX, 'sanitize_text_field' );
		register_setting( Config::OPTION_GROUP, Config::INTAKE_ID, 'sanitize_text_field' );
		register_setting( Config::OPTION_GROUP, Config::COMMUNICATIONS_ID, 'sanitize_text_field' );
		register_setting( Config::OPTION_GROUP, Config::HR_ID, 'sanitize_text_field' );
		register_setting( Config::OPTION_GROUP, Config::ALUMNI_ID, 'sanitize_text_field' );
		register_setting( Config::OPTION_GROUP, Config::WEBSITE_ID, 'sanitize_text_field' );
		register_setting( Config::OPTION_GROUP, Config::PRIVACY_ID, 'sanitize_text_field' );
		register_setting( Config::OPTION_GROUP, Config::MAIN, 'sanitize_text_field' );
		register_setting( Config::OPTION_GROUP, Config::XMAS, 'sanitize_text_field' );
		register_setting( Config::OPTION_GROUP, Config::GOOGLE, 'sanitize_text_field' );
		register_setting( Config::OPTION_GROUP, Config::GOOGLE_TAG, 'sanitize_text_field' );
		register_setting( Config::OPTION_GROUP, Config::FACEBOOK, 'sanitize_text_field' );
		register_setting( Config::OPTION_GROUP, Config::PIXEL, 'sanitize_text_field' );
		register_setting( Config::OPTION_GROUP, Config::BING, 'sanitize_text_field' );
		register_setting( Config::OPTION_GROUP, Config::LINKEDIN, 'sanitize_text_field' );
		register_setting( Config::OPTION_GROUP, Config::TWITTER, 'sanitize_text_field' );
		register_setting( Config::OPTION_GROUP, Config::ORG, array( $this, 'validateData' ) );
		register_setting( Config::OPTION_GROUP, Config::LOCAL, array( $this, 'validateData' ) );
		register_setting( Config::OPTION_GROUP, Config::ADMINISTRATIVE, array( $this, 'validateData' ) );
		register_setting( Config::OPTION_GROUP, Config::CLINICAL, array( $this, 'validateData' ) );
		register_setting( Config::OPTION_GROUP, Config::MEDICAL, array( $this, 'validateData' ) );
		register_setting( Config::OPTION_GROUP, Config::RECOVERY, array( $this, 'validateData' ) );
		register_setting( Config::OPTION_GROUP, Config::SUPPORT, array( $this, 'validateData' ) );
		register_setting( Config::OPTION_GROUP, Config::WELLNESS, array( $this, 'validateData' ) );

		add_settings_section( Config::PHONE_SECTION, null, array( $this, 'sectionPhoneTitle' ), Config::MENU_SLUG );

		add_settings_field(
			Config::PHONE,
			esc_html__( 'Local Phone Number:', 'orc-options' ),
			array( $this, 'textField' ),
			Config::MENU_SLUG,
			Config::PHONE_SECTION,
			array(
				'classes' => 'widefat',
				'value'   => Options::getOption( Config::PHONE ),
				'name'    => Config::PHONE,
				'id'      => Config::PHONE,
			)
		);

		add_settings_field(
			Config::TOLL_FREE,
			esc_html__( 'Toll Free Phone Number:', 'orc-options' ),
			array( $this, 'textField' ),
			Config::MENU_SLUG,
			Config::PHONE_SECTION,
			array(
				'classes' => 'widefat',
				'value'   => Options::getOption( Config::TOLL_FREE ),
				'name'    => Config::TOLL_FREE,
				'id'      => Config::TOLL_FREE,
			)
		);

		add_settings_field(
			Config::TEXT,
			esc_html__( 'Text Number:', 'orc-options' ),
			array( $this, 'textField' ),
			Config::MENU_SLUG,
			Config::PHONE_SECTION,
			array(
				'classes' => 'widefat',
				'value'   => Options::getOption( Config::TEXT ),
				'name'    => Config::TEXT,
				'id'      => Config::TEXT,
			)
		);

		add_settings_field(
			Config::FAX,
			esc_html__( 'Fax Number:', 'orc-options' ),
			array( $this, 'textField' ),
			Config::MENU_SLUG,
			Config::PHONE_SECTION,
			array(
				'classes' => 'widefat',
				'value'   => Options::getOption( Config::FAX ),
				'name'    => Config::FAX,
				'id'      => Config::FAX,
			)
		);

		add_settings_section( Config::EMAIL_SECTION, null, array( $this, 'sectionEmailTitle' ), Config::MENU_SLUG );

		add_settings_field(
			Config::INTAKE_ID,
			esc_html__( 'Contact Form 7 ID (Intake):', 'orc-options' ),
			array( $this, 'textField' ),
			Config::MENU_SLUG,
			Config::EMAIL_SECTION,
			array(
				'classes' => 'widefat',
				'value'   => Options::getOption( Config::INTAKE_ID ),
				'name'    => Config::INTAKE_ID,
				'id'      => Config::INTAKE_ID,
			)
		);

		add_settings_field(
			Config::COMMUNICATIONS_ID,
			esc_html__( 'Contact Form 7 ID (Communications):', 'orc-options' ),
			array( $this, 'textField' ),
			Config::MENU_SLUG,
			Config::EMAIL_SECTION,
			array(
				'classes' => 'widefat',
				'value'   => Options::getOption( Config::COMMUNICATIONS_ID ),
				'name'    => Config::COMMUNICATIONS_ID,
				'id'      => Config::COMMUNICATIONS_ID,
			)
		);

		add_settings_field(
			Config::HR_ID,
			esc_html__( 'Contact Form 7 ID (Human Resources):', 'orc-options' ),
			array( $this, 'textField' ),
			Config::MENU_SLUG,
			Config::EMAIL_SECTION,
			array(
				'classes' => 'widefat',
				'value'   => Options::getOption( Config::HR_ID ),
				'name'    => Config::HR_ID,
				'id'      => Config::HR_ID,
			)
		);

		add_settings_field(
			Config::ALUMNI_ID,
			esc_html__( 'Contact Form 7 ID (Alumni):', 'orc-options' ),
			array( $this, 'textField' ),
			Config::MENU_SLUG,
			Config::EMAIL_SECTION,
			array(
				'classes' => 'widefat',
				'value'   => Options::getOption( Config::ALUMNI_ID ),
				'name'    => Config::ALUMNI_ID,
				'id'      => Config::ALUMNI_ID,
			)
		);

		add_settings_field(
			Config::WEBSITE_ID,
			esc_html__( 'Contact Form 7 ID (Website):', 'orc-options' ),
			array( $this, 'textField' ),
			Config::MENU_SLUG,
			Config::EMAIL_SECTION,
			array(
				'classes' => 'widefat',
				'value'   => Options::getOption( Config::WEBSITE_ID ),
				'name'    => Config::WEBSITE_ID,
				'id'      => Config::WEBSITE_ID,
			)
		);

		add_settings_field(
			Config::PRIVACY_ID,
			esc_html__( 'Contact Form 7 ID (Privacy):', 'orc-options' ),
			array( $this, 'textField' ),
			Config::MENU_SLUG,
			Config::EMAIL_SECTION,
			array(
				'classes' => 'widefat',
				'value'   => Options::getOption( Config::PRIVACY_ID ),
				'name'    => Config::PRIVACY_ID,
				'id'      => Config::PRIVACY_ID,
			)
		);

		add_settings_section( Config::VIDEO_SECTION, null, array( $this, 'sectionVideoTitle' ), Config::MENU_SLUG );

		add_settings_field(
			Config::MAIN,
			esc_html__( 'Main Video ID:', 'orc-options' ),
			array( $this, 'textField' ),
			Config::MENU_SLUG,
			Config::VIDEO_SECTION,
			array(
				'classes' => 'widefat',
				'value'   => Options::getOption( Config::MAIN ),
				'name'    => Config::MAIN,
				'id'      => Config::MAIN,
			)
		);

		add_settings_field(
			Config::XMAS,
			esc_html__( 'Xmas Video ID:', 'orc-options' ),
			array( $this, 'textField' ),
			Config::MENU_SLUG,
			Config::VIDEO_SECTION,
			array(
				'classes' => 'widefat',
				'value'   => Options::getOption( Config::XMAS ),
				'name'    => Config::XMAS,
				'id'      => Config::XMAS,
			)
		);

		add_settings_section( Config::ANALYTICS_SECTION, null, array( $this, 'sectionAnalyticsTitle' ), Config::MENU_SLUG );

		add_settings_field(
			Config::GOOGLE,
			esc_html__( 'Google Analytics Code:', 'orc-options' ),
			array( $this, 'textField' ),
			Config::MENU_SLUG,
			Config::ANALYTICS_SECTION,
			array(
				'classes' => 'widefat',
				'value'   => Options::getOption( Config::GOOGLE ),
				'name'    => Config::GOOGLE,
				'id'      => Config::GOOGLE,
			)
		);

		add_settings_field(
			Config::GOOGLE_TAG,
			esc_html__( 'Google Tag Manager Code:', 'orc-options' ),
			array( $this, 'textField' ),
			Config::MENU_SLUG,
			Config::ANALYTICS_SECTION,
			array(
				'classes' => 'widefat',
				'value'   => Options::getOption( Config::GOOGLE_TAG ),
				'name'    => Config::GOOGLE_TAG,
				'id'      => Config::GOOGLE_TAG,
			)
		);

		add_settings_field(
			Config::FACEBOOK,
			esc_html__( 'Facebook App ID:', 'orc-options' ),
			array( $this, 'textField' ),
			Config::MENU_SLUG,
			Config::ANALYTICS_SECTION,
			array(
				'classes' => 'widefat',
				'value'   => Options::getOption( Config::FACEBOOK ),
				'name'    => Config::FACEBOOK,
				'id'      => Config::FACEBOOK,
			)
		);

		add_settings_field(
			Config::PIXEL,
			esc_html__( 'Facebook Pixel:', 'orc-options' ),
			array( $this, 'textField' ),
			Config::MENU_SLUG,
			Config::ANALYTICS_SECTION,
			array(
				'classes' => 'widefat',
				'value'   => Options::getOption( Config::PIXEL ),
				'name'    => Config::PIXEL,
				'id'      => Config::PIXEL,
			)
		);

		add_settings_field(
			Config::BING,
			esc_html__( 'Bing Tracking:', 'orc-options' ),
			array( $this, 'textField' ),
			Config::MENU_SLUG,
			Config::ANALYTICS_SECTION,
			array(
				'classes' => 'widefat',
				'value'   => Options::getOption( Config::BING ),
				'name'    => Config::BING,
				'id'      => Config::BING,
			)
		);

		add_settings_field(
			Config::LINKEDIN,
			esc_html__( 'LinkedIn Partner Code:', 'orc-options' ),
			array( $this, 'textField' ),
			Config::MENU_SLUG,
			Config::ANALYTICS_SECTION,
			array(
				'classes' => 'widefat',
				'value'   => Options::getOption( Config::LINKEDIN ),
				'name'    => Config::LINKEDIN,
				'id'      => Config::LINKEDIN,
			)
		);

		add_settings_field(
			Config::TWITTER,
			esc_html__( 'Twitter Universal Website Tag:', 'orc-options' ),
			array( $this, 'textField' ),
			Config::MENU_SLUG,
			Config::ANALYTICS_SECTION,
			array(
				'classes' => 'widefat',
				'value'   => Options::getOption( Config::TWITTER ),
				'name'    => Config::TWITTER,
				'id'      => Config::TWITTER,
			)
		);

		add_settings_section( Config::SCHEMA_SECTION, null, array( $this, 'sectionSchemaTitle' ), Config::MENU_SLUG );

		add_settings_field(
			Config::ORG,
			esc_html__( 'Organization Schema (Displayed on Home Page ONLY):', 'orc-options' ),
			array( $this, 'textAreaField' ),
			Config::MENU_SLUG,
			Config::SCHEMA_SECTION,
			array(
				'classes'     => 'widefat',
				'value'       => Options::getOption( Config::ORG ),
				'name'        => Config::ORG,
				'id'          => Config::ORG,
				'style'       => 'font-family:Courier;white-space:pre-line;',
				'rows'        => '8',
				'placeholder' => 'Enter the schema script WITHOUT the script tags',
			)
		);

		add_settings_field(
			Config::LOCAL,
			esc_html__( 'Local Business Schema (Displayed on ALL Pages):', 'orc-options' ),
			array( $this, 'textAreaField' ),
			Config::MENU_SLUG,
			Config::SCHEMA_SECTION,
			array(
				'classes'     => 'widefat',
				'value'       => Options::getOption( Config::LOCAL ),
				'name'        => Config::LOCAL,
				'id'          => Config::LOCAL,
				'style'       => 'font-family:Courier;',
				'rows'        => '8',
				'placeholder' => 'Enter the schema script WITHOUT the script tags',
			)
		);

		add_settings_section( Config::EXCERPTS_SECTION, null, array( $this, 'sectionExcerptsTitle' ), Config::MENU_SLUG );

		add_settings_field(
			Config::ADMINISTRATIVE,
			esc_html__( 'Administrative:', 'orc-options' ),
			array( $this, 'textAreaField' ),
			Config::MENU_SLUG,
			Config::EXCERPTS_SECTION,
			array(
				'classes'     => 'widefat',
				'value'       => Options::getOption( Config::ADMINISTRATIVE ),
				'name'        => Config::ADMINISTRATIVE,
				'id'          => Config::ADMINISTRATIVE,
				'style'       => 'font-family:Courier;',
				'rows'        => '8',
				'placeholder' => 'Enter the excerpt for the Administrative Staff',
			)
		);

		add_settings_field(
			Config::CLINICAL,
			esc_html__( 'Clinical:', 'orc-options' ),
			array( $this, 'textAreaField' ),
			Config::MENU_SLUG,
			Config::EXCERPTS_SECTION,
			array(
				'classes'     => 'widefat',
				'value'       => Options::getOption( Config::CLINICAL ),
				'name'        => Config::CLINICAL,
				'id'          => Config::CLINICAL,
				'style'       => 'font-family:Courier;',
				'rows'        => '8',
				'placeholder' => 'Enter the excerpt for the Clinical Staff',
			)
		);

		add_settings_field(
			Config::MEDICAL,
			esc_html__( 'Medical:', 'orc-options' ),
			array( $this, 'textAreaField' ),
			Config::MENU_SLUG,
			Config::EXCERPTS_SECTION,
			array(
				'classes'     => 'widefat',
				'value'       => Options::getOption( Config::MEDICAL ),
				'name'        => Config::MEDICAL,
				'id'          => Config::MEDICAL,
				'style'       => 'font-family:Courier;',
				'rows'        => '8',
				'placeholder' => 'Enter the excerpt for the Medical Staff',
			)
		);

		add_settings_field(
			Config::RECOVERY,
			esc_html__( 'Recovery Coaches:', 'orc-options' ),
			array( $this, 'textAreaField' ),
			Config::MENU_SLUG,
			Config::EXCERPTS_SECTION,
			array(
				'classes'     => 'widefat',
				'value'       => Options::getOption( Config::RECOVERY ),
				'name'        => Config::RECOVERY,
				'id'          => Config::RECOVERY,
				'style'       => 'font-family:Courier;',
				'rows'        => '8',
				'placeholder' => 'Enter the excerpt for the Recovery Coaches',
			)
		);

		add_settings_field(
			Config::SUPPORT,
			esc_html__( 'Support Staff:', 'orc-options' ),
			array( $this, 'textAreaField' ),
			Config::MENU_SLUG,
			Config::EXCERPTS_SECTION,
			array(
				'classes'     => 'widefat',
				'value'       => Options::getOption( Config::SUPPORT ),
				'name'        => Config::SUPPORT,
				'id'          => Config::SUPPORT,
				'style'       => 'font-family:Courier;',
				'rows'        => '8',
				'placeholder' => 'Enter the excerpt for the Support Staff',
			)
		);

		add_settings_field(
			Config::WELLNESS,
			esc_html__( 'Wellness Staff:', 'orc-options' ),
			array( $this, 'textAreaField' ),
			Config::MENU_SLUG,
			Config::EXCERPTS_SECTION,
			array(
				'classes'     => 'widefat',
				'value'       => Options::getOption( Config::WELLNESS ),
				'name'        => Config::WELLNESS,
				'id'          => Config::WELLNESS,
				'style'       => 'font-family:Courier;',
				'rows'        => '8',
				'placeholder' => 'Enter the excerpt for the Wellness Staff',
			)
		);
	}
	/**
	 * Called when the save changes button has been pressed to save the plugin Options and used
	 * to validate all the input fields.
	 *
	 * @param array $input
	 * @return array
	 */
	public function validateData( $input ) {
		$output = $input;

		// TODO: Add validation code for all the Options

		return apply_filters( 'validateData', $output, $input );
	}

	public function sectionPhoneTitle() {
		printf( '<h3>' . esc_html__( 'Phone Numbers', 'orc-options' ) . '</h3>' );
	}

	public function sectionEmailTitle() {
		printf( '<h3>' . esc_html__( 'Email Addresses', 'orc-options' ) . '</h3>' );
	}

	public function sectionVideoTitle() {
		printf( '<h3>' . esc_html__( 'YouTube Video URL\'s', 'orc-options' ) . '</h3>' );
	}

	public function sectionAnalyticsTitle() {
		printf( '<h3>' . esc_html__( 'Analytics/Tracking Codes', 'orc-options' ) . '</h3>' );
	}

	public function sectionSchemaTitle() {
		printf( '<h3>' . esc_html__( 'Schema\'s', 'orc-options' ) . '</h3>' );
	}

	public function sectionExcerptsTitle() {
		printf( '<h3>' . esc_html__( 'Staff Excerpts', 'orc-options' ) . '</h3>' );
	}

	/**
	 * Display a text field in the form.
	 *
	 * @param array $args The arguments passed to the function.
	 */
	public function textField( $args ) {
		$args = shortcode_atts(
			array(
				'classes'     => '',
				'name'        => '',
				'id'          => '',
				'value'       => '',
				'description' => '',
				'placeholder' => '',
			),
			$args
		);

		$args['placeholder'] = ( '' === $args['placeholder'] ) ? '' : 'placeholder="' . $args['placeholder'] . '"';

		printf(
			'<input type="text" class="%s" name="%s" id="%s" %s value="%s" /><span class="description"> %s</span>',
			$args['classes'],
			$args['name'],
			$args['id'],
			$args['placeholder'],
			$args['value'],
			$args['description']
		);
	}

	public function textAreaField( $args ) {
		$args = shortcode_atts(
			array(
				'classes'     => '',
				'name'        => '',
				'id'          => '',
				'value'       => '',
				'description' => '',
				'style'       => '',
				'rows'        => '',
				'placeholder' => '',
			),
			$args
		);

		// Replace new line character with an actual newline
		$val                 = str_replace(
			'\n',
			'
',
			esc_html( $args['value'] )
		);
		$args['style']       = ( '' === $args['style'] ) ? '' : 'style="' . $args['style'] . '"';
		$args['rows']        = ( '' === $args['rows'] ) ? '' : 'rows="' . $args['rows'] . '"';
		$args['placeholder'] = ( '' === $args['placeholder'] ) ? '' : 'placeholder="' . $args['placeholder'] . '"';
		printf(
			'<textarea class="%s" name="%s" id="%s" %s %s %s>%s</textarea><span class="description"> %s</span>',
			$args['classes'],
			$args['name'],
			$args['id'],
			$args['style'],
			$args['rows'],
			$args['placeholder'],
			$val,
			$args['description']
		);
	}

	/**
	 * Display a number field in the form.
	 *
	 * @param array $args The arguments passed to the function.
	 */
	public function numberField( $args ) {
		$args = shortcode_atts(
			array(
				'classes'     => '',
				'name'        => '',
				'id'          => '',
				'value'       => '',
				'description' => '',
				'min'         => '0',
				'max'         => '200',
			),
			$args
		);

		printf(
			'<input type="number" min="%s" max="%s" class="%s" name="%s" id="%s" value="%s" /><span class="description"> %s</span>',
			$args['min'],
			$args['max'],
			$args['classes'],
			$args['name'],
			$args['id'],
			$args['value'],
			$args['description']
		);
	}
	/**
	 * Display a checkbox field in the form.
	 *
	 * @param array $args The arguments passed to the function.
	 */
	public function checkboxField( $args ) {
		$args = shortcode_atts(
			array(
				'classes'     => '',
				'name'        => '',
				'id'          => '',
				'description' => '',
				'checked'     => '',
			),
			$args
		);

		printf(
			'<input type="checkbox" class="%s" name="%s" id="%s" value="1" %s /><span class="description"> %s</span>',
			$args['classes'],
			$args['name'],
			$args['id'],
			$args['checked'],
			$args['description']
		);
	}

	/**
	 * Display a settings link on the plugins page.
	 *
	 * @param array $links
	 * @return array
	 */
	public function addSettingsLink( $links ) {

		$settings_link = '<a href="admin.php?page=' . CONFIG::MENU_SLUG . '">' . __( 'Settings' ) . '</a>';
		$info_link     = '<a href="admin.php?page=' . CONFIG::MENU_SLUG . '-info">' . __( 'Information' ) . '</a>';
		array_push( $links, $settings_link );
		array_push( $links, $info_link );
		return $links;

		$settings = array(
			'settings' => sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=' . Config::MENU_SLUG ), __( 'Settings', 'orc-options' ) ),
			'info'     => sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=' . Config::MENU_SLUG ) . '-info', __( 'Settings', 'orc-options' ) ),
		);
		return array_merge( $settings, $links );
	}
}
