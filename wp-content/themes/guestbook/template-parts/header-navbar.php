<header id="page-header">
    <nav class="bg-primary">
        <div class="left">
          <?php printf( '<a href="%1$s" class="navbar-brand" rel="home">%2$s</a>',
              esc_url( home_url( '/' ) ),
              get_bloginfo('name')
            ); ?>
        </div>
        <div class="right">
            <div class="primary-nav has-mega-menu">
                <?php
                    do_action(
                        'display_nav_menu', [
                            'container'      => false,
                            'theme_location' => 'primary_menu',
                            'menu_class'     => 'navigation',
                            'fallback_cb'    => '__return_empty_string',
                            'walker'         => new \GB\GB_Nav_Menu()
                        ]
                    );
                ?>
            </div>
            <div class="nav-btn">
                <i></i>
                <i></i>
                <i></i>
            </div>
        </div>
    </nav>
</header>
