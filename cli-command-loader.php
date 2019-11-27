<?php 

namespace WooSubscriptions;

use WP_CLI, WP_CLI_Command;

if( class_exists( 'WP_CLI_Command' ) ) {

    class Commands extends WP_CLI_Command { 

        public function delete( $args, $assoc_args ) {

            $product_id = isset( $assoc_args['id'] ) ? $assoc_args['id'] : false;
            // $deleted_subscriptions = array();
            // $upload_dir = wp_upload_dir();

            if( ! $product_id ) {
                WP_CLI::error( 'Invalid arguments. Please provide product ID, ex. --id={id}' );
            }

            $args = array(
                'limit' => isset( $assoc_args['limit'] ) ? $assoc_args['limit'] : '10'
            );

            // create progress bar
		    $progress = \WP_CLI\Utils\make_progress_bar( 'Deleting subscriptions for product ID ' . $product_id, 0 );

            $subscriptions = wcs_get_subscriptions_for_product( $product_id, 'ids', $args );
            
            // $handle = fopen( $upload_dir['basedir'] . "/deleted_subscriptions.txt","wb" );

            foreach( $subscriptions as $subscription_id ) {

                // Run post delete command to delete subscription. --force is used as this post type does not support being sent to trash. 
                WP_CLI::runcommand( 'post delete ' . $subscription_id . ' --force' );

                // fwrite( $handle, 'Deleted subscriptions with ID ' . $subscription_id . PHP_EOL );

                // +1 for progress
                $progress->tick();
            }

            // fclose( $handle );

            // progress finished
            $progress->finish();

            // success message
            WP_CLI::success( $args['limit'] . ' Subscriptions deleted!' );
        }
    }

    WP_CLI::add_command( 'woo-subscription', 'WooSubscriptions\Commands' );
}