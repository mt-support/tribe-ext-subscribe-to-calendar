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
	}

	/**
	 * Load text domain for localization of the plugin.
	 *
	 * @since 1.0.0
	 */ public function load_text_domains() {
		$mopath = tribe( Plugin::class )->plugin_dir . 'lang/';
		$domain = 'tec-labs-subscribe-to-calendar';

		// This will load `wp-content/languages/plugins` files first.
		Common::instance()->load_text_domain( $domain, $mopath );
	}

	/**
	 * Add iCal feed template overrides.
	 *
	 * @since 1.0.0
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
	 * Add iCal feed link labels and URIs.
	 *
	 * @since 1.0.0
	 */
	public function template_vars( $template_vars, \Tribe\Events\Views\V2\View $view ) {
		$template_vars['subscribe_links'] = [
			[
				'label' => __( 'Google Calendar', 'tec-labs-subscribe-to-calendar' ),
				'uri'   => 'https://link',
			],
			[
				'label' => __( 'iCalendar', 'tec-labs-subscribe-to-calendar' ),
				'uri'   => 'https://link',
			],
			[
				'label' => __( 'Download as .ICS', 'tec-labs-subscribe-to-calendar' ),
				'uri'   => 'https://link',
			],
		];
		return $template_vars;
	}
}
