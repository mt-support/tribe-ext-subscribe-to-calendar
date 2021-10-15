<?php
namespace Tribe\Extensions\Subscribe_To_Calendar\Test;

use Tribe\Events\Views\V2 as Views;

class CanonicalTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @test
	 * there should be no pretty permalinks in canonical URL
	 */
	public function url_should_not_be_pretty() {
		update_option( 'permalink_structure', '/%postname%/' );
		flush_rewrite_rules();

		tribe_register_provider( Views\Service_Provider::class );

		$view = Views\View::make( Views\View::class, tribe_context() );

		$url = new Views\Url( \Tribe__Events__Main::instance()->getLink( 'home' ) );

		$this->assertEmpty( $url->get_query_args() );

		$subscribe_to_calendar = tribe( 'extension.subscribe_to_calendar' );
		
		$url = new Views\Url( $subscribe_to_calendar->get_canonical_ics_feed_url( $view ) );

		$this->assertNotEmpty( $url->get_query_args() );
	}

	/**
	 * @test
	 * start date should be overridden to today
	 */
	public function start_date_should_be_overridden() {
		tribe_register_provider( Views\Service_Provider::class );

		$view = Views\View::make( Views\View::class, tribe_context()->alter( [
			'event_date' => '2097-03-01',
		] ) );

		$subscribe_to_calendar = tribe( 'extension.subscribe_to_calendar' );

		$url = new Views\Url( $subscribe_to_calendar->get_canonical_ics_feed_url( $view ) );

		$this->assertEquals( date( 'Y-m-d' ), $url->get_query_args()['tribe-bar-date'] );
	}

	/**
	 * @test
	 * ical=1 query parameter should be present
	 */
	public function url_should_trigger_ical_with_query_arg() {
		tribe_register_provider( Views\Service_Provider::class );

		$view = Views\View::make( Views\View::class, tribe_context() );

		$subscribe_to_calendar = tribe( 'extension.subscribe_to_calendar' );

		$url = new Views\Url( $subscribe_to_calendar->get_canonical_ics_feed_url( $view ) );

		$this->assertNotEmpty( $args = $url->get_query_args() );
		$this->assertEquals( $args['ical'], 1 );
	}
}
