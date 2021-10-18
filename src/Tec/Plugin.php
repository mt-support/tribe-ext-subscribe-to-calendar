<?php
/**
 * Plugin Class.
 *
 * @since 1.0.0
 *
 * @package Tribe\Extensions\Subscribe_To_Calendar
 */

namespace Tribe\Extensions\Subscribe_To_Calendar;

/**
 * Class Plugin
 *
 * @since 1.0.0
 *
 * @package Tribe\Extensions\Subscribe_To_Calendar
 */
class Plugin extends \tad_DI52_ServiceProvider {
	/**
	 * Stores the version for the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const VERSION = '1.0.0';

	/**
	 * Stores the base slug for the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const SLUG = 'subscribe-to-calendar';

	/**
	 * Stores the base slug for the extension.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const FILE = TRIBE_EXTENSION_SUBSCRIBE_TO_CALENDAR_FILE;

	/**
	 * @since 1.0.0
	 *
	 * @var string Plugin Directory.
	 */
	public $plugin_dir;

	/**
	 * @since 1.0.0
	 *
	 * @var string Plugin path.
	 */
	public $plugin_path;

	/**
	 * @since 1.0.0
	 *
	 * @var string Plugin URL.
	 */
	public $plugin_url;

	/**
	 * Setup the Extension's properties.
	 *
	 * This always executes even if the required plugins are not present.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		// Set up the plugin provider properties.
		$this->plugin_path = trailingslashit( dirname( static::FILE ) );
		$this->plugin_dir  = trailingslashit( basename( $this->plugin_path ) );
		$this->plugin_url  = plugins_url( $this->plugin_dir, $this->plugin_path );

		// Register this provider as the main one and use a bunch of aliases.
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'extension.subscribe_to_calendar', $this );
		$this->container->singleton( 'extension.subscribe_to_calendar.plugin', $this );
		$this->container->register( PUE::class );

		if ( ! $this->check_plugin_dependencies() ) {
			// If the plugin dependency manifest is not met, then bail and stop here.
			return;
		}

		$this->container->register( Hooks::class );
		$this->container->register( Assets::class );
	}

	/**
	 * Checks whether the plugin dependency manifest is satisfied or not.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether the plugin dependency manifest is satisfied or not.
	 */
	protected function check_plugin_dependencies() {
		$this->register_plugin_dependencies();

		return tribe_check_plugin( static::class );
	}

	/**
	 * Registers the plugin and dependency manifest among those managed by Tribe Common.
	 *
	 * @since 1.0.0
	 */
	protected function register_plugin_dependencies() {
		$plugin_register = new Plugin_Register();
		$plugin_register->register_plugin();

		$this->container->singleton( Plugin_Register::class, $plugin_register );
		$this->container->singleton( 'extension.subscribe_to_calendar.register', $plugin_register );
	}

	/**
	 * Retrieve the iCal Feed URL with current context parameters.
	 *
	 * Both iCal and gCal URIs can be built from the Feed URL which simply
	 * points to a canonical URL that the generator can parse
	 * via `tribe_get_global_query_object` and spew out results in the
	 * ICS format.
	 *
	 * This is exactly what \Tribe__Events__iCal::do_ical_template does
	 * and lets it generate from a less vague and a more context-bound URL
	 * for more granular functionality. This lets us have shortcode support
	 * among other things.
	 *
	 * We strip some of the things that we don't need for subscriptions
	 * like end dates, view types, etc., ignores pagination and always returns
	 * fresh future events. Subsciptions to past events is pointless.
	 *
	 * The URL generated is also inert to the Permalink and Rewrite Rule settings
	 * in WordPress, so will work out of the box on any website, even if
	 * the settings are changes or break.
	 *
	 * @param \Tribe\Events\Views\V2\View $view The View we're being called from.
	 *
	 * @return string The iCal Feed URI.
	 */
	public function get_canonical_ics_feed_url( \Tribe\Events\Views\V2\View $view ) {
		if ( $single_ical_link = $view->get_context()->get( 'single_ical_link' ) ) {
			/**
			 * This is not really canonical. As single event views are not actually
			 * Views\V2\View instances we pass them out as is. A lot of extra fundamental
			 * things need to happen before we can actually canonicalize single iCals links.
			 */
			return $single_ical_link;
		}

		$view_url_args = $view->get_url_args();

		// Clean query params to only contain canonical arguments.
		$canonical_args = [ 'post_type', 'tribe_events_cat' ];

		foreach ( $view_url_args as $arg => $value ) {
			if ( ! in_array( $arg, $canonical_args, true ) ) {
				unset( $view_url_args[ $arg ] );
			}
		}

		$view_url_args['tribe-bar-date'] = date( 'Y-m-d' ); // Subscribe from today.
		$view_url_args['ical'] = 1; // iCalendarize.

		return add_query_arg( urlencode_deep( $view_url_args ), home_url( '/' ) );
	}

	/**
	 * Retrieve the Google Calendar URI.
	 *
	 * Clicking this link will open up Google Calendar.
	 *
	 * @since 1.0.0
	 *
	 * @param \Tribe\Events\Views\V2\View $view The View we're being called from.
	 *
	 * @return string The Google Calendar URI.
	 */
	public function get_gcal_uri( \Tribe\Events\Views\V2\View $view ) {
		$canonical_ics_feed_url = $this->get_canonical_ics_feed_url( $view );

		return add_query_arg(
			[ 'cid' => urlencode( $canonical_ics_feed_url ) ],
			'https://www.google.com/calendar/render?cid='
		);
	}

	/**
	 * Retrieve the iCalendar URI.
	 *
	 * Clicking this link will open up the default iCalendar
	 * handler. Might open Google Calendar in some cases.
	 *
	 * The initial request will go out over HTTP, then switched to HTTPs by the
	 * server. There's no webcals://-based scheme that's officially supported.
	 *
	 * @since 1.0.0
	 *
	 * @param \Tribe\Events\Views\V2\View $view The View we're being called from.
	 *
	 * @return string The iCalendar URI.
	 */
	public function get_ical_uri( \Tribe\Events\Views\V2\View $view ) {
		$canonical_ics_feed_url = $this->get_canonical_ics_feed_url( $view );

		return str_replace( [ 'http://', 'https://' ], 'webcal://', $canonical_ics_feed_url );
	}
}
