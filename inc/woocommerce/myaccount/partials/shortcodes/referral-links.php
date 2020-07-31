<?php
/**
 * Shortcode for display referral links
 */
?>

<div class="ignico-socials ignico-mb-3" data-ignico-share>
	<button class="ignico-btn ignico-btn-gray ignico-socials-btn ignico-mr-2" data-ignico-socials-email>
		<svg class="ignico-btn-icon ignico-mr-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 12.713l-11.985-9.713h23.97l-11.985 9.713zm0 2.574l-12-9.725v15.438h24v-15.438l-12 9.725z"/></svg>
		<span><?php echo __( 'Email', 'ignico' ); ?></span>
	</button><!--
 --><button class="ignico-btn ignico-btn-gray ignico-socials-btn" aria-label="<?php echo __( 'Copied', 'ignico' ); ?>" data-ignico-socials-link>
		<svg class="ignico-btn-icon ignico-mr-1" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g fill-rule="nonzero"><path d="M14.618 9.338a5.59 5.59 0 0 0-7.905 0L1.635 14.42a5.59 5.59 0 0 0 3.952 9.537 5.553 5.553 0 0 0 3.949-1.628l4.191-4.192a.4.4 0 0 0-.283-.683h-.16a6.72 6.72 0 0 1-2.555-.495.4.4 0 0 0-.435.088L7.28 20.065a2.397 2.397 0 1 1-3.39-3.39l5.099-5.093a2.395 2.395 0 0 1 3.385 0c.63.593 1.614.593 2.244 0 .27-.272.435-.632.463-1.014.029-.458-.14-.906-.463-1.23z"/><path d="M22.319 1.637a5.59 5.59 0 0 0-7.905 0l-4.188 4.184a.4.4 0 0 0 .292.683h.147a6.707 6.707 0 0 1 2.551.499.4.4 0 0 0 .436-.088l3.006-3.002a2.397 2.397 0 1 1 3.389 3.389l-3.745 3.74-.032.037-1.31 1.301a2.395 2.395 0 0 1-3.384 0 1.637 1.637 0 0 0-2.244 0 1.597 1.597 0 0 0-.463 1.022c-.03.457.14.905.463 1.23a5.541 5.541 0 0 0 1.597 1.118c.084.04.167.071.251.107.084.036.172.064.256.096.084.032.171.06.255.084l.236.064c.16.04.32.072.483.1.197.029.396.048.595.056H13.308l.24-.028c.087-.004.18-.024.283-.024h.136l.275-.04.128-.024.232-.048h.044a5.589 5.589 0 0 0 2.59-1.47l5.083-5.081a5.59 5.59 0 0 0 0-7.905z"/></g></svg>
		<span><?php echo __( 'Copy link', 'ignico' ); ?></span>
	</button>
</div>

<script>var referrer_code = '<?php echo esc_attr( get_user_meta( get_current_user_id(), '_ignico_referral_code', true ) ); ?>' ;</script>
