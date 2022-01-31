<?php
 
function azure_function_settings_init() {
    // Register a new setting for "azure_function" page.
    register_setting( 'azure_function', 'azure_function_options' );
 
    // Register a new section in the "azure_function" page.
    add_settings_section(
        'azure_function_section_developers',
        __( 'Azure Function Options', 'azure_function' ), 
        'azure_function_section_developers_callback',
        'azure_function'
    );
 
    // Register a new field in the "azure_function_section_developers" section, inside the "azure_function" page.
    add_settings_field(
        'azure_function_field_url', // As of WP 4.6 this value is used only internally.
                                // Use $args' label_for to populate the id inside the callback.
            __( 'URL', 'azure_function' ),
        'azure_function_field_key_cb',
        'azure_function',
        'azure_function_section_developers',
        array(
            'label_for'         => 'azure_function_field_url',
            'class'             => 'azure_function_row',
            'azure_function_custom_data' => 'custom',
        )
    );
    // Register a new field in the "azure_function_section_developers" section, inside the "azure_function" page.
    add_settings_field(
        'azure_function_field_key',
        __( 'Key', 'azure_function' ),
        'azure_function_field_key_cb',
        'azure_function',
        'azure_function_section_developers',
        array(
            'label_for'         => 'azure_function_field_key',
            'class'             => 'azure_function_row',
            'azure_function_custom_data' => 'custom',
        )
    );
    add_settings_field(
        'azure_function_field_trigger',
        __( 'Trigger', 'azure_function' ),
        'azure_function_field_trigger_cb',
        'azure_function',
        'azure_function_section_developers',
        array(
            'label_for'         => 'azure_function_field_trigger',
            'class'             => 'azure_function_row',
            'azure_function_custom_data' => 'custom',
        )
    );
    
}
 
/**
 * Register our azure_function_settings_init to the admin_init action hook.
 */
add_action( 'admin_init', 'azure_function_settings_init' );
 
 
/**
 * Custom option and settings:
 *  - callback functions
 */
 
 
/**
 * Developers section callback function.
 *
 * @param array $args  The settings array, defining title, id, callback.
 */
function azure_function_section_developers_callback( $args ) {
    ?>
    <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Options for Function App', 'azure_function' ); ?></p>
    <p>This plugin allows for a Wordpress hook which will call an HTTP Azure Function when a post is saved.</p>
    <?php
}
 
function azure_function_field_key_cb( $args ) {
    // Get the value of the setting we've registered with register_setting()
    $options = get_option('azure_function_options');
    ?>
    <input type="text" 
        id="<?php echo esc_attr( $args['label_for'] ); ?>"
        data-custom="<?php echo esc_attr( $args['azure_function_custom_data'] ); ?>"
        name="azure_function_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
        class="regular-text"
        value=<?php echo $options[$args['label_for']]?>>
    <?php
}

function azure_function_field_trigger_cb( $args ) {
    $options = get_option( 'azure_function_options' );
    ?>

<select
id="<?php echo esc_attr( $args['label_for'] ); ?>"
data-custom="<?php echo esc_attr( $args['azure_function_custom_data'] ); ?>"
name="azure_function_options[<?php echo esc_attr( $args['label_for'] ); ?>]">
            <?php 	
                $post_statuses = get_post_statuses();
                foreach ($post_statuses as &$vals1) {
                    echo"<option value= \"".htmlentities($vals1)."\"";
                    echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], $vals1, false ) ) : ( '' );
                    echo">$vals1</option>";
                }

            ?>
    </select>
    <?php
}
 
/**
 * Add the top level menu page.
 */
function azure_function_options_page() {
    add_menu_page(
        'Azure Function',
        'Azure Function Options',
        'manage_options',
        'azure_function',
        'azure_function_options_page_html'
    );
}
 
 
/**
 * Register our azure_function_options_page to the admin_menu action hook.
 */
add_action( 'admin_menu', 'azure_function_options_page' );
 
 
/**
 * Top level menu callback function
 */
function azure_function_options_page_html() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
 
    // add error/update messages
 
    // check if the user have submitted the settings
    // WordPress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
        // add settings saved message with the class of "updated"
        add_settings_error( 'azure_function_messages', 'azure_function_message', __( 'Settings Saved', 'azure_function' ), 'updated' );
    }
 
    // show error/update messages
    settings_errors( 'azure_function_messages' );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "azure_function"
            settings_fields( 'azure_function' );
            // output setting sections and their fields
            // (sections are registered for "azure_function", each field is registered to a specific section)
            do_settings_sections( 'azure_function' );
            // output save settings button
            submit_button( 'Save Settings' );
            ?>
        </form>
    </div>
    <?php
}