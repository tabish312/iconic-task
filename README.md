# IKONIC TASKS

1.      Please setup a Blank WordPress project

   Created in the xampp folder named as iconic

2.      Do initial commit of blank project on a GitHub repository (Just push wp-content/plugins and wp-content/themes Folders)

   Did the initial commit at https://github.com/tabish312/iconic-task

3.      Write a function that will redirect the user away from the site if their IP address starts with 77.29. Use WordPress native hooks and APIs to handle this.

    ```php
    function redirect_user() {
        $ip = $_SERVER['REMOTE_ADDR'];
        $ip = explode('.', $ip);
        if($ip[0] == '77' && $ip[1] == '29') {
            wp_redirect('https://www.google.com/');
            exit;
        }
    }
   ````

4.      Register post type called "Projects" and a taxonomy "Project Type" for this post type.

   ```php
   function register_projects_post_type() {
       register_post_type( 'projects',
           array(
               'labels'      => array(
                   'name'          => __( 'Projects' ),
                   'singular_name' => __( 'Project' )
               ),
               'public'      => true,
               'has_archive' => true,
               'supports'    => array( 'title', 'editor', 'thumbnail' ),
               'taxonomies'  => array( 'project_type' ),
               'rewrite'     => array( 'slug' => 'projects', 'with_front' => false, ),
           )
       );
   }
   
   add_action( 'init', 'register_projects_post_type' );
   
   function register_project_type_taxonomy() {
       register_taxonomy( 'project_type', 'projects',
           array(
               'labels'       => array(
                   'name'          => __( 'Project Types' ),
                   'singular_name' => __( 'Project Type' )
               ),
               'public'       => true,
               'rewrite'      => array( 'slug' => 'project_type' ),
               'hierarchical' => true,
           )
       );
   }
   
   add_action( 'init', 'register_project_type_taxonomy' );
   ```
5.      Create a WordPress archive page that displays six Projects per page with pagination. Simple pagination is enough (with next, prev buttons)

   ```php
   <?php
   get_header(); ?>
   
       <div class="post-grid">
   
           <?php
           $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
           $args  = array(
               'post_type'      => 'projects',
               'posts_per_page' => 6,
               'paged'          => $paged,
               'orderby'        => 'date',
               'order'          => 'ASC',
           );
           $query = new WP_Query( $args );
   
           while ( $query->have_posts() ) : $query->the_post();
               echo '<div class="post-grid-column">';
               get_template_part( 'content', 'projects' );
               echo '</div>';
           endwhile;
           wp_reset_postdata();
           ?>
       </div>
   
       <div class="pagination">
           <?php
           echo paginate_links( array(
               'total'     => $query->max_num_pages,
               'prev_text' => 'Previous',
               'next_text' => 'Next',
               'mid_size'  => 1
           ) );
           ?>
       </div>
   <?php get_footer(); ?>
   ```

   ```php
   <article class="post-grid-item">
    <a href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'medium' ); ?>
		<?php endif; ?>
        <h2><?php the_title(); ?></h2>
    </a>
    <p><?php the_excerpt(); ?></p>
   </article>
   ````

6.      Create an Ajax endpoint that will output the last three published "Projects" that belong in the "Project Type" called "Architecture" If the user is not logged in. If the user is logged In it should return the last six published "Projects" in the project type call. "Architecture". Results should be returned in the following JSON format {success: true, data: [{object}, {object}, {object}, {object}, {object}]}. The object should contain three properties (id, title, link).

   ```php
   function get_projects() {
       $projects = array();
       $args     = array(
           'post_type'      => 'projects',
           'posts_per_page' => is_user_logged_in() ? 6 : 3,
           'orderby'        => 'date',
           'order'          => 'DESC',
           'tax_query'      => array(
               array(
                   'taxonomy' => 'project_type',
                   'field'    => 'slug',
                   'terms'    => 'architecture',
               ),
           ),
       );
       $query    = new WP_Query( $args );
       if ( $query->have_posts() ) {
           while ( $query->have_posts() ) {
               $query->the_post();
               $projects[] = array(
                   'id'    => get_the_ID(),
                   'title' => get_the_title(),
                   'link'  => esc_url( get_the_permalink() ),
               );
           }
           wp_reset_postdata();
       }
   
       // We can do with this way
       //	wp_send_json_success( $projects );
   
       /**
        * Alternate way for pretty print of JSON
        */
       header( 'Content-Type: application/json' );
       echo json_encode( array(
           'success' => true,
           'data'    => $projects,
       ), JSON_PRETTY_PRINT );
       wp_die();
   }
   
   add_action( 'wp_ajax_get_projects', 'get_projects' );
   add_action( 'wp_ajax_nopriv_get_projects', 'get_projects' );
   ```

7.      Use the WordPress HTTP API to create a function called hs_give_me_coffee() that will return a direct link to a cup of coffee. for us using the Random Coffee API [JSON].

    ```php
    function hs_give_me_coffee() {
         $response = wp_remote_get( 'https://random-data-api.com/api/coffee/random_coffee' );
         if ( is_wp_error( $response ) ) {
              return false;
         }
         $body = wp_remote_retrieve_body( $response );
         $body = json_decode( $body );
         if ( ! empty( $body->image ) ) {
              return $body->image;
         }
         return false;
    }
    ```

8.      Use this API https://api.kanye.rest/ and show 5 quotes on a page.

   ```php
   function get_kanye_quotes() {
       $response = wp_remote_get( 'https://api.kanye.rest/' );
       if ( is_wp_error( $response ) ) {
           return false;
       }
       $body = wp_remote_retrieve_body( $response );
       $body = json_decode( $body );
       if ( ! empty( $body->quote ) ) {
           return $body->quote;
       }
       return false;
   }
   ```