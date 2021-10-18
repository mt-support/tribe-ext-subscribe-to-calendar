<?php
/**
 * Handles hooking all the actions and filters used by the module.
 *
 * To remove a filter:
 * ```php
 *  remove_filter( 'some_filter', [ tribe( Tribe\Extensions\Subscribe_To_Calendar\Hooks::class ), 'some_filtering_method' ] );
 *  remove_filter( 'some_filter', [ tribe( 'extension.subscribe_to_calendar.hooks' ), 'some_filtering_method' ] );
 * ```
 *
 * To remove an action:
 * ```php
 *  remove_action( 'some_action', [ tribe( Tribe\Extensions\Subscribe_To_Calendar\Hooks::class ), 'some_method' ] );
 *  remove_action( 'some_action', [ tribe( 'extension.subscribe_to_calendar.hooks' ), 'some_method' ] );
 * ```
 *
 * @since 1.0.0
 *
 * @package Tribe\Extensions\Subscribe_To_Calendar;
 */

namespace Tribe\Extensions\Subscribe_To_Calendar;

use Tribe__Main as Common;

/**
 * Class Hooks.
 *
 * @since 1.0.0
 *
 * @package Tribe\Extensions\Subscribe_To_Calendar;
 */
class Hooks extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'extension.subscribe_to_calendar.hooks', $this );

		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds the actions required by the plugin.
	 *
	 * @since 1.0.0
	 */
	protected function add_actions() {
		add_action( 'tribe_load_text_domains', [ $this, 'load_text_domains' ] );
	}

	/**
	 * Adds the filters required by the plugin.
	 *
	 * @since 1.0.0
	 */
	protected function add_filters() {
		add_filter( 'tribe_template_path_list', [ $this, 'template_locations' ], 10, 2 );
		add_filter( 'tribe_events_views_v2_view_template_vars', [ $this, 'template_vars' ], 10, 2 );
		add_filter( 'tribe_events_ical_single_event_links', [ $this, 'single_event_links' ], 11 );
		add_filter( 'tribe_ical_properties', [ $this, 'ical_properties' ] );
	}

	/**
	 * Load text domain for localization of the plugin.
	 *
	 * @since 1.0.0
	 */
	public function load_text_domains() {
		$mopath = tribe( Plugin::class )->plugin_dir . 'lang/';
		$domain = 'tec-labs-subscribe-to-calendar';

		// This will load `wp-content/languages/plugins` files first.
		Common::instance()->load_text_domain( $domain, $mopath );
	}

	/**
	 * Add iCal feed template overrides.
	 *
	 * We're mainly interested in ical-link.php template here.
	 *
	 * @see `tribe_template_path_list` filter.
	 *
	 * @since 1.0.0
	 *
	 * @param array $folders An array of the current folders.
	 * @param \Tribe__Template $template The current template requested.
	 *
	 * @return array The filtered template locations.
	 */
	public function template_locations( $folders, \Tribe__Template $template ) {
		$path = array_merge(
			(array) dirname( Plugin::FILE ),
			$template->get_template_folder()
		);

		$folders[ Plugin::SLUG ] = [
			'id'       => Plugin::SLUG,
			'priority' => 5,
			'path'     => $path,
		];

		return $folders;
	}

	/**
	 * Add iCal feed link labels and URIs to the global template vars.
	 *
	 * Usable in ical-link.php via the $subscribe_links global.
	 *
	 * @see `tribe_events_views_v2_view_template_vars` filter.
	 *
	 * @since 1.0.0
	 *
	 * @param array $template_vars The vars.
	 * @param \Tribe\Events\Views\V2\View $view The View implementation.
	 *
	 * @return array The filtered template variables.
	 */
	public function template_vars( $template_vars, \Tribe\Events\Views\V2\View $view ) {
		$subscribe_to_calendar = tribe( 'extension.subscribe_to_calendar' );

		$template_vars['subscribe_links'] = [
			[
				'label' => __( 'Google Calendar', 'tec-labs-subscribe-to-calendar' ),
				'uri'   => $subscribe_to_calendar->get_gcal_uri( $view ),
			],
			[
				'label' => __( 'iCalendar', 'tec-labs-subscribe-to-calendar' ),
				'uri'   => $subscribe_to_calendar->get_ical_uri( $view ),
			],
		];

		/**
		 * Add the .ics legacy export link.
		 *
		 * This is controlled by the default iCal_Data trait.
		 *
		 * @see Tribe\Events\Views\V2\Views\Traits\iCal_Data
		 */
		if ( isset( $template_vars['ical'] ) && $template_vars['ical']->display_link ) {
			$template_vars['subscribe_links'][] = [
				'label' => __( 'Download as .ICS', 'tec-labs-subscribe-to-calendar' ),
				'uri'   => $template_vars['ical']->link->url,
			];
		}

		return $template_vars;
	}

	/**
	 * Replace the default single event links with subsciption links.
	 *
	 * As single calendars are not really a View\V2\View we have to emulate one.
	 * We use `tribe_get_single_ical_link` to figure out what the feed URI
	 * should be for this pseudo-View.
	 * Fun.
	 *
	 * @see `tribe_events_ical_single_event_links` filter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $calendar_links The link content.
	 *
	 * @return string The altered link content.
	 */
	public function single_event_links( $calendar_links ) {
		$single_ical_link = tribe_get_single_ical_link();
		$subscribe_to_calendar = tribe( 'extension.subscribe_to_calendar' );

		$view = new class extends \Tribe\Events\Views\V2\View {
		};
		$view->set_url( [] );
		$view->set_context( tribe_context()->alter( [
			'single_ical_link' => $single_ical_link,
		] ) );

		$labels = [
			'gcal' => __( 'Subscribe via Google Calendar', 'tec-labs-subscribe-to-calendar' ),
			'ical' => __( 'Subscribe via iCalendar', 'tec-labs-subscribe-to-calendar' ),
		];

		$calendar_links = '<div class="tribe-events-cal-links">';
		$calendar_links .= '<a class="tribe-events-gcal tribe-events-button" href="' . esc_url( $subscribe_to_calendar->get_gcal_uri( $view ) ) . '" title="' . esc_attr( $labels['gcal'] ) . '">+ ' . esc_html( $labels['gcal'] ) . '</a>';
		$calendar_links .= '<a class="tribe-events-ical tribe-events-button" href="' . esc_url( $subscribe_to_calendar->get_ical_uri( $view ) ) . '" title="' . esc_attr( $labels['ical'] ) . '" >+ ' . esc_html( $labels['ical'] ) . '</a>';
		$calendar_links .= '</div><!-- .tribe-events-cal-links -->';

		return $calendar_links;
	}

	/**
	 * Add iCal REFRESH and TTL headers.
	 *
	 * Some clients may ignore these refresh headers.
	 * https://support.google.com/calendar/answer/37100?hl=en&ref_topic=1672445
	 *
	 * @see `tribe_ical_properties` filter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content The iCal content.
	 *
	 * @return string The filtered content.
	 */
	public function ical_properties( $content ) {
		$content .= "REFRESH-INTERVAL;VALUE=DURATION:PT1H\r\n";
		return $content . "X-PUBLISHED-TTL:PT1H\r\n";
	}
}
