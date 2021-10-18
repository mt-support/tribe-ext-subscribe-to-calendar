<?php
namespace Tribe\Extensions\Subscribe_To_Calendar\Test;

use Tribe\Events\Views\V2 as Views;

class URITest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @test
	 * iCal should have a webcal:// scheme attached to the canonical iCal feed URL
	 */
	public function ical_should_have_a_webcal_scheme() {
		tribe_register_provider( Views\Service_Provider::class );

		$view = Views\View::make( Views\View::class, tribe_context() );

		$subscribe_to_calendar = tribe( 'extension.subscribe_to_calendar' );

		$expected = preg_replace( '#https?://#', 'webcal://', $subscribe_to_calendar->get_canonical_ics_feed_url( $view ) );

		$this->assertEquals( $expected, $subscribe_to_calendar->get_ical_uri( $view ) );
	}

	/**
	 * @test
	 * gCal should prefix our canonical URL with https://www.google.com/calendar/render?cid=
	 */
	public function gcal_should_have_a_google_calendar_prefix() {
		tribe_register_provider( Views\Service_Provider::class );

		$view = Views\View::make( Views\View::class, tribe_context() );

		$subscribe_to_calendar = tribe( 'extension.subscribe_to_calendar' );

		$expected = add_query_arg(
			[ 'cid' => urlencode( $subscribe_to_calendar->get_canonical_ics_feed_url( $view ) ) ],
			'https://www.google.com/calendar/render'
		);

		$this->assertEquals( $expected, $subscribe_to_calendar->get_gcal_uri( $view ) );
	}

	/**
	 * @test
	 * both URIs should be overridable by single_ical_link View context
	 */
	public function single_ical_link_should_passthru_via_context() {
		tribe_register_provider( Views\Service_Provider::class );

		$view = Views\View::make( Views\View::class, tribe_context()->alter( [
			'single_ical_link' => $link = 'http://random.org'
		] ) );

		$subscribe_to_calendar = tribe( 'extension.subscribe_to_calendar' );

		$this->assertEquals( 'webcal://random.org', $subscribe_to_calendar->get_ical_uri( $view ) );
		$this->assertEquals( 'https://www.google.com/calendar/render?cid=http%3A%2F%2Frandom.org', $subscribe_to_calendar->get_gcal_uri( $view ) );
	}
}
