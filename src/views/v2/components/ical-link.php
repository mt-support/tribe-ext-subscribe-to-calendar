<?php
/**
 * Component: Subscribe To Calendar
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/components/ical-link.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 1.0.0
 *
 * @var array $subscribe_links Array containing subscribe/export labels and links
 *
 * @package Tribe\Extensions\Subscribe_To_Calendar
 */
?>
<div class="tribe-events-c-events-bar__dropdown">
    <div class="tribe-events-c-dropdown-selector" data-js="tribe-events-dropdown-selector">
	<button
	    class="tribe-common-c-btn tribe-events-c-dropdown-selector__button"
	    data-js="tribe-events-dropdown-selector-button"
	>
	    <span class="tribe-events-c-dropdown-selector__button-text">
		<?php echo esc_html__( 'Subscribe to calendar', 'the-events-calendar' ); ?>
	    </span>
	    <svg class="tribe-common-c-svgicon tribe-common-c-svgicon--caret-down" viewBox="0 0 10 7" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.008.609L5 4.6 8.992.61l.958.958L5 6.517.05 1.566l.958-.958z" class="tribe-common-c-svgicon__svg-fill"/></svg>
	</button>
	<div
	    class="tribe-events-c-dropdown-selector__content"
	    id="tribe-events-dropdown-selector-content"
	    data-js="tribe-events-dropdown-selector-list-container"
	>
	    <ul class="tribe-events-c-dropdown-selector__list">
		<?php foreach ( $subscribe_links as $link ) : ?>
		    <li class="tribe-events-c-dropdown-selector__list-item">
			<a
			    href="<?php echo esc_url( $link['uri'] ); ?>"
			    class="tribe-events-c-dropdown-selector__list-item-link"
			    data-js="tribe-events-dropdown-link"
			>
			    <span class="tribe-events-c-dropdown-selector__list-item-text">
				<?php echo esc_html( $link['label'] ); ?>
			    </span>
			</a>
		    </li>
		<?php endforeach; ?>
	    </ul>
	</div>
    </div>
</div>
