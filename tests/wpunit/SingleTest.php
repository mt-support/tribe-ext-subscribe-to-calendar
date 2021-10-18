<?php
namespace Tribe\Extensions\Subscribe_To_Calendar\Test;

use Tribe\Events\Views\V2 as Views;
use Tribe\Extensions\Subscribe_To_Calendar\Test\Factories;

class SingleTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @test
	 * single event subscription links should be output
	 */
	public function should_output_single_event_subscription_links() {
		$event = ( new Factories\Event() )
			->starting_on( '2099-01-10 09:00:00' )
			->create();

		global $post;

		$post = get_post( $event );

		$ical = tribe( 'tec.iCal' );
		$subscribe_to_calendar = tribe( 'extension.subscribe_to_calendar' );

		ob_start();
		$ical->single_event_links();
		$links = ob_get_clean();

		tribe_register_provider( Views\Service_Provider::class );

		$view = Views\View::make( Views\View::class, tribe_context()->alter( [
			'single_ical_link' => tribe_get_single_ical_link()
		] ) );

		$this->assertContains( esc_url( $subscribe_to_calendar->get_ical_uri( $view ) ), $links );
		$this->assertContains( esc_url( $subscribe_to_calendar->get_gcal_uri( $view ) ), $links );
	}
}
