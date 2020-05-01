<?php

// Dev mode enabled
// Use this for uncompressed custom css codes
//if ( ! defined( 'BF_DEV_MODE' ) ) {
//	define( 'BF_DEV_MODE', TRUE );
//}

/* Desabilitar comentarios globalmente */
function filter_media_comment_status( $open, $post_id ) {
    $post = get_post( $post_id );
    if( $post->post_type == 'attachment' ) {
        return false;
    }
    return $open;
}
add_filter( 'comments_open', 'filter_media_comment_status', 10 , 2 );

/* Remover metabox de WPML */
function so_remove_wpml_meta_box() {
	$screen = get_current_screen();
	remove_meta_box( 'icl_div_config', $screen->post_type, 'normal' );
}
add_action( 'admin_head', 'so_remove_wpml_meta_box' );

/* Remover animación preview post-page */
function enqueue_gutenberg_js(){
    wp_enqueue_script( 'tas-scripts', get_stylesheet_directory_uri() . '/js/admin.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'enqueue_block_editor_assets',  'enqueue_gutenberg_js' );

/* Remover logo Wordpress de admin */
function example_admin_bar_remove_logo() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'wp-logo' );
}
add_action( 'wp_before_admin_bar_render', 'example_admin_bar_remove_logo', 0 );

/* Cambiar styles del login de Wordpress */
function style_personalizado() { ?> 
    <style type="text/css"> 
        body.login div#login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri()?>/assets/logo.png);
            width:240px;
            height:190px; 
            background-size: contain;
        }
        /
        /*body.login{
            background: #d2af74;
        }*/
       
    </style>
     <?php 
    } 
add_action( 'login_enqueue_scripts', 'style_personalizado' );

/* Cambiar url del logo de login */
function my_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

/* Remover versión de Wordpress */
function wpbeginner_remove_version() {
    return '';
    }
add_filter('the_generator', 'wpbeginner_remove_version');
  

/* Redireccionar despues de entrar */
function login_redirect( $redirect_to, $request, $user ){
    return get_admin_url().'post.php?post=83&action=edit&lang=es';
}
add_filter( 'login_redirect', 'login_redirect', 10, 3 );


/*
function admin_style() {
	wp_enqueue_style('admin-styles', get_template_directory_uri().'/admin.css');
}
add_action('admin_enqueue_scripts', 'admin_style');
*/

function cambiarNombreSidebar( $sidebar ){
    global $wp_registered_sidebars;

    if ( 'Primary Sidebar' !== $sidebar[ 'name' ] )
        return;
    $id = $sidebar[ 'id' ];
    $sidebar[ 'name' ] = 'Casos Multimedia';
    $wp_registered_sidebars[ $id ] = $sidebar;
    }
add_action( 'register_sidebar', 'cambiarNombreSidebar');

/* Bloques por default en nuevos casos */
function cargarBloquesPorDefault( $args, $post_type ) {
    if ( 'caso_multimedia' == $post_type ) {
      $args['template_insert'] = 'all';
      $args['template'] = [
        [
          'mdlr/featured-image',
		
        ],
		[
		   'lazyblock/autor'
		],
		
      ];
    }
    return $args;  
  }
  add_filter( 'register_post_type_args', 'cargarBloquesPorDefault', 20, 2 );

/* Parsear blockes de gutenberg para encontrar anexos y añadirlos a la tabla de contenido */
function parsear_bloques() {
    ob_start();
    global $post;
    $blocks = parse_blocks( $post->post_content );
    echo '<ol>';
    foreach( $blocks as $block ) {
		var_dump($block);
            if ($block['blockName'] === 'lazyblock/video'){
                    if (!empty($block ['attrs']['video'])) {
                        $url_video_block = ($block ['attrs']['video']);
                        $regex = "/[[Video:(http)\:\/\/[a-zA-Z\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?[docx|mp4|doc|pdf|jpg|png|jpeg|mp3|gif]/";
                        $regex01 = "(http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w\- .\/?%&=;]*)?$)";
                        preg_match($regex, $url_video_block, $matches);
                        $url_video_block =  $matches[0];
                        echo $url_video_block;
                        preg_match($regex01, $url_video_block, $matches);
                        $url_video_block =  $matches[0];
                        echo $url_video_block;
                        echo '<li><a href="'.$url_video_block.'" class="open-lightbox">';
                            echo $block['attrs']['titulo'];
                        echo '</a></li>';
                }else{
                    echo '<li><a href="'.$block ['attrs']['youtube'].'" class="open-lightbox">';
                        echo $block['attrs']['titulo'];
                    echo '</a></li>';
                }
            }
      }
      echo '</ol>';
      $output = ob_get_contents();
      ob_end_clean();
      return $output;
   }
add_shortcode('videos', 'parsear_bloques');

if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

function wp_rauf_check_filetype_and_ext($wp_filetype, $file, $filename, $mimes) {
    if(!$wp_filetype['proper_filename']) {
      $wp_filetype['proper_filename'] = remove_accents(str_replace('.'.$wp_filetype['ext'], '', $filename)).'.'.$wp_filetype['ext'];
    }
    return $wp_filetype;
  }
  add_filter( 'wp_check_filetype_and_ext', 'wp_rauf_check_filetype_and_ext', 10, 4 );

function parsear_anexos() {
    ob_start();
    global $post;
    $blocks = parse_blocks( $post->post_content );
    echo '<ol>';
    foreach( $blocks as $block ) {
      // var_dump($block);
            if ($block['blockName'] === 'lazyblock/anexo'){
                $url_anexo = ($block ['attrs']['documento']);
                //$regex = "/[[Video:(http)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?4/";
                $regex = "/[[Video:(http)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?[docx|mp4|doc|pdf|jpg|png]/";
                $regex01 = '(http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- ./?%&=;]*)?$)';
                preg_match($regex, $url_anexo, $matches);
                $url_anexo =  $matches[0];
                preg_match($regex01, $url_anexo, $matches);
                $url_anexo =  $matches[0];
                echo '<li><a href="'.$url_anexo.'" class="open-lightbox">';
                  echo $block['attrs']['titulo'];
                echo '</a></li>';
            }

      }
      echo '</ol>';
      $output = ob_get_contents();
      ob_end_clean();
      return $output;
   }
add_shortcode('anexos', 'parsear_anexos');


/* Bloque de video  */
function bloque_video( $output, $attributes ){
   // global $videos, $nombres_videos;
    //$videos[] = $attributes['video']['url'];
    //$nombres_videos[] = $attributes['nombre'];
    ob_start();
    ?>

       <div class="<?php echo strtr($attributes['titulo'], " ", "_");?>">
		 <?php if (!empty ($attributes['youtube'] )) : ?>
            <?php 
                $url_video_yt = $attributes['youtube'];
                function generateVideoEmbedUrl($url){
                    //This is a general function for generating an embed link of an FB/Vimeo/Youtube Video.
                    $finalUrl = '';
                    if(strpos($url, 'facebook.com/') !== false) {
                        //it is FB video
                        $finalUrl.='https://www.facebook.com/plugins/video.php?href='.rawurlencode($url).'&show_text=1&width=200';
                    }else if(strpos($url, 'vimeo.com/') !== false) {
                        //it is Vimeo video
                        $videoId = explode("vimeo.com/",$url)[1];
                        if(strpos($videoId, '&') !== false){
                            $videoId = explode("&",$videoId)[0];
                        }
                        $finalUrl.='https://player.vimeo.com/video/'.$videoId;
                    }else if(strpos($url, 'youtube.com/') !== false) {
                        //it is Youtube video
                        $videoId = explode("v=",$url)[1];
                        if(strpos($videoId, '&') !== false){
                            $videoId = explode("&",$videoId)[0];
                        }
                        $finalUrl.='https://www.youtube.com/embed/'.$videoId;
                    }else if(strpos($url, 'youtu.be/') !== false){
                        //it is Youtube video
                        $videoId = explode("youtu.be/",$url)[1];
                        if(strpos($videoId, '&') !== false){
                            $videoId = explode("&",$videoId)[0];
                        }
                        $finalUrl.='https://www.youtube.com/embed/'.$videoId;
                    }else{
                        //Enter valid video URL
                    }
                    return $finalUrl;
                }
            ?>
            <iframe width="560" height="315" src='<?php echo generateVideoEmbedUrl($url_video_yt);?>' ></iframe>
		<?php else: ?>
			<video controls src="<?php echo esc_html( $attributes['video']['url'] ); ?>">
        	</video>
		<?php endif; ?>
       <?php

       return ob_get_clean();
   }
   
// filter for Frontend output.
add_filter( 'lazyblock/video/frontend_callback', 'bloque_video', 10, 2 );
// filter for Editor output.
add_filter( 'lazyblock/video/editor_callback', 'bloque_video', 10, 2 );


//add_filter('video_data', 'get_data_video' );
/*
function tabla_contenido_videos(){
    global $videos, $nombres_videos;
    for($count = 0; $count <= $videos; $count++){
        echo '<a href="'.$videos[$count].'" target="_blank" class="videos-css">'.$nombres_videos[$count].'</a>';
    }
}
//add_filter( 'the_content', 'tabla_contenido_videos', 6); 

//add_shortcode('videos', 'my_block_output');

*/
add_filter( 'allowed_block_types', 'misha_allowed_block_types' );
 
function misha_allowed_block_types( $allowed_blocks ) {
 
	return array(
		'core/image',
		'core/paragraph',
		'core/heading',
		'core/quote',
        'lazyblock/video',
        'lazyblock/anexo',
        'core/gallery',
	);
 
}

 
add_action( 'enqueue_block_editor_assets', function() {
    wp_enqueue_style( 'editor-style', get_stylesheet_directory_uri() . "/css/editor-style.css", false, '1.0', 'all' );
    wp_enqueue_script('editor-js', get_stylesheet_directory_uri() . "/editor-js.js");
} );
    
// disable custom colors
add_theme_support( 'disable-custom-colors' );

// remove color palette
add_theme_support( 'editor-color-palette' );