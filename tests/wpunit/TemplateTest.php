<?php
namespace Tribe\Extensions\Subscribe_To_Calendar\Test;

class TemplateTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @test
	 * $subscribe_links are globally defined
	 */
	public function template_vars_are_defined() {
	}

	/**
	 * @test
	 * the "Download .ICS" link respects ical.display_link configuration
	 */
	public function dot_ics_hidden_by_display_link_configuration() {
	}

	/**
	 * @test
	 * the correct ical-link.php template is loaded
	 */
    public function correct_template_is_loaded() {
    }
}
