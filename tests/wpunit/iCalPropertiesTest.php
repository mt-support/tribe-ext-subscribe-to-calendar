<?php
namespace Tribe\Extensions\Subscribe_To_Calendar\Test;

class iCalPropertiesTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @test
	 * iCal Feed should contain REFRESH properties
	 */
    public function ical_feed_should_contain_refresh_properties() {
		global $wp_query;
		$wp_query = tribe_get_events( [], true );

		$ical = tribe( 'tec.iCal' );
		$content = $ical->generate_ical_feed( null, false );

		$required_properties = [
			'REFRESH-INTERVAL;VALUE=DURATION:PT1H',
			'X-PUBLISHED-TTL:PT1H',
		];

		foreach ( $required_properties as $property ) {
			$this->assertContains( $property, $content, "iCal Feed does not contain $property" );
		}
    }
}
