<?php
namespace Tribe\Extensions\Subscribe_To_Calendar\Test;

use Tribe\Events\Views\V2 as Views;

class TemplateTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @test
	 * $subscribe_links should be globally defined
	 */
	public function template_vars_should_be_defined() {
		tribe_register_provider( Views\Service_Provider::class );

		$view = Views\View::make( Views\View::class, tribe_context() );

		$subscribe_to_calendar = tribe( 'extension.subscribe_to_calendar' );

		$this->assertEquals( [
			[
				'label' => 'Google Calendar',
				'uri' => $subscribe_to_calendar->get_gcal_uri( $view ),
			],
			[
				'label' => 'iCalendar',
				'uri' => $subscribe_to_calendar->get_ical_uri( $view ),
			],
			[
				'label' => 'Download as .ICS',
				'uri' => $view->get_template_vars()['ical']->link->url,
			],
		], $view->get_template_vars()['subscribe_links'] );
	}

	/**
	 * @test
	 * the "Download .ICS" link should respect ical.display_link configuration
	 */
	public function dot_ics_should_be_hidden_by_display_link_configuration() {
		tribe_register_provider( Views\Service_Provider::class );

		$view = Views\View::make( Views\View::class, tribe_context() );

		$subscribe_to_calendar = tribe( 'extension.subscribe_to_calendar' );

		add_filter( 'tribe_events_views_v2_view_ical_data', function( $data ) {
			$data->display_link = false;
			return $data;
		} );

		$this->assertNotContains(
			'Download as .ICS',
			wp_list_pluck( $view->get_template_vars()['subscribe_links'], 'label' ),
		);
	}

	/**
	 * @test
	 * the correct ical-link.php template should be loaded
	 */
    public function correct_template_should_be_loaded() {
		$template = new Views\Template( $this->makeEmpty( Views\View_Interface::class ) );

		$expected = implode( DIRECTORY_SEPARATOR, array_merge(
			(array) dirname( \Tribe\Extensions\Subscribe_To_Calendar\Plugin::FILE ),
			$template->get_template_folder(),
			[ 'components', 'ical-link.php' ]
		) );

		$this->assertEquals( $expected, $template->get_template_file( 'components/ical-link' ) );
    }
}
