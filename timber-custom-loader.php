<?php

/*
Plugin Name: Timber Custom Loader
Description: This plugin adds a custom Twig loader to Timber
Version: 1.0.0
Author: Laurence Archer
Author URI: https://ozpital.com
*/

class TimberCustomLoader {
    public function __construct() {
        // Settings
       $this->version = '1.0.0';
       $this->name = 'Timber Custom Loader';
       $this->slug = 'timber-custom-loader';

       // Do Admin Stuff
        if (is_admin()) {
            add_action('admin_menu', [$this, 'admin_menu']);
            add_action('admin_init', [$this, 'register_admin_settings']);
        }

        // Do User Stuff
        if (!is_admin()) {
            add_action('plugins_loaded', [$this, 'plugin_checks']);
        }
    }

    public function admin_menu() {
        add_options_page(
            $this->name,
            $this->name,
            'manage_options',
            $this->slug,
            [$this, 'admin_page']
        );
    }

    public function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php echo $this->name; ?></h1>

            <form method="post" action="options.php">
                <?php settings_fields($this->slug . '-options'); ?>
                <?php do_settings_sections($this->slug . '-options'); ?>

                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Twig templates directory</th>
                        <td><input type="text" name="twig_templates_directory" value="<?php echo esc_attr(get_option('twig_templates_directory')); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Template file name and extention</th>
                        <td><input type="text" name="twig_template_filename" value="<?php echo esc_attr(get_option('twig_template_filename')); ?>" /></td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function register_admin_settings() {
        register_setting($this->slug . '-options', 'twig_templates_directory');
        register_setting($this->slug . '-options', 'twig_template_filename');
    }

    public function plugin_checks() {
        add_filter('timber/loader/loader', [$this, 'add_loader']);
    }

    public function add_loader($loader) {
        require_once('loader/loader.php');

        $paths = get_option('twig_templates_directory');
        $filename = get_option('twig_template_filename');
        $customLoader = new TwigLoader($paths, $filename);
        $chainLoader = new \Twig_Loader_Chain([$loader, $customLoader]);

        return $chainLoader;
    }
}

new TimberCustomLoader();
