<?php
/**
 * BadgeOS Importer Tool
 *
 * @package     GamiPress\Admin\Tools\BadgeOS\Importer
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register BadgeOS Importer Tool meta boxes
 *
 * @since  1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_badgeos_importer_tool_meta_boxes( $meta_boxes ) {

    $prefix = 'gamipress_badgeos_importer_';

    $requirement_types = gamipress_get_requirement_types();

    $achievements_types = gamipress_get_achievement_types();
    $achievements_types_options = array();

    foreach( $achievements_types as $achievements_type_slug => $achievements_type ) {

        if( in_array( $achievements_type_slug, $requirement_types ) ) {
            continue;
        }

        $achievements_types_options[$achievements_type_slug] = $achievements_type['plural_name'];
    }

    $points_types = gamipress_get_points_types();

    $points_types_options = array();

    foreach( $points_types as $points_type_slug => $points_type ) {
        $points_types_options[$points_type_slug] = $points_type['plural_name'];
    }

    $meta_boxes['badgeos-importer'] = array(
        'title' => __( 'BadgeOS Importer', 'gamipress-badgeos-importer' ),
        'fields' => apply_filters( 'gamipress_badgeos_importer_tool_fields', array(
            $prefix . 'desc' => array(
                'content' => __( 'This tool will migrate all BadgeOS stored data to GamiPress. All the new content will be <strong>appended</strong> to prevent override anything.', 'gamipress-badgeos-importer' )
                    . '<br>' . __( '<strong>Important!</strong> Please backup your database before starting this process.', 'gamipress-badgeos-importer' )
                    . ' <a  href="javascript:void(0);" onClick="jQuery(this).next(\'p\').slideToggle();">' . __( 'Read some important notes', 'gamipress-badgeos-importer' ) . '</a>'
                    . '<p style="display: none;">'
                        . sprintf( __( '<strong>About BadgeOS Leaderboards:</strong> To supply BadgeOS leaderboards, you can check the <a href="%s" target="_blank">GamiPress - Leaderboards</a> add-on.', 'gamipress-badgeos-importer' ), 'https://gamipress.com/add-ons/gamipress-leaderboards' )
                        . '<br>' . sprintf( __( '<strong>About BadgeOS Popups:</strong> To supply BadgeOS popups, you can check the <a href="%s" target="_blank">GamiPress - Notifications</a> add-on.', 'gamipress-badgeos-importer' ), 'https://gamipress.com/add-ons/gamipress-notifications' )
                        . '<br>' . sprintf( __( '<strong>About BadgeOS Reports:</strong> To supply BadgeOS reports, you can check the <a href="%s" target="_blank">GamiPress - Reports</a> add-on.', 'gamipress-badgeos-importer' ), 'https://gamipress.com/add-ons/gamipress-reports' )
                        . '<br>' . __( '<strong>About Submissions and Nominations:</strong> Submissions and Nominations are not supported on GamiPress yet. We are working hard on get this feature ready as soon as possible. Achievement that are earned by this type, will be turned into admin-awarded.', 'gamipress-badgeos-importer' )
                        . '<br>' . sprintf( __( '<strong>About specific trigger events:</strong> For specific trigger events (bbPress, BuddyPress, LearnDash, etc ) you need to install each specific <a href="%s" target="_blank">GamiPress integration</a>. <strong>Note:</strong> All GamiPress integrations are completely free!', 'gamipress-badgeos-importer' ), admin_url( 'admin.php?page=gamipress_add_ons' ) )
                    . '</p>',
                'type' => 'html',
            ),
            $prefix . 'achievements_achievement_type' => array(
                'name' => __( 'BadgeOS Achievement Types', 'gamipress-badgeos-importer' ),
                'content' => '<p class="cmb2-metabox-description">' . __( 'BadgeOS achievement types will be imported as GamiPress achievement types with the same configuration.', 'gamipress-badgeos-importer' ) . '</p>',
                'type' => 'html',
            ),
            $prefix . 'points_points_type' => array(
                'name' => __( 'BadgeOS Points to', 'gamipress-badgeos-importer' ),
                'desc' => __( 'Choose the points type you want to import the BadgeOS points.', 'gamipress-badgeos-importer' ),
                'type' => 'select',
                'options' => $points_types_options,
            ),
            $prefix . 'override_points' => array(
                'name' => __( 'Override User Points Balance', 'gamipress-badgeos-importer' ),
                'desc' => __( 'Check this option to keep BadgeOS points balance instead of sum them to the current points balance.', 'gamipress-badgeos-importer' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'import_earnings' => array(
                'name' => __( 'Import User Earnings', 'gamipress-badgeos-importer' ),
                'desc' => __( 'Check this option to import user earned achievements and points.', 'gamipress-badgeos-importer' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'import_logs' => array(
                'name' => __( 'Import Logs', 'gamipress-badgeos-importer' ),
                'desc' => __( 'Check this option to import logs.', 'gamipress-badgeos-importer' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'run' => array(
                'label' => __( 'Start Importing Data', 'gamipress-badgeos-importer' ),
                'desc' => __( '<strong>Note:</strong> Don\'t worry about running this process several times, imported data won\'t be duplicated.', 'gamipress-badgeos-importer' ),
                'type' => 'button',
                'button' => 'primary'
            ),
        ) )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_tools_import_export_meta_boxes', 'gamipress_badgeos_importer_tool_meta_boxes' );

/**
 * First importer run (to clear marks)
 *
 * @since 1.0.8
 */
function gamipress_badgeos_importer_ajax_first_run() {

    global $wpdb;

    // Delete users meta to allow re-import user earnings again
    $wpdb->delete(
        $wpdb->usermeta,
        array( 'meta_key' => '_gamipress_imported_badgeos_achievements' )
    );

}

/**
 * AJAX handler for the BadgeOS Importer achievements import action
 *
 * @since 1.0.0
 */
function gamipress_badgeos_importer_ajax_import_achievements() {

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    global $wpdb;

    ignore_user_abort( true );
    set_time_limit( 0 );

    if( isset( $_REQUEST['first_run'] ) && (bool) absint( $_REQUEST['first_run'] ) ) {
        gamipress_badgeos_importer_ajax_first_run();
    }

    $points_types = gamipress_get_points_types();

    // First of all, check the points type
    $desired_points_type = $_REQUEST['points_points_type'];

    if( ! isset( $points_types[$desired_points_type] ) ) {
        wp_send_json_error( __( 'You need to choose a valid points type.', 'gamipress' ) );
    }

    // Our prefix
    $prefix = '_gamipress_';
    $badgeos_prefix = '_badgeos_';

    // We got our globally points type
    $points_type = $desired_points_type;

    // First update BadgeOS achievement types meta data
    $badgeos_achievement_types = $wpdb->get_results( $wpdb->prepare(
        "SELECT *
             FROM {$wpdb->posts}
             WHERE post_type = %s
             ORDER BY ID ASC",
        'achievement-type'
    ) );

    foreach( $badgeos_achievement_types as $badgeos_achievement_type ) {

        $singular_name = get_post_meta( $badgeos_achievement_type->ID, '_badgeos_singular_name', true );
        $plural_name = get_post_meta( $badgeos_achievement_type->ID, '_badgeos_plural_name', true );

        $post_data = (array) $badgeos_achievement_type;

        $post_data['post_title'] = $singular_name;

        wp_update_post( $post_data );

        // Update meta data
        update_post_meta( $badgeos_achievement_type->ID, '_gamipress_plural_name', $plural_name );

        $badgeos_achievement_type_slug = sanitize_title( substr( strtolower( $singular_name ), 0, 20 ) );

        // Next, update BadgeOS achievements meta data
        $badgeos_achievements = $wpdb->get_results( $wpdb->prepare(
            "SELECT *
             FROM {$wpdb->posts}
             WHERE post_type = %s
             ORDER BY ID ASC",
            $badgeos_achievement_type_slug
        ) );

        foreach( $badgeos_achievements as $badgeos_achievement ) {

            // if has a GamiPress meta, it means that has been updated trought GamiPress, so skip it
            $exists = get_post_meta( $badgeos_achievement->ID, $prefix . 'earned_by', true );
            if( $exists && ! empty( $exists ) ) {
                continue;
            }

            $points = absint( get_post_meta( $badgeos_achievement->ID, $badgeos_prefix . 'points', true ) );                    // Achievement points
            $earned_by = get_post_meta( $badgeos_achievement->ID, $badgeos_prefix . 'earned_by', true );                        // Achievement earned by
            $points_required = absint( get_post_meta( $badgeos_achievement->ID, $badgeos_prefix . 'points_required', true ) );  // Achievement points required
            $sequential = get_post_meta( $badgeos_achievement->ID, $badgeos_prefix . 'sequential', true );                 // Achievement sequential
            $show_earners = get_post_meta( $badgeos_achievement->ID, $badgeos_prefix . 'show_earners', true );               // Achievement show earners
            $congratulations_text = get_post_meta( $badgeos_achievement->ID, $badgeos_prefix . 'congratulations_text', true );       // Achievement congratulations text
            $maximum_earnings = get_post_meta( $badgeos_achievement->ID, $badgeos_prefix . 'maximum_earnings', true );       // Achievement max earnings
            $hidden = get_post_meta( $badgeos_achievement->ID, $badgeos_prefix . 'hidden', true );       // Achievement hidden

            if( $earned_by === 'submission' || $earned_by === 'submission_auto' || $earned_by === 'nomination' ) {
                $earned_by = 'admin';
            }

            // Update achievement meta data
            update_post_meta( $badgeos_achievement->ID, $prefix . 'points', $points );
            update_post_meta( $badgeos_achievement->ID, $prefix . 'earned_by', $earned_by );
            update_post_meta( $badgeos_achievement->ID, $prefix . 'points_type', $points_type );
            update_post_meta( $badgeos_achievement->ID, $prefix . 'points_required', $points_required );
            update_post_meta( $badgeos_achievement->ID, $prefix . 'points_type_required', $points_type );
            update_post_meta( $badgeos_achievement->ID, $prefix . 'sequential', $sequential );
            update_post_meta( $badgeos_achievement->ID, $prefix . 'show_earners', $show_earners );
            update_post_meta( $badgeos_achievement->ID, $prefix . 'congratulations_text', $congratulations_text );
            update_post_meta( $badgeos_achievement->ID, $prefix . 'maximum_earnings', $maximum_earnings );
            update_post_meta( $badgeos_achievement->ID, $prefix . 'hidden', $hidden );

        }
    }

    // Return a success message
    wp_send_json_success( __( 'BadgeOS achievements has been migrated successfully.', 'gamipress' ) );
}
add_action( 'wp_ajax_gamipress_badgeos_importer_import_achievements', 'gamipress_badgeos_importer_ajax_import_achievements' );

/**
 * AJAX handler for the BadgeOS Importer steps import action
 *
 * @since 1.0.0
 */
function gamipress_badgeos_importer_ajax_import_steps() {

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    global $wpdb;

    ignore_user_abort( true );
    set_time_limit( 0 );

    $points_types = gamipress_get_points_types();

    // First of all, check the points type
    $desired_points_type = $_REQUEST['points_points_type'];

    if( ! isset( $points_types[$desired_points_type] ) ) {
        wp_send_json_error( __( 'You need to choose a valid points type.', 'gamipress' ) );
    }

    // Our prefix
    $prefix = '_gamipress_';
    $badgeos_prefix = '_badgeos_';

    // We got our globally points type
    $points_type = $desired_points_type;

    $badgeos_steps = $wpdb->get_results( $wpdb->prepare(
        "SELECT *
             FROM {$wpdb->posts}
             WHERE post_type = %s
             ORDER BY ID ASC",
        'step'
    ) );

    foreach( $badgeos_steps as $badgeos_step ) {

        // if has a GamiPress meta, it means that has been updated trought GamiPress, so skip it
        $exists = get_post_meta( $badgeos_step->ID, $prefix . 'trigger_type', true );
        if( $exists && ! empty( $exists ) ) {
            continue;
        }

        $badgeos_trigger = get_post_meta( $badgeos_step->ID, $badgeos_prefix . 'trigger_type', true );              // Step trigger
        $count = absint( get_post_meta( $badgeos_step->ID, $badgeos_prefix . 'count', true ) );                     // Step count
        $achievement_type = get_post_meta( $badgeos_step->ID, $badgeos_prefix . 'achievement_type', true );         // Step achievement type
        $achievement_post = get_post_meta( $badgeos_step->ID, $badgeos_prefix . 'achievement_post', true );         // Step achievement post

        // Integrations support
        if( $badgeos_trigger === 'learndash_trigger' ) {
            $integration_trigger = get_post_meta( $badgeos_step->ID, '_badgeos_learndash_trigger', true );

            // Unused on GamiPress
            //$arg = (int) get_post_meta( $badgeos_step->ID, '_badgeos_learndash_object_arg1', true );

            $trigger = gamipress_badgeos_importer_convert_to_gamipress_trigger( $integration_trigger );

            // Override achievement post for specific triggers
            $integration_achievement_post = (int) get_post_meta( $badgeos_step->ID, '_badgeos_learndash_object_id', true );

            if( $integration_achievement_post ) {

                $achievement_post = $integration_achievement_post;

                // Turn "Complete any" events to "Complete specific" events
                if( $trigger === 'gamipress_ld_complete_lesson' ) {

                    $trigger = 'gamipress_ld_complete_specific_lesson';

                } else if( $trigger === 'gamipress_ld_complete_course' ) {

                    $trigger = 'gamipress_ld_complete_specific_course';

                }

            }

        } else if( $badgeos_trigger === 'community_trigger' ) {
            $integration_trigger = get_post_meta( $badgeos_step->ID, '_badgeos_community_trigger', true );

            $trigger = gamipress_badgeos_importer_convert_to_gamipress_trigger( $integration_trigger );
        } else {
            $trigger = gamipress_badgeos_importer_convert_to_gamipress_trigger( $badgeos_trigger );
        }

        // Since 1.5.1 the P2P library has been removed, so we need to recover the post parent and menu order from P2P tables
        if( version_compare( GAMIPRESS_VER, '1.5.1', '>=' ) ) {

            // Setup the P2P tables
            $p2p        = ( property_exists( $wpdb, 'p2p' ) ? $wpdb->p2p : $wpdb->prefix . 'p2p' );
            $p2pmeta 	= ( property_exists( $wpdb, 'p2pmeta' ) ? $wpdb->p2pmeta : $wpdb->prefix . 'p2pmeta' );

            // Multisite support
            if( gamipress_is_network_wide_active() ) {
                $p2p        = $wpdb->base_prefix . 'p2p';
                $p2pmeta 	= $wpdb->base_prefix . 'p2pmeta';
            }

            $p2p_entry = $wpdb->get_row( "SELECT p2p.p2p_id, p2p.p2p_to FROM {$p2p} AS p2p WHERE p2p.p2p_from = {$badgeos_step->ID} AND p2p.p2p_type LIKE 'step-to-%'" );

            if( $p2p_entry ) {

                // Setup the vars to update our post
                $post_parent = $p2p_entry->p2p_to;
                $menu_order = absint( $wpdb->get_var( "SELECT p2pmeta.meta_value FROM {$p2pmeta} AS p2pmeta WHERE p2pmeta.p2p_id = {$p2p_entry->p2p_id} AND p2pmeta.meta_key = 'order'" ) );

                // Update the requirement object to meet the new relationships
                wp_update_post( array(
                    'ID' => $badgeos_step->ID,
                    'post_parent' => $post_parent,
                    'menu_order' => $menu_order,
                ) );

            }

        }

        // Update step meta data
        update_post_meta( $badgeos_step->ID, $prefix . 'trigger_type', ( $trigger ? $trigger : $badgeos_trigger ) );
        update_post_meta( $badgeos_step->ID, $prefix . 'count', $count );
        update_post_meta( $badgeos_step->ID, $prefix . 'limit', '1' );
        update_post_meta( $badgeos_step->ID, $prefix . 'limit_type', 'unlimited' );
        update_post_meta( $badgeos_step->ID, $prefix . 'achievement_type', $achievement_type );
        update_post_meta( $badgeos_step->ID, $prefix . 'achievement_post', $achievement_post );

    }

    // Return a success message
    wp_send_json_success( __( 'BadgeOS steps has been migrated successfully.', 'gamipress' ) );
}
add_action( 'wp_ajax_gamipress_badgeos_importer_import_steps', 'gamipress_badgeos_importer_ajax_import_steps' );

/**
 * AJAX handler for the BadgeOS Importer points import action
 *
 * @since 1.0.0
 */
function gamipress_badgeos_importer_ajax_import_points() {

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    global $wpdb;

    ignore_user_abort( true );
    set_time_limit( 0 );

    $points_types = gamipress_get_points_types();

    // First of all, check the points type
    $desired_points_type = $_REQUEST['points_points_type'];
    $override_points = absint( $_REQUEST['override_points'] );

    if( ! isset( $points_types[$desired_points_type] ) ) {
        wp_send_json_error( __( 'You need to choose a valid points type.', 'gamipress' ) );
    }

    // We got our globally points type
    $points_type = $desired_points_type;

    // Default points
    $user_meta = '_gamipress_points';

    if( ! empty( $points_type ) ) {
        $user_meta = "_gamipress_{$points_type}_points";
    }

    // Get all stored users
    $users = $wpdb->get_results( $wpdb->prepare(
        "SELECT ID
        FROM {$wpdb->users}
        WHERE ID NOT IN (
          SELECT um.user_id FROM {$wpdb->usermeta} AS um WHERE um.meta_key = %s
        )
        LIMIT 0, 100",
        '_gamipress_imported_badgeos_points_balance'
    ) );

    foreach( $users as $user ) {

        $badgeos_user_points = absint( get_user_meta( $user->ID, '_badgeos_points', true ) );

        if( $override_points ) {
            // Update user points balance with BadgeOS points balance
            update_user_meta( $user->ID, $user_meta, $badgeos_user_points );
        } else {
            $user_points = absint( get_user_meta( $user->ID, $user_meta, true ) );

            // Update user points balance with GamiPress and BadgeOS points balance
            update_user_meta( $user->ID, $user_meta, ( $user_points + $badgeos_user_points ) );
        }

        // Set a meta to meet already imported users
        update_user_meta( $user->ID, '_gamipress_imported_badgeos_points_balance', 1 );

    }

    // Check remaining users
    $users_to_import = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*)
        FROM {$wpdb->users}
        WHERE ID NOT IN (
          SELECT um.user_id FROM {$wpdb->usermeta} AS um WHERE um.meta_key = %s
        )",
        '_gamipress_imported_badgeos_points_balance'
    ) );

    if( absint( $users_to_import ) !== 0 ) {
        // Return a run again action
        wp_send_json_success( array(
            'run_again' => true,
            'message' => sprintf( __( 'Remaining users points balances %d', 'gamipress' ), absint( $users_to_import ) )
        ) );
    } else {
        // Return a success message
        wp_send_json_success( __( 'BadgeOS points has been migrated successfully.', 'gamipress' ) );
    }

}
add_action( 'wp_ajax_gamipress_badgeos_importer_import_points', 'gamipress_badgeos_importer_ajax_import_points' );

/**
 * AJAX handler for the BadgeOS Importer earnings import action
 *
 * @since 1.0.0
 */
function gamipress_badgeos_importer_ajax_import_earnings() {

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    global $wpdb;

    ignore_user_abort( true );
    set_time_limit( 0 );

    $points_types = gamipress_get_points_types();

    // First of all, check the points type
    $desired_points_type = $_REQUEST['points_points_type'];

    if( ! isset( $points_types[$desired_points_type] ) ) {
        wp_send_json_error( __( 'You need to choose a valid points type.', 'gamipress' ) );
    }

    // We got our globally points type
    $points_type = $desired_points_type;

    // Migrate from user meta _gamipress_achievements to gamipress_user_earnings table
    $ct_table = ct_setup_table( 'gamipress_user_earnings' );

    // Retrieve all user IDs with the meta _gamipress_achievements
    $results = $wpdb->get_results( $wpdb->prepare(
        "SELECT u.user_id, u.meta_value
         FROM   $wpdb->usermeta AS u
         WHERE  u.meta_key = %s
          AND u.user_id NOT IN (
            SELECT um.user_id FROM {$wpdb->usermeta} AS um WHERE um.meta_key = %s
          )
          LIMIT 0, 100",
        '_badgeos_achievements',
        '_gamipress_imported_badgeos_achievements'
    ) );

    foreach( $results as $result ) {

        $user_id = $result->user_id;
        $user_earnings = maybe_unserialize( $result->meta_value );

        $site_id = get_current_blog_id();

        if( is_array( $user_earnings ) && isset( $user_earnings[$site_id] ) && is_array( $user_earnings[$site_id] ) ) {

            $user_earnings = $user_earnings[$site_id];

            foreach( $user_earnings as $user_earning ) {

                // Skip if not is an user earning
                if( ! $user_earning || ! is_object( $user_earning ) ) {
                    continue;
                }

                $exists = $wpdb->get_var( $wpdb->prepare(
                    "SELECT COUNT(*)
                         FROM {$ct_table->db->table_name}
                        WHERE user_id = %d
                          AND post_id = %d
                          AND date = %s
                        LIMIT 1",
                    absint( $user_id ),
                    absint( $user_earning->ID ),
                    date( 'Y-m-d H:i:s', $user_earning->date_earned )
                ) );

                // Skip if already exists
                if( absint( $exists ) ) {
                    continue;
                }

                $user_earning_data = array(
                    'user_id' => $user_id,
                    'post_id' => $user_earning->ID,
                    'post_type' => $user_earning->post_type,
                    'points' => $user_earning->points,
                    'points_type' => $points_type,
                    'date' => date( 'Y-m-d H:i:s', $user_earning->date_earned ),
                );

                if( is_gamipress_upgraded_to( '1.4.7' ) ) {
                    $user_earning_data['title'] = get_the_title( $user_earning->ID );
                }

                // Insert from the user _badgeos_achievements meta to gamipress_user_earnings table
                $ct_table->db->insert( $user_earning_data );

            }

        }

        // Set a meta to meet already imported users
        update_user_meta( $user_id, '_gamipress_imported_badgeos_achievements', 1 );
    }

    // Check remaining users
    $users_earnings_to_import = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*)
        FROM   $wpdb->usermeta AS u
        WHERE  u.meta_key = %s
          AND u.user_id NOT IN (
            SELECT um.user_id FROM {$wpdb->usermeta} AS um WHERE um.meta_key = %s
          )",
        '_badgeos_achievements',
        '_gamipress_imported_badgeos_achievements'
    ) );

    if( absint( $users_earnings_to_import ) !== 0 ) {
        // Return a run again action
        wp_send_json_success( array(
            'run_again' => true,
            'message' => sprintf( __( 'Remaining user earnings %d', 'gamipress' ), absint( $users_earnings_to_import ) )
        ) );
    } else {
        // Return a success message
        wp_send_json_success( __( 'BadgeOS earnings has been migrated successfully.', 'gamipress' ) );
    }

}
add_action( 'wp_ajax_gamipress_badgeos_importer_import_earnings', 'gamipress_badgeos_importer_ajax_import_earnings' );

/**
 * AJAX handler for the BadgeOS Importer logs import action
 *
 * @since 1.0.0
 */
function gamipress_badgeos_importer_ajax_import_logs() {

    // Check user capabilities
    if( ! current_user_can( gamipress_get_manager_capability() ) ) {
        wp_send_json_error( __( 'You are not allowed to perform this action.', 'gamipress' ) );
    }

    global $wpdb;

    ignore_user_abort( true );
    set_time_limit( 0 );

    $points_types = gamipress_get_points_types();

    // First of all, check the points type
    $desired_points_type = $_REQUEST['points_points_type'];

    if( ! isset( $points_types[$desired_points_type] ) ) {
        wp_send_json_error( __( 'You need to choose a valid points type.', 'gamipress' ) );
    }

    // We got our globally points type
    $points_type = $desired_points_type;

    $ct_table = ct_setup_table( 'gamipress_logs' );
    $prefix = '_gamipress_';
    $badgeos_prefix = '_badgeos_';

    $logs = $wpdb->get_results( $wpdb->prepare(
        "SELECT *
             FROM $wpdb->posts
             WHERE post_type = %s
              AND ID NOT IN (
                SELECT lm.meta_value FROM {$ct_table->meta->db->table_name} AS lm WHERE lm.meta_key = %s
              )
             ORDER BY ID ASC
             LIMIT 0, 100",
        'badgeos-log-entry',
        $prefix . 'legacy_log_id'
    ) );

    foreach( $logs as $log ) {

        $exists = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*)
             FROM {$ct_table->meta->db->table_name}
             WHERE ( meta_key = %s
              AND meta_value = %s )
             LIMIT 1",
            $prefix . 'legacy_log_id',
            $log->ID
        ) );

        // Skip if already exists
        if( absint( $exists ) ) {
            continue;
        }

        $log_data = array(
            'title' => $log->post_title,
            'type' => 'event_trigger',
            'access' => 'private',
            'user_id' => $log->post_author,
            'date' => $log->post_date,
        );

        // Since 1.5.1 the field description has been removed from logs table
        if( version_compare( GAMIPRESS_VER, '1.5.1', '<' ) ) {
            $log_data['description'] = __( 'Log imported from BadgeOS', 'gamipress-badgeos-importer' );
        }

        if( is_gamipress_upgraded_to( '1.4.7' ) ) {
		// try to extract trigger type from log title
		preg_match('/.*triggered\s([a-z_]*)\s.*/', $log->post_title, $matches );
		if ( count( $matches ) > 1 ) {
			$log_data['trigger_type'] = $matches[1];
		} else {
			$log_data['trigger_type'] = __( '(no trigger)', 'gamipress' );
		}
        }

        // Insert from posts to gamipress_logs table
        $log_id = $ct_table->db->insert( $log_data );

        if( $log_id ) {

            // Since 1.5.1 the field description has been removed from logs table
            if( version_compare( GAMIPRESS_VER, '1.5.1', '>=' ) ) {
                $ct_table->meta->db->insert( array(
                    'log_id' => $log_id,
                    'meta_key' => $prefix . 'description',
                    'meta_value' => __( 'Log imported from BadgeOS', 'gamipress-badgeos-importer' ),
                ) );
            }

            // Legacy Log ID
            $ct_table->meta->db->insert( array(
                'log_id' => $log_id,
                'meta_key' => $prefix . 'legacy_log_id',
                'meta_value' => $log->ID,
            ) );

            // BadgeOS logs hasn't any meta data stored, just the achievement ID
            $ct_table->meta->db->insert( array(
                'log_id' => $log_id,
                'meta_key' => $prefix . 'achievement_id',
                'meta_value' => get_post_meta( $log->ID, '_badgeos_log_achievement_id', true ),
            ) );

        }

    }

    // Check remaining logs
    $logs_to_import = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*)
         FROM $wpdb->posts
         WHERE post_type = %s
          AND ID NOT IN (
            SELECT lm.meta_value FROM {$ct_table->meta->db->table_name} AS lm WHERE lm.meta_key = %s
          )",
        'badgeos-log-entry',
        $prefix . 'legacy_log_id'
    ) );

    if( absint( $logs_to_import ) !== 0 ) {
        // Return a run again action
        wp_send_json_success( array(
            'run_again' => true,
            'message' => sprintf( __( 'Remaining logs %d', 'gamipress' ), absint( $logs_to_import ) )
        ) );
    } else {
        // Return a success message
        wp_send_json_success( __( 'BadgeOS logs has been migrated successfully.', 'gamipress' ) );
    }
}
add_action( 'wp_ajax_gamipress_badgeos_importer_import_logs', 'gamipress_badgeos_importer_ajax_import_logs' );

/**
 * Turns a BadgeOS type to a GamiPress trigger
 *
 * @param $type
 *
 * @return string|false
 */
function gamipress_badgeos_importer_convert_to_gamipress_trigger( $type ) {

    switch( $type ) {

        // WordPress triggers
        case 'wp_login':
            $trigger = 'gamipress_login';
            break;
        case 'badgeos_new_comment':
            $trigger = 'gamipress_new_comment';
            break;
        case 'badgeos_specific_new_comment':
            $trigger = 'gamipress_specific_new_comment';
            break;
        case 'badgeos_new_post':
            $trigger = 'gamipress_publish_post';
            break;
        case 'badgeos_new_page':
            $trigger = 'gamipress_publish_page';
            break;
        case 'specific-achievement':
        case 'any-achievement':
        case 'all-achievements':
            $trigger = $type;
            break;

        // BuddyPress

        case 'bp_core_activated_user':
            $trigger = 'gamipress_bp_activate_user';
            break;
        case 'xprofile_avatar_uploaded':
            $trigger = 'gamipress_bp_upload_avatar';
            break;
        case 'xprofile_updated_profile':
            $trigger = 'gamipress_bp_update_profile';
            break;
        case 'bp_activity_posted_update':
            $trigger = 'gamipress_bp_publish_activity';
            break;
        case 'bp_groups_posted_update':
            $trigger = 'gamipress_bp_group_publish_activity';
            break;
        case 'bp_activity_comment_posted':
            $trigger = 'gamipress_bp_new_activity_comment';
            break;
        case 'bp_activity_add_user_favorite':
            $trigger = 'gamipress_bp_favorite_activity';
            break;
        case 'friends_friendship_requested':
            $trigger = 'gamipress_bp_friendship_request';
            break;
        case 'friends_friendship_accepted':
            $trigger = 'gamipress_bp_friendship_accepted';
            break;
        case 'messages_message_sent':
            $trigger = 'gamipress_bp_send_message';
            break;
        case 'groups_group_create_complete':
            $trigger = 'gamipress_bp_new_group';
            break;
        case 'groups_join_group':
            $trigger = 'gamipress_bp_join_group';
            break;
        case 'groups_join_specific_group':
            $trigger = 'gamipress_bp_join_specific_group';
            break;
        case 'groups_invite_user':
            $trigger = 'gamipress_bp_invite_user';
            break;
        case 'groups_promote_member':
            $trigger = 'gamipress_bp_promote_member';
            break;
        case 'groups_promoted_member':
            $trigger = 'gamipress_bp_promoted_member';
            break;

        // bbPress
        case 'bbp_new_topic':
            $trigger = 'gamipress_bbp_new_topic';
            break;
        case 'bbp_new_reply':
            $trigger = 'gamipress_bbp_new_reply';
            break;

        // LearnDash
        case 'learndash_quiz_completed':
            $trigger = 'gamipress_ld_complete_quiz';
            break;
        case 'badgeos_learndash_quiz_completed_specific':
            $trigger = 'gamipress_ld_pass_specific_quiz';
            break;
        case 'badgeos_learndash_quiz_completed_fail':
            $trigger = 'gamipress_ld_fail_quiz';
            break;
        case 'learndash_lesson_completed':
            $trigger = 'gamipress_ld_complete_lesson';
            break;
        case 'learndash_course_completed':
        case 'badgeos_learndash_course_completed_tag':
            $trigger = 'gamipress_ld_complete_course';
            break;

        default:
            $trigger = false;
            break;
    }

    return $trigger;

}
