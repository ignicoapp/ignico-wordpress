<?php
/**
 * The template for displaying full width pages.
 *
 * Template Name: Full width
 *
 * @package storefront
 */

$plugin = ignico();

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php

			while ( have_posts() ) :
				the_post();

				do_action( 'storefront_page_before' );
				?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<header class="entry-header">
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
					</header><!-- .entry-header -->

					<?php $plugin['notice']->woocommerce_notice(); ?>

					<div class="entry-content">

						<?php

						/**
						 * My Account navigation.
						 *
						 * @since 2.6.0
						 */
						do_action( 'woocommerce_account_navigation' );
						?>

						<div class="woocommerce-MyAccount-content">
							<?php the_content(); ?>
						</div><!-- .entry-content -->
					<div>
				</article><!-- #post-## -->

				<?php

				/**
				 * Functions hooked in to storefront_page_after action
				 *
				 * @hooked storefront_display_comments - 10
				 */
				do_action( 'storefront_page_after' );


			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
