<?php
/**
* Plugin Name: WP User Testimonials
* Plugin URI: https://github.com/andresgmh/wp-user-testimonials
* Description: Custom testimonials form.
* Version: 1.0
* Author: Andres Menco Haeckermann
* Author URI: https://github.com/andresgmh
**/

/**
 * @return  string  $html   HTML form.
 */
function wp_ut_shortcode__testimonail_post_form() {

    ob_start(); ?>
    <form action="" id="testimonial-form-new-post">
        <fieldset>
            <legend>Add Testimonial</legend>
            <div class="form-group">
                <p>
                	<input type="text" id="post_title" placeholder="Post Title" class="form-control" name="post_title" required/>
                </p>
                <p>
                	<textarea id="post_content" placeholder="Post Text" class="form-control" name="post_content" required></textarea>
            	</p>
            	<p>
            		<?php $user_id = get_current_user_id();?>
            		
            		By: <b><?php echo ($user_id)?  get_the_author_meta( 'user_login' , $user_id ):'Anonymous user';?></b>

            	</p>
  
            </div>

			<p>
	            <button type="submit"
	                    class="submit">Submit
	            </button>
        	</p>
        </fieldset>
        <a href=""
           class="preview-link"
           target="_blank"
           style="display: none;"
           rel="nofollow">Preview Link</a>
    </form>
    <?php
    $html = ob_get_clean();

    return $html;
}

/*
Custom plugin scripts
*/

function wp_ut_script__new_post_form() {
    wp_enqueue_script(
        'testimonial-post-script',
        plugins_url( 'js/script.js', __FILE__ ),
        array( 'jquery' ),
        '1.0.0',
        true
    );

    wp_localize_script(
        'testimonial-post-script',
        'localized_testimonial_post_form',
        array(
            'admin_ajax_url' => admin_url( 'admin-ajax.php' ),
        )
    );
}

/*
	Insert custom post
*/

function wp_ut_insert_testimonial_new_post() {
    /**
     * @var     array $r Initialize response variable.
     */
    $r = array(
        'error'        => '', // Error message.
        'html'         => '', // Any message/HTML you want to output to the logged in user.
        'preview_link' => '', // Preview link URL.
        'post'         => '' // The created/updated post.
    );

    $user_id = get_current_user_id();

    //Post array

    $postarr = array(
        'ID'          => '', 
        'post_title'  => 'Post title',
        'post_content'  => 'Post body',
        'post_status' => 'pending',
        'post_type'   => 'testimonials',
    );

    parse_str( $_POST['form_data'], $form_data );
    $_POST['post_author'] = $user_id;
    $postarr['post_author'] = $user_id;
    
    $postarr = array_merge( $postarr, $form_data );

    /**
     * wp_insert_post
     */
    $new_post = wp_insert_post(
        $postarr,
        true
    );

    // Output the error message.
    if ( is_wp_error( $new_post ) ) {
        $r['error'] = $new_post->get_error_message();

        echo json_encode( $r );

        exit;
    }

    exit;
}


/*
Add author column
*/
function wp_ut_add_user_column( $columns ) {
    return array_merge( $columns, 
        array( 'custom_post_author' => __( 'Author', 'wp_ut' ) ) );
}

/*
Display author name value
*/

function wp_ut_display_author_name( $column, $post_id ) {
    if ($column == 'custom_post_author'){
    	$author_id = get_post_field ('post_author', $post_id);

    	if(!($author_id==0)){
			$display_name = get_the_author_meta( 'user_login' , $author_id );
		}
		else{
			$display_name = 'Anonymous user';
		}

		echo $display_name;
    	
    }
}

/*
Testminonials list shortcode
*/


function wp_ut_testimonials_shortcode($atts){
    //merge the passed attributes with defaults
    extract(
        shortcode_atts(
            array(
                'post_type'         => 'testimonials',
                'post_status'       => 'publish',
                'posts_per_page'    => 15,
            ),
            $atts
        )
    );

 
    $args = array(
        'post_type'         => $post_type,
        'post_status'       => $post_status,
        'posts_per_page'    => $posts_per_page
    );

    ob_start();

    $my_query = new WP_Query( $args );
    if( $my_query->have_posts() ) {
        ?>

        <div id="testimonials-list">
            <?php
            while ($my_query->have_posts()) : $my_query->the_post();?>
                <p class="testimonial-row">
                     <fieldset>
                     	<legend><a href="<?php the_permalink()?>"><?php the_title(); ?></a></legend>
                    	<span class="s-right"><?php echo the_excerpt(); ?></span>
                	</fieldset>
                    
                </p>
            <?php endwhile; ?>
        </div>
        <?php 
    }
    wp_reset_query();//reset the global variable related to post loop
    $retVal = ob_get_contents();
    ob_end_clean();

    return $retVal;
}

//archive page shortcode
add_shortcode('testimonials_list', 'wp_ut_testimonials_shortcode');

//Author Column
add_action( 'manage_testimonials_posts_custom_column' , 'wp_ut_display_author_name', 10, 2 );
add_filter( 'manage_testimonials_posts_columns' , 'wp_ut_add_user_column' );

// Ads shortcode
add_shortcode( 'testimonial_post_form', 'wp_ut_shortcode__testimonail_post_form' );

// Use wp_enqueue_scripts action hook so you can correctly localize the script with admin ajax URL.
add_action( 'wp_enqueue_scripts', 'wp_ut_script__new_post_form' );

// Prefix 'wp_ajax_' is mandatory.
add_action( 'wp_ajax_testimonial_new_post', 'wp_ut_insert_testimonial_new_post' );
add_action('wp_ajax_nopriv_testimonial_new_post', 'wp_ut_insert_testimonial_new_post');


?>