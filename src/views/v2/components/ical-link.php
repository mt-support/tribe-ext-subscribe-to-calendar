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
<div>
	<?php foreach ( $subscribe_links as $link ) : ?>
		<a href="<?php echo esc_url( $link['uri'] ); ?>"><?php echo esc_html( $link['label'] ); ?></a>
	<?php endforeach; ?>
</div>
