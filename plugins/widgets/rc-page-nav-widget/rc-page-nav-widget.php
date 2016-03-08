<?php
/*
Plugin Name: RC Page Navigation Widget
Description: Automatic Page Navigation Widget by RighteousCoder.
*/

// Creating the widget 
class rcpn_page_nav_widget extends WP_Widget {

function __construct() {
parent::__construct(
    // Base ID of your widget
    'rcpn_page_nav_widget', 

    //This address for this post is: <?php the_permalink(); 
    
    // Widget name will appear in UI
    __('WPBeginner Widget', 'rcpn_page_nav_widget_domain'), 

    // Widget description
    array( 'description' => __( 'Page Navigation Widget that provides automatic PREVIOUS, INDEX, and NEXT links in web-novel stories. Supports lineraly numbered stories. Volume/Chapter style stories to come in future revision.', 'rcpn_page_nav_widget_domain' ), ) 
    );
}

function rcpn_get_title( $args, $instance ){
    $title = ( isset($instance['title']) ) ? esc_attr( $instance['title'] ) : false;
    //$title = apply_filters( 'widget_title', $instance['title'] );
    //echo $title;
    if ( empty( $title ) ){ 
        $title = $args['default_title'];
    } 
    return $title;
}

function rcpn_print_title ( $args, $instance ){
  //  $title = $this->rcpn_get_title( $args, $instance );
    
    $supress_title = ( isset($instance['suppress_title']) ) ? $instance['suppress_title'] : false;
    $title = ( isset($instance['title']) ) ? esc_attr( $instance['title'] ) : false;
    
  // echo "title?: " . $title . " " . $instance['title'];
    
    // TODO make global settings impact title display
    ( $title && ( ! $supress_title ) ) ? print($args['before_title'] . $title . $args['after_title']) : null;
    
}

function rcpn_get_hr_above ( $args, $instance ){
    return ( isset($instance['hr_above']) ) ? esc_attr( $instance['hr_above'] ) : '0';
}

function rcpn_get_hr_below ( $args, $instance ){
    return ( isset($instance['hr_below']) ) ? esc_attr( $instance['hr_below'] ) : '0';
}

function rcpn_get_prev_word ( $args, $instance ){
    // TODO make global settings impact title display
    return ( isset($instance['prev_word']) ) ? esc_attr( $instance['prev_word'] ) : "PREVIOUS";
}

function rcpn_get_separator ( $args, $instance ){
    // TODO make global settings impact title display
    return ( isset($instance['separator']) ) ? esc_attr( $instance['separator'] ) : "PREVIOUS";
}

function rcpn_get_index_word ( $args, $instance ){
    // TODO make global settings impact title display
    return ( isset($instance['index_word']) ) ? esc_attr( $instance['index_word'] ) : "INDEX";
}

function rcpn_get_next_word ( $args, $instance ){
    // TODO make global settings impact title display
    return ( isset($instance['next_word']) ) ? esc_attr( $instance['next_word'] ) : "NEXT";
}


function rcpn_get_text_style ( $args, $instance ){
    // TODO make global settings impact title display
    return ( isset($instance['text_style']) ) ? esc_attr( $instance['text_style'] ) : "";
}

function rcpn_get_chapter_prefix ( $args, $instance ){
    // TODO make global settings impact title display
    return ( isset($instance['chapter_prefix']) ) ? esc_attr( $instance['chapter_prefix'] ) : "";
}

function rcpn_get_last_chapter_number ( $args, $instance ){
    // TODO make global settings impact title display
    return ( isset($instance['last_chapter_number']) ) ? esc_attr( $instance['last_chapter_number'] ) : "";
}

function rcpn_get_chapter_padding_character ( $args, $instance ){
    // TODO make global settings impact title display
    //return '0';
    return $instance['chapter_padding_character'];
   // return ( isset($instance['chapter_padding_character']) ) ? $instance['chapter_padding_character']  : '0';
}

function rcpn_get_chapter_padding_size ( $args, $instance ){
    // TODO make global settings impact title display
    return ( isset($instance['chapter_padding_size']) ) ? esc_attr( $instance['chapter_padding_size'] ) : '3';
}

function rcpn_get_chapter_number ( $args, $instance, $chapter_prefix, $chapter_padding_character, $chapter_padding_size ){
    ob_start();
    the_permalink();    
    $permalink = ob_get_clean();
    $pattern = '((.*' . $chapter_prefix . ')([0-9]+)(/))';
    preg_match($pattern, $permalink, $matches);
    $location = ltrim($matches[2], $chapter_padding_character);
    return $location;
}


// Creating widget front-end
// This is where the action happens
public function widget( $args, $instance ) {
    // Print title if set and not hidden.
    $this->rcpn_print_title( $args, $instance );
    
    $separator = $this->rcpn_get_separator( $args, $instance );
    $prev_word = $this->rcpn_get_prev_word( $args, $instance );
    $index_word = $this->rcpn_get_index_word( $args, $instance );
    $next_word = $this->rcpn_get_next_word( $args, $instance );
    $text_style = $this->rcpn_get_text_style( $args, $instance );
    $chapter_prefix = $this->rcpn_get_chapter_prefix( $args, $instance );
    $chapter_padding_character = $this->rcpn_get_chapter_padding_character( $args, $instance );
    $hr_above = $this->rcpn_get_hr_above( $args, $instance );
    $hr_below = $this->rcpn_get_hr_below( $args, $instance );
    $last_chapter_number = $this->rcpn_get_last_chapter_number( $args, $instance );
    
    
    $chapter_padding_size = $this->rcpn_get_chapter_padding_size( $args, $instance );
    $location = $this->rcpn_get_chapter_number( $args, $instance, $chapter_prefix, $chapter_padding_character, $chapter_padding_size );
        
    $nextLoc = intval($location) + 1;
    $prevLoc = intval($location) - 1;
    $nextLoc = str_pad($nextLoc, $chapter_padding_size, $chapter_padding_character, STR_PAD_LEFT);
    $prevLoc = str_pad($prevLoc, $chapter_padding_size, $chapter_padding_character, STR_PAD_LEFT);

    $ancestors = get_post_ancestors($post->ID);
    $parent = $ancestors[0];
    
    $out = "";
    if ( intval($hr_above) == '1' ) { $out = $out . "<hr />"; }
    $out = $out . "<p style=\"" . $text_style . "\">";
    
    if (intval($location) > 1){
        $out = $out . "<a href=\"" . get_permalink($parent) . $chapter_prefix . $prevLoc . "/\">" . $prev_word . "</a>";
    } else {
        $out = $out . "<del>". $prev_word . "</del>";
    }
    
    $out = $out . $separator;
    $out = $out . "<a href=\"" . get_permalink($parent) . "\">" . $index_word . "</a>";
    $out = $out . $separator;
    
    if (intval($location) < intval ($last_chapter_number) ) {
        $out = $out . "<a href=\"" . get_permalink($parent)  . $chapter_prefix . $nextLoc . "/\">" . $next_word . "</a>";
    } else {
        $out = $out . "<del>". $next_word . "</del>";
    }
    $out = $out . "</p>";
    if ( intval($hr_below) == 1 ) { $out = $out . "<hr />"; }  
      
    echo __( $out , 'rcpn_page_nav_widget_domain' );
   
//  echo $args['after_widget'];
}
		
// Widget Backend 
public function form( $instance ) {
    if ( empty( $instance[ 'title' ] ) ) {
        $title = $instance[ 'title' ];
    }
    else {
        $title = __( 'New title', 'rcpn_page_nav_widget_domain' );
    }
    
    if ( empty( $instance[ 'separator' ] ) ) {
        $separator = $instance[ 'separator' ];
    }
    else {
        $separator = __( '|', 'rcpn_page_nav_widget_domain' );
    }
        
    if ( empty( $instance[ 'prev_word' ] ) ) {
        $prev_word = $instance[ 'prev_word' ];
    }
    else {
        $prev_word = __( 'PREVIOUS', 'rcpn_page_nav_widget_domain' );
    }
    
    if ( empty( $instance[ 'index_word' ] ) ) {
        $index_word = $instance[ 'index_word' ];
    }
    else {
        $index_word = __( 'INDEX', 'rcpn_page_nav_widget_domain' );
    }
    
    if ( empty( $instance[ 'next_word' ] ) ) {
        $next_word = $instance[ 'next_word' ];
    }
    else {
        $next_word = __( 'NEXT', 'rcpn_page_nav_widget_domain' );
    }
    
    if ( empty( $instance[ 'chapter_prefix' ] ) ) {
        $chapter_prefix = $instance[ 'chapter_prefix' ];
    }
    else {
        $chapter_prefix = __( 'ch-', 'rcpn_page_nav_widget_domain' );
    }
    
    if ( empty( $instance[ 'text_style' ] ) ) {
        $text_style = $instance[ 'text_style' ];
    }
    else {
        $text_style = __( 'text-align: center;', 'rcpn_page_nav_widget_domain' );
    }
    
    if ( isset( $instance[ 'chapter_padding_size' ] ) ) {
        $chapter_padding_size = $instance[ 'chapter_padding_size' ];
    }
    else {
        $chapter_padding_size = __( '3', 'rcpn_page_nav_widget_domain' );
    }
    
   if ( isset( $instance[ 'chapter_padding_character' ] ) ) {
        $chapter_padding_character = $instance[ 'chapter_padding_character' ];
   }
   else {
       $chapter_padding_character = __( '0', 'rcpn_page_nav_widget_domain' );
   }
    
    if ( empty( $instance[ 'suppress_title' ] ) ) {
        $suppress_title = $instance[ 'suppress_title' ];
    }
    else {
        $suppress_title = __( '0', 'rcpn_page_nav_widget_domain' );
    }
    
    if ( empty( $instance[ 'hr_above' ] ) ) {
        $hr_above = $instance[ 'hr_above' ];
    }
    else {
        $hr_above = __( '1', 'rcpn_page_nav_widget_domain' );
    }
    
    if ( empty( $instance[ 'hr_below' ] ) ) {
        $hr_below = $instance[ 'hr_below' ];
    }
    else {
        $hr_below = __( '1', 'rcpn_page_nav_widget_domain' );
    }
    
// Widget admin form
?>
<p>

<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />

<label for="<?php echo $this->get_field_id( 'separator' ); ?>"><?php _e( 'Separator Character(s):' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'separator' ); ?>" name="<?php echo $this->get_field_name( 'separator' ); ?>" type="text" value="<?php echo esc_attr( $separator ); ?>" />

<label for="<?php echo $this->get_field_id( 'prev_word' ); ?>"><?php _e( 'String for Previous Link:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'prev_word' ); ?>" name="<?php echo $this->get_field_name( 'prev_word' ); ?>" type="text" value="<?php echo esc_attr( $prev_word ); ?>" />

<label for="<?php echo $this->get_field_id( 'index_word' ); ?>"><?php _e( 'String for Index Link:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'index_word' ); ?>" name="<?php echo $this->get_field_name( 'index_word' ); ?>" type="text" value="<?php echo esc_attr( $index_word ); ?>" />

<label for="<?php echo $this->get_field_id( 'next_word' ); ?>"><?php _e( 'String for Next Link:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'next_word' ); ?>" name="<?php echo $this->get_field_name( 'next_word' ); ?>" type="text" value="<?php echo esc_attr( $next_word ); ?>" />

<label for="<?php echo $this->get_field_id( 'chapter_prefix' ); ?>"><?php _e( 'Chapter Prefix:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'chapter_prefix' ); ?>" name="<?php echo $this->get_field_name( 'chapter_prefix' ); ?>" type="text" value="<?php echo esc_attr( $chapter_prefix ); ?>" />

<label for="<?php echo $this->get_field_id( 'text_style' ); ?>"><?php _e( 'Text Style:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'text_style' ); ?>" name="<?php echo $this->get_field_name( 'text_style' ); ?>" type="text" value="<?php echo esc_attr( $text_style ); ?>" />

<label for="<?php echo $this->get_field_id( 'chapter_padding_size' ); ?>"><?php _e( 'Total Size after Padding:' ); ?></label> 
<input class="widefat" maxlength="1" id="<?php echo $this->get_field_id( 'chapter_padding_size' ); ?>" name="<?php echo $this->get_field_name( 'chapter_padding_size' ); ?>" type="text" value="<?php echo esc_attr( $chapter_padding_size ); ?>" />

<label for="<?php echo $this->get_field_id( 'chapter_padding_character' ); ?>"><?php _e( 'Padding Character(s):' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'chapter_padding_character' ); ?>" name="<?php echo $this->get_field_name( 'chapter_padding_character' ); ?>" type="text" value="<?php echo $chapter_padding_character ; ?>" />

</p>

<br />
<label for="<?php echo $this->get_field_id('suppress_title'); ?>" title="<?php _e('Do not output widget title in the front-end.');?>">
<input idx="<?php echo $this->get_field_id('suppress_title'); ?>" name="<?php echo $this->get_field_name('suppress_title'); ?>" type="checkbox" value="1" <?php checked($instance['suppress_title'],'1', true);?> /> <?php _e('Suppress Title Output');?>
</label>

</p>

<br />
<label for="<?php echo $this->get_field_id('hr_above'); ?>" title="<?php _e('Put a &#60;hr /&#62; line above the links.');?>">
<input idx="<?php echo $this->get_field_id('hr_above'); ?>" name="<?php echo $this->get_field_name('hr_above'); ?>" type="checkbox" value="1" <?php checked($instance['hr_above'],'1', true);?> /> <?php _e('&#60;hr /&#62; line above.');?>
</label>

</p>

<br />
<label for="<?php echo $this->get_field_id('hr_below'); ?>" title="<?php _e('Put a &#60;hr /&#62; line below the links.');?>">
<input idx="<?php echo $this->get_field_id('hr_below'); ?>" name="<?php echo $this->get_field_name('hr_below'); ?>" type="checkbox" value="1" <?php checked($instance['hr_below'],'1', true);?> /> <?php _e('&#60;hr /&#62; line below.');?>
</label>

<?php 
}
	
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['separator'] = ( ! empty( $new_instance['separator'] ) ) ? strip_tags( $new_instance['separator'] ) : '';
    $instance['next_word'] = ( ! empty( $new_instance['next_word'] ) ) ? strip_tags( $new_instance['next_word'] ) : '';
    $instance['index_word'] = ( ! empty( $new_instance['index_word'] ) ) ? strip_tags( $new_instance['index_word'] ) : '';
    $instance['prev_word'] = ( ! empty( $new_instance['prev_word'] ) ) ? strip_tags( $new_instance['prev_word'] ) : '';
    $instance['chapter_prefix'] = ( ! empty( $new_instance['chapter_prefix'] ) ) ? strip_tags( $new_instance['chapter_prefix'] ) : '';
    $instance['text_style'] = ( ! empty( $new_instance['text_style'] ) ) ? strip_tags( $new_instance['text_style'] ) : '';
    $instance['chapter_padding_size'] = ( ! empty( $new_instance['chapter_padding_size'] ) ) ? strip_tags( $new_instance['chapter_padding_size'] ) : '';
    $instance['chapter_padding_character'] = $new_instance['chapter_padding_character'] ;
        
    $instance['suppress_title'] = ( ! empty( $new_instance['suppress_title'] ) ) ? strip_tags( $new_instance['suppress_title'] ) : '';
    $instance['hr_above'] =  $new_instance['hr_above'] ;
    $instance['hr_below'] =  $new_instance['hr_below'] ;
    
    return $instance;
}

} // Class rcpn_widget ends here

// Register and load the widget
function rcpn_load_widget() {
	register_widget( 'rcpn_page_nav_widget' );
}
add_action( 'widgets_init', 'rcpn_load_widget' )

?>
