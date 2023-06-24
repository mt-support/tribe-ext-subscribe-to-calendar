<?php
/**
 * Handles registering all Assets for the Plugin.
 *
 * To remove a Asset you can use the global assets handler:
 *
 * ```php
 *  tribe( 'assets' )->remove( 'asset-name' );
 * ```
 *
 * @since 1.0.0
 *
 * @package Tribe\Extensions\Subscribe_To_Calendar
 */

namespace Tribe\Extensions\Subscribe_To_Calendar;

use TEC\Common\Contracts\Service_Provider;

/**
 * Register Assets.
 *
 * @since 1.0.0
 *
 * @package Tribe\Extensions\Subscribe_To_Calendar
 */
class Assets extends Service_Provider {
	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'extension.subscribe_to_calendar.assets', $this );

		$plugin = tribe( Plugin::class );

		tribe_asset(
			$plugin,
			'tribe-ext-subscribe-to-calendar-css',
			'tribe-ext-subscribe-to-calendar.css',
			[],
			'wp_enqueue_scripts',
			[]
		);

		tribe_asset(
			$plugin,
			'tribe-ext-subscribe-to-calendar',
			'tribe-ext-subscribe-to-calendar.js',
			[],
			'wp_enqueue_scripts',
			[
				'in_footer' => true,
			]
		);
	}
}
