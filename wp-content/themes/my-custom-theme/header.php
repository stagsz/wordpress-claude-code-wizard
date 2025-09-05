<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo is_home() ? get_bloginfo('description') : wp_trim_words(get_the_excerpt() ?: get_the_content(), 25); ?>">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph Tags -->
    <meta property="og:title" content="<?php wp_title('|', true, 'right'); ?>">
    <meta property="og:description" content="<?php echo is_home() ? get_bloginfo('description') : wp_trim_words(get_the_excerpt() ?: get_the_content(), 25); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo get_permalink(); ?>">
    <meta property="og:site_name" content="<?php bloginfo('name'); ?>">
    
    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php wp_title('|', true, 'right'); ?>">
    <meta name="twitter:description" content="<?php echo is_home() ? get_bloginfo('description') : wp_trim_words(get_the_excerpt() ?: get_the_content(), 25); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" role="banner">
    <div class="container">
        <div class="header-container">
            
            <!-- Site Logo/Branding -->
            <div class="site-branding">
                <?php if (has_custom_logo()) : ?>
                    <div class="site-logo">
                        <?php the_custom_logo(); ?>
                    </div>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" rel="home">
                        <h1 class="site-title"><?php bloginfo('name'); ?></h1>
                        <?php if (get_bloginfo('description')) : ?>
                            <p class="site-description"><?php bloginfo('description'); ?></p>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Primary Navigation -->
            <nav class="primary-nav" role="navigation" aria-label="<?php _e('Primary Navigation', 'my-custom-theme'); ?>">
                <?php if (has_nav_menu('primary')) : ?>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_class' => 'primary-navigation',
                        'container' => false,
                    ));
                    ?>
                <?php else : ?>
                    <ul class="primary-navigation">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'my-custom-theme'); ?></a></li>
                        <?php if (post_type_exists('secondhand_store')) : ?>
                            <li><a href="<?php echo get_post_type_archive_link('secondhand_store'); ?>"><?php _e('Store Directory', 'my-custom-theme'); ?></a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo esc_url(home_url('/about')); ?>"><?php _e('About', 'my-custom-theme'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/contact')); ?>"><?php _e('Contact', 'my-custom-theme'); ?></a></li>
                    </ul>
                <?php endif; ?>
            </nav>
            
            <!-- Header Actions -->
            <div class="header-actions">
                
                <!-- Search Form -->
                <div class="header-search">
                    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                        <label for="search-field" class="sr-only"><?php _e('Search stores...', 'my-custom-theme'); ?></label>
                        <input type="search" 
                               id="search-field" 
                               class="search-input" 
                               placeholder="<?php _e('Search stores...', 'my-custom-theme'); ?>" 
                               value="<?php echo get_search_query(); ?>" 
                               name="s">
                        <button type="submit" class="search-submit" aria-label="<?php _e('Search', 'my-custom-theme'); ?>">
                            <i class="fas fa-search" aria-hidden="true"></i>
                        </button>
                    </form>
                </div>
                
                <!-- Directory Quick Link -->
                <?php if (post_type_exists('secondhand_store')) : ?>
                    <a href="<?php echo get_post_type_archive_link('secondhand_store'); ?>" 
                       class="btn-header-action">
                        <i class="fas fa-store"></i>
                        <?php _e('Browse Stores', 'my-custom-theme'); ?>
                    </a>
                <?php endif; ?>
                
            </div>
            
            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" 
                    aria-label="<?php _e('Toggle Mobile Menu', 'my-custom-theme'); ?>"
                    aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
        </div>
    </div>
    
    <!-- Mobile Navigation -->
    <nav class="mobile-navigation" role="navigation" aria-label="<?php _e('Mobile Navigation', 'my-custom-theme'); ?>">
        <?php if (has_nav_menu('primary')) : ?>
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class' => 'mobile-nav-menu',
                'container' => false,
            ));
            ?>
        <?php else : ?>
            <ul class="mobile-nav-menu">
                <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'my-custom-theme'); ?></a></li>
                <?php if (post_type_exists('secondhand_store')) : ?>
                    <li><a href="<?php echo get_post_type_archive_link('secondhand_store'); ?>"><?php _e('Store Directory', 'my-custom-theme'); ?></a></li>
                <?php endif; ?>
                <li><a href="<?php echo esc_url(home_url('/about')); ?>"><?php _e('About', 'my-custom-theme'); ?></a></li>
                <li><a href="<?php echo esc_url(home_url('/contact')); ?>"><?php _e('Contact', 'my-custom-theme'); ?></a></li>
            </ul>
        <?php endif; ?>
        
        <!-- Mobile Search -->
        <div class="mobile-search">
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <input type="search" 
                       placeholder="<?php _e('Search stores...', 'my-custom-theme'); ?>" 
                       value="<?php echo get_search_query(); ?>" 
                       name="s">
                <button type="submit"><?php _e('Search', 'my-custom-theme'); ?></button>
            </form>
        </div>
    </nav>
</header>