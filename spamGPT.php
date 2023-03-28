<?php
/*
Plugin Name: Stop Spam Comments
Plugin URI: https://yourwebsite.com/
Description: This plugin helps to stop spam comments on your WordPress posts.
Version: 1.0
Author: Your Name
Author URI: https://yourwebsite.com/
License: GPL2
*/

// Plugin code goes here
?>

// Define the reCAPTCHA keys
define( 'RECAPTCHA_SITE_KEY', 'your-site-key-goes-here' );
define( 'RECAPTCHA_SECRET_KEY', 'your-secret-key-goes-here' );

// Add the reCAPTCHA script to the footer of the page
add_action( 'wp_footer', 'stop_spam_comments_add_recaptcha_script' );
function stop_spam_comments_add_recaptcha_script() {
    ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php
}

// Add the reCAPTCHA field to the comment form
add_action( 'comment_form', 'stop_spam_comments_add_recaptcha_field' );
function stop_spam_comments_add_recaptcha_field() {
    ?>
    <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
    <?php
}

// Validate the reCAPTCHA response when a comment is submitted
add_action( 'pre_comment_on_post', 'stop_spam_comments_validate_recaptcha' );
function stop_spam_comments_validate_recaptcha() {
    if ( isset( $_POST['g-recaptcha-response'] ) ) {
        $response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
            'body' => array(
                'secret'   => RECAPTCHA_SECRET_KEY,
                'response' => $_POST['g-recaptcha-response'],
            ),
        ) );

        $response_data = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( ! $response_data['success'] ) {
            wp_die( __( 'Error: Invalid reCAPTCHA response.', 'stop-spam-comments' ) );
        }
    } else {
        wp_die( __( 'Error: Please complete the reCAPTCHA.', 'stop-spam-comments' ) );
    }
}
