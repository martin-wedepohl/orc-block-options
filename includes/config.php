<?php

namespace ORCOptions\Includes;

defined( 'ABSPATH' ) or die;

class Config {
	const PLUGIN_VERSION       = '3.0.0';
	const TEXT_DOMAIN          = 'orc-options';
	const CAPABILITY           = 'edit_posts';
	const MENU_SLUG            = 'orc-options';

	const OPTION_GROUP         = 'orc_options_option-group';

	const PHONE_SECTION        = 'orc-phone-section';
	const EMAIL_SECTION        = 'orc-email-section';
	const EMAIL_DELETE_SECTION = 'orc-email-delete-section';
	const VIDEO_SECTION        = 'orc-video-section';
	const ANALYTICS_SECTION    = 'orc-analytics-section';
	const SCHEMA_SECTION       = 'orc-schema-section';
	const EXCERPTS_SECTION     = 'orc-excerpts-section';
	const DEBUG_SECTION        = 'orc-debug-section';

	// Phone numbers.
	const PHONE                = 'orc_options_phone';
	const TOLL_FREE            = 'orc_options_tollfree';
	const TEXT                 = 'orc_options_text';
	const FAX                  = 'orc_options_fax';

	// Contact Form 7 ID's.
	const INTAKE_ID            = 'orc_options_wpcf7id';
	const COMMUNICATIONS_ID    = 'orc_options_wpcf7id_comm';
	const HR_ID                = 'orc_options_wpcf7id_hr';
	const ALUMNI_ID            = 'orc_options_wpcf7id_alumni';
	const WEBSITE_ID           = 'orc_options_wpcf7id_website';
	const PRIVACY_ID           = 'orc_options_wpcf7id_privacy';

	// Videos.
	const MAIN                 = 'orc_options_mainvideo';
	const XMAS                 = 'orc_options_xmasvideo';

	// Analytics.
	const GOOGLE               = 'orc_options_google_analytics';
	const GOOGLE_TAG           = 'orc_options_google_tag';
	const FACEBOOK             = 'orc_options_facebookappid';
	const PIXEL                = 'orc_options_facebookpixel';
	const BING                 = 'orc_options_bing';
	const LINKEDIN             = 'orc_options_linkedin';
	const TWITTER              = 'orc_options_twitter';

	// Schema's.
	const ORG                  = 'orc_options_org_schema';
	const LOCAL                = 'orc_options_local_schema';

	// Excerpts.
	const ADMINISTRATIVE       = 'orc_options_staff_administrative_excerpt';
	const CLINICAL             = 'orc_options_staff_clinical_excerpt';
	const MEDICAL              = 'orc_options_staff_medical_excerpt';
	const RECOVERY             = 'orc_options_staff_recovery_excerpt';
	const SUPPORT              = 'orc_options_staff_support_excerpt';
	const WELLNESS             = 'orc_options_staff_wellness_excerpt';

	public static function getVersion() {
		return self::PLUGIN_VERSION;
	}

}
