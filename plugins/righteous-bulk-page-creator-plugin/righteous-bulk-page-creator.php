<?php
/*
 * Plugin Name: Righteous Bulk Page Creator plugin
 * Description: plugin to allow you to crate novel pages in bulk
 * Author: Michael A. Walker
 * Version: 0.5.0.0
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit('no access'); // disable direct access (skript kiddies, etc.)
}

class righteous_bulk_page_creator {

    

    // Holds the values to be used in the fields callbacks
    private $options;
    // Holds the values for actual page creation.
    private $pageParams = array();

    /*
     * Constants to use throughout class.
     */
    // About physical Files. 
    const PLUGIN_FOLDER                 = 'righteous-bulk-page-creator-plugin';
    const JS_FILE                       = 'righteous-bulk-page-creator.js';
    const CSS_FILE                      = 'righteous-bulk-page-creator.css';
    // About the Settings Page itself.
    const PAGE_NAME                     = 'rc_bulk_page_creator';
    const PAGE_TITLE                    = 'Righteous Bulk Page Creator';
    const MENU_TITLE                    = 'Righteous Bulk Page Creator';
    const CAPABILITY                    = 'manage_options';
    const MENU_SLUG                     = 'rc_bulk_page_creator';
    const VAR_PREFIX                    = 'righteous_bulk_page_creator';
    const OPTION_GROUP                  = 'righteous_bulk_page_creator_options_group';
    const OPTIONS                       = 'righteous-bulk-page-creator-plugin-options';
    const NEW_PAGES                     = 'righteous-bulk-page-creator-plugin-new-pages';
    // attempts to start to translated
    const settingsPageTitleString       = 'Settings for generating pages:';
    const modeSelectString              = 'Make Pages based on<br />Chapters or Volumes:';
    const parentPageSelectString        = 'Parent Page:';
    const pageTemplateSelectionString   = 'Select Page Template:';
    const volumePrefixString            = 'Volume Prefix:';
    const chapterPrefixString           = 'Chapter Prefix:';
    const volumePaddingOptionsString    = 'Volume Padding Options:';
    const chapterPaddingOptionsString   = 'Chapter Padding Options:';
    const titleseparatorOptionsString   = 'Title Separator Options:';
    const newPageTextString             = 'Enter Pages to Create:';
    
    /**
     * Start up
     */
    public function __construct()
        
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
        
        if( isset($_GET['page']) && $_GET['page']== self::PAGE_NAME ){
            add_action('admin_print_scripts', array( $this, 'righteous_bulk_page_creator_scripts_load' ) );
            add_action('admin_print_styles', array( $this, 'righteous_bulk_page_creator_styles_load' ) );
        }
    }
    
    /** 
     * mode_select_render
     */
    public function righteous_bulk_page_creator_scripts_load(){
        $jsFileTag = 'righteous-bulk-page-creator-js';
        wp_register_script( $jsFileTag , plugins_url() .'/'. self::PLUGIN_FOLDER .'/' . self::JS_FILE );
        wp_enqueue_script( $jsFileTag );
    }
    
    /** 
     * mode_select_render
     */
    public function righteous_bulk_page_creator_styles_load(){
        $cssFileTag = 'righteous-bulk-page-creator-css';
        wp_register_style($cssFileTag, plugins_url() .'/'. self::PLUGIN_FOLDER .'/' . self::CSS_FILE );
        wp_enqueue_style($cssFileTag);
    }
    
     /**
     * Add options page
     */
    public function add_plugin_page(){
     // This page will be under "Settings"
        add_menu_page(
            self::PAGE_TITLE ,
            self::MENU_TITLE ,
            self::CAPABILITY , 
            self::MENU_SLUG , 
            array( $this, 'create_admin_page' )
        );
    }
    
    /**
     * Register and add settings
     */
    public function page_init(){     
        // register a setting (and its callback)
        register_setting(
            self::OPTION_GROUP , // Option group
            self::OPTIONS //, // Option name
            //array( $this, 'sanitize' ) // Sanitize // TODO actually use this.
        );

        $setting_section_1 = 'setting_section_1';
        
        add_settings_section(
            $setting_section_1, // Row Name
            righteous_bulk_page_creator::settingsPageTitleString , // Title
            array( $this, 'print_section_info' ), // Creation Callback
            self::PAGE_NAME // Which Page
        );  
        
        add_settings_field( 
            'mode_select', 
            __( righteous_bulk_page_creator::modeSelectString , 'wordpress' ), 
            array( $this, 'mode_select_render'), 
            self::PAGE_NAME, 
            $setting_section_1,
            array( 'class' => 'all_row' )
        );
        
        add_settings_field( 
            'parent_page_select', 
            __( righteous_bulk_page_creator::parentPageSelectString , 'wordpress' ), 
            array( $this, 'parent_page_select_render'), 
            self::PAGE_NAME, 
            $setting_section_1,
            array( 'class' => 'all_row' )
        );
        
        add_settings_field( 
            'page_template_selection', 
            __( righteous_bulk_page_creator::pageTemplateSelectionString , 'wordpress' ), 
            array( $this, 'template_select_render'), 
            self::PAGE_NAME, 
            $setting_section_1,
            array( 'class' => 'all_row' )
        );
        
        add_settings_field( 
            'volume_prefix', 
            __( righteous_bulk_page_creator::volumePrefixString , 'wordpress' ), 
            array( $this, 'chapter_volume_prefix_render'), 
            self::PAGE_NAME, 
            $setting_section_1,
            array( 'class' => 'volume_row', 'mode' => 'volume'  )
        );
        
         add_settings_field( 
            'chapter_prefix', 
            __( righteous_bulk_page_creator::chapterPrefixString , 'wordpress' ), 
            array( $this, 'chapter_volume_prefix_render'), 
            self::PAGE_NAME, 
            $setting_section_1,
            array( 'class' => 'all_row', 'mode' => 'chapter'  )
        );
        
        add_settings_field( 
            'volume_padding', 
            __( righteous_bulk_page_creator::volumePaddingOptionsString , 'wordpress' ), 
            array( $this, 'padding_options_render'), 
            self::PAGE_NAME, 
            $setting_section_1,
            array( 'class' => 'volume_row', 'mode' => 'volume'  )
        );
        
        add_settings_field( 
            'chapter_padding', 
            __( righteous_bulk_page_creator::chapterPaddingOptionsString , 'wordpress' ), 
            array( $this, 'padding_options_render'), 
            self::PAGE_NAME, 
            $setting_section_1,
            array( 'class' => 'all_row', 'mode' => 'chapter'  )
        );
        
        add_settings_field( 
            'separator_options', 
            __( righteous_bulk_page_creator::titleseparatorOptionsString , 'wordpress' ), 
            array( $this, 'chapter_title_formatting_render'), 
            self::PAGE_NAME, 
            $setting_section_1,
            array( 'class' => 'all_row' )
        );
        
        add_settings_field( 
            'new_pages_text', 
            __( righteous_bulk_page_creator::newPageTextString , 'wordpress' ), 
            array( $this, 'new_pages_text_render'), 
            self::PAGE_NAME, 
            $setting_section_1,
            array( 'class' => 'all_row', )
        );
    }
    
    /** 
     * mode_select_render
     */
    public function new_pages_text_render(){
        echo "<table id=\"myTable\">";
        echo "<tr>";
            echo "<td>"; 
                echo "Separate Volumes with:' ; '" . 
                    "<br />" . "Separate Pages with:' , '" . 
                    "<br />" . "Volumes have: &lt;Volume Number&gt; : &lt;PageInfo&gt; (if you have volumes) " . 
                    "<br />" . "Use  ' -> ' to indicate page ranges" . 
                    "<br />" . "For Chapter Mode: example: Table of Content,1->300,afterward,index" .
                    "<br />" . "For Volume  Mode: example: 1:ToC,1->3,terms;2:1->3;3:1->3;4:1->3,index" ;
            echo "</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>";
            
            
            printf(
                '<input type="text" id="vol1" name="%s" value="%s"  size="60" />',
                self::OPTIONS . '[page_data]' ,
                 '1:ToC,1->3,terms;2:1->3;3:1->3;4:1->3,index'
            );
            echo "</td>";
                
            // button for future? work to make dynamic volume text boxes
            // <td><input type="button" class="button-secondary" value="Add Volume"  id="newvolumebutton" /></td>
            
        echo "</tr>";
        echo "</table>";
    }
    
    /** 
     * mode_select_render
     */  
    public function padding_options_render( $args ){ 
        $name = $args['mode'];
        
        $paddings = $array = array(
            "0 (No Padding.)" => "0",
            "2 (99 Vol. or less)" => "2",
            "3 (999 Ch. or less)" => "3",
            "4 (9999 Ch. or less)" => "4",
        );
        
        echo "<table>";
            echo "<tr>";
                echo "<td>Padding on?</br>(suggested)</td>";
                echo "<td>";
                    echo "<input type='checkbox' name='".self::OPTIONS."[" . $name . "_padding_checkbox]' value='1' checked></td>";
                echo "<td>Padding Size<br/>0 or 2 suggested.</td>";
                echo "<td>";
                    echo "<select name='".self::OPTIONS."[" . $name . "_padding_ammount]'>";
                        foreach ($paddings as $padding_name => $padding_size) {
                            if ( ($name == 'volume' && $padding_size == '2') || 
                                ($name == 'chapter' && $padding_size == '3') ){
                                $selected = 'selected';
                            } else{ 
                                $selected = '';
                            }
                            echo "<option ".$selected." value=\"$padding_size\">$padding_name</option>";
                        }
                        echo '</select>';
                echo '</td>';
                echo "<td>Padding Character<br />('0' suggested)</td>";
                echo "<td>";
                    echo "<input type='text' size=\"1\" placeholder=\"0\" maxlength=\"1\" name='". self::OPTIONS ."[" . $name . "_padding_character]' value='0'>";
                echo "</td>";
            echo "</tr>";
        echo "</table>";
    }
    
    /** 
     * mode_select_render
     */    
    public function chapter_title_formatting_render(){
        $separator = array(
            "&lt;Space&gt;" => "0",
            "&lt;Hyphen&gt;" => "1",
            "&lt;Under Score&gt;" => "2",
        );
      
        echo "<table>";
            
        $this->get_title_part_render( "Between Volume Word and Volume Number", "title_separator_1", "volume_row", $separator);
        
        $this->get_title_part_render( "Between Volume Number and Chapter Word", "title_separator_2", "volume_row", $separator);
        
        $this->get_title_part_render( "Between Chapter Word and Chapter Number", "title_separator_3", "chapter_row", $separator);
            
        echo "</table>";
    }

    /** 
     * mode_select_render
     */
    public function template_select_render(){
        $templates = get_page_templates(); 
        echo "<select name='".self::OPTIONS."[template_id]'>";
        echo '<option value="">Default</option>';
        foreach ( $templates as $template_name => $template_filename ) {
            echo "<option value=\"$template_filename\">$template_name</option>";
        }
        echo '</select>';
    }
    
    /** 
     * mode_select_render
     */    
    public function mode_select_render(){
        $modes = $array = array(
            "Chapter" => "1",
            "Volumes" => "2",
        );
        echo "<select id='genmode' name=" . self::OPTIONS . "[mode] >";
        //echo '<option value="">Default</option>';
        foreach ( $modes as $mode_name => $mode_filename ) {
            echo "<option value=\"$mode_filename\">$mode_name</option>";
        }
        echo '</select>';
    }
    
    /** 
     * parent_page_select_render
     */
    public function parent_page_select_render(){
        echo "<table border=\"0\">";
            echo "<tr>";
                echo "<td>";
                    wp_dropdown_pages('sort_column=menu_order&post_status=draft,publish&show_option_none=(No Parent)&name='.self::OPTIONS. "[parent_page_select]&id=parent_page_select");
                echo "</td>";
                echo "<td class=\"new_parent_page\">";
                    echo "<input id='new_page_text' type='text' cols='20' placeholder=\"New Page Name\" name='".self::OPTIONS."[new_parent_page]' id='new_parent_page' value=\"\">";
                echo "</td>";
            echo "</tr>";
        echo "</table>";
    }
    
    /** 
     * Print the Section text
     */
    public function print_section_info() {
        print 'Enter your settings below:';
    }

    /** 
     * chapter_volume_prefix_render
     */
    function chapter_volume_prefix_render( $args = 'chapter' ) { 
        $name=$args['mode'];
        
        echo '<table>';
        echo '<td>' . ucfirst($name) .' title-word: </td>';
        echo '<td>';
            echo "<input type='text' size='10' name='". self::OPTIONS ."[" . $name . "_prefix_for_title]' value='"  . ucfirst($name) . "'>";
        echo '</td>';

        echo '<td>' . ucfirst($name) .' url-slug-word: </td>';
        echo '<td>';
            echo "<input type='text' size='10' name='". self::OPTIONS ."[" . $name . "_prefix_for_slug]' value='" . ($name == 'chapter' ? 'ch' : 'vol') . "'>";
        echo '</td>';
        echo '</table>';    
    }
    
    /** 
     * get_title_part_render
     */
    private function get_title_part_render($separatorLabel = "", $separatorName= "", $className="", $separator = null){
    
        echo '<tr class="' . $className . '">';
            echo "<td>" . $separatorLabel . ":";
            
                echo "<select name='". self::OPTIONS ."[" . $separatorName . "]'>";
                echo '<option value="-1">None</option>';
                foreach ( $separator as $separator_name => $separator_value ) {
                
                    ( ($separatorName == 'title_separator_2') && ($separator_name == '&lt;Hyphen&gt;') ) ? $selected = 'selected' : $selected = '';
                    echo '<option value="' . $separator_value . '" '. $selected .'>' . $separator_name . '</option>';
                }
                echo '</select>';
                
            echo '</td>';
            
              echo "<td>";
                echo "Space Bedfore? :";
            echo '</td>';
            echo "<td>";
                echo "<input type='checkbox' name='".self::OPTIONS."[" . $separatorName . "_before_checkbox]' value='1' ";
                if ($separatorName == 'title_separator_2'){
                    echo "checked";
                }
                echo " >";
            echo '</td>';
            
            echo "<td>";
                echo "Space After? :";
            echo '</td>';
            echo "<td>";
                echo "<input type='checkbox' name='".self::OPTIONS."[" . $separatorName . "_after_checkbox]' value='1' ";
                if ($separatorName == 'title_separator_2'){
                    echo "checked";
                }
                echo " >";
            echo '</td>';
        echo "</tr>";
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ) {
        $new_input = array();
        if( isset( $input['id_number'] ) )
            $new_input['id_number'] = absint( $input['id_number'] );
        if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );
        return $new_input;
    }

    

    /** 
     * convert_separator
     */
    private function convert_separator($separator, $mode, $scope ){
        if ($mode == '1' && $scope == 'volume') {
            $separator = '';
        }
        switch ($separator) {
            case '0':
                $separator = ' ';
                break;
            case '1':
                $separator = '-';
                break;
            case '2':
                $separator = '_';
                break;
            default:
                $separator = ''; 
        }
        return $separator;
    }

    /**
     * create page
     */
    public function create_admin_page()
    {
        // If the user can't 'manage_options' then exit.
        if ( !current_user_can('manage_options') ){
            exit('Restricted');
        }
        
        // check if form was submitted.
        if ( isset($_POST[  self::OPTIONS ]) && $_POST[ self::OPTIONS ]!='' ){

            require 'libs/page-creation.php';
            $options = $_POST[ self::OPTIONS ];
            $page_data = $options["page_data"];
            $volumeData = PageCreationParser::parseString( $page_data );
            
            //$_POST[ self::OPTIONS ];
            ?>
            
             <div class="wrap">
                <h2>Righteous Bulk Page Creator</h2>           
                <?php echo "<form action='?page=" . $_GET['page'] . " ' method='post'>"; ?>

                <?php
                PageCreationParser::generatePagesWithFunct ( $volumeData , $this, 'createPages' , 'printPageParams' );
                
                submit_button(); 
                ?>
                
                </form>
            </div>
            
            <?php  
            
            $this->makeSettingsPage();  

        } else if ( isset( $_POST[ self::NEW_PAGES ] ) && $_POST[ self::NEW_PAGES ] != '' ){
            $this->createNewPages();
            
            echo '<h1>Congrats!!! Your pages are now created!!</h1>';
            
        } else {
            $this->makeSettingsPage();
        }
    }
    
    public function makeSettingsPage(){
        ?>
        <div class="wrap">
            <h2>Righteous Bulk Page Creator</h2>           
            <?php echo "<form action='?page=" . $_GET['page'] . " ' method='post'>"; ?>

            <?php
                // This prints out all hidden setting fields
                settings_fields( self::OPTION_GROUP );   
                do_settings_sections( self::PAGE_NAME );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * 
     */
    public function makePageSelectOptions ( $pageParams ){
        $selectionOptions = array();
        foreach ($pageParams as $params) {
            array_push ( $selectionOptions , array ( 'prev_link' => $params['meta_input']['prev_link'], 'next_link' => $params['meta_input']['next_link'] ) );
        }
        return $selectionOptions;
    }
    
    /**
     * 
     */
    public function makePrintParams( $pageParams ){
        $newPageParams = $pageParams;
        for ( $i = 0; $i < count($pageParams); $i++ ){

            if ( $i == 0 ) {
                $newPageParams[$i]['meta_input']['prev_link'] = "NONE";
            } else {
                $newPageParams[$i]['meta_input']['prev_link'] = $pageParams[ ($i - 1) ]['post_name'];
            }

            if ( $i == count($pageParams) - 1 ) {
                $newPageParams[$i]['meta_input']['next_link'] = "NONE";
            } else {
                $newPageParams[$i]['meta_input']['next_link'] = $pageParams[ ($i + 1) ]['post_name'];
            }
            
        }
        return $newPageParams;
    }
    
    /**
     * 
     */
    public function printPageParams() {
            
        $pageParams = $this->makePrintParams( $this->pageParams );
        $selectOptions = $this->makePageSelectOptions( $pageParams );
        
        $parentPost = get_post( $pageParams[0]['post_parent'] ) ;
        $parentPageName = $parentPost->post_name;
    
        echo "<br />The floowing New Pages will be created:<br /> Customize Previous/Next Link meta data for the auto-nav widget as desired.<br /><table border='1'>";
        echo "<tr>" .
                "<th>Parent Post</th>" .
                "<th>Title</th>" .
                "<th>Page Template</th>" .
                "<th>PAGE SLUG</th>" .
                "<th>Prev Link's Page</th>" .
                "<th>Next Link's Page</th>" .
            "</tr>";
       
        $counter = 0;
        foreach ($pageParams as $params) {
            echo "<tr>";
            
                echo '<input type="hidden" name="'. self::NEW_PAGES .'" value="true">';
                echo '<input type="hidden" name="PARENT_PAGE_ID['.$counter.']" value="'.$params['post_parent'].'">';
            
                echo '<td><input type="text" readonly="readonly" name="PARENT_PAGE_NAME['
                    . $counter .']" value="'
                    . $parentPageName
                    . '"></td>';
                
                echo '<td><input type="text" readonly="readonly" name="PAGE_TITLE['
                    . $counter .']" value="'
                    . $params['post_title']
                    . '"></td>';
                
                ( $params['page_template'] == '') ? $page_template = "Default" : $page_template = $params['page_template'] ;
                echo '<td><input type="text" readonly="readonly" name="PAGE_TEMPLATE['
                    . $counter .']" value="' 
                    . $page_template
                    . '"></td>';
               
                echo '<td><input type="text" readonly="readonly" name="POST_SLUG['
                    . $counter .']" value="'
                    . $params['post_name']
                    . '"></td>';
               
                if ( is_array( $params['meta_input'] ) == true ){
                    $this->makeNavPageSelect( 'prev_link' , $counter , $params['meta_input']['prev_link'], $selectOptions);
                    $this->makeNavPageSelect( 'next_link' , $counter , $params['meta_input']['next_link'], $selectOptions );
                }
            echo "</tr>";
        $counter++;
        }
        echo "</table>";
    }
    
    /**
     * 
     */
    private function createNewPages(){
    
        $PARENT_PAGE_ID     = $_POST['PARENT_PAGE_ID'];
        $PAGE_TITLE         = $_POST['PAGE_TITLE'];
        $PAGE_TEMPLATE      = $_POST['PAGE_TEMPLATE'];
        $POST_SLUG          = $_POST['POST_SLUG'];
        $prev_link          = $_POST['prev_link'];
        $next_link          = $_POST['next_link'];
      
        for ( $i = 0; $i < count( $_POST['POST_SLUG'] ); $i++ ){
               
            $params = array( 
                'post_type'     => 'page',
                'post_status'   => 'draft',
                'post_parent'   => $PARENT_PAGE_ID[$i],
                'post_title'    => $PAGE_TITLE[$i] ,
                'page_template' => rtrim($PAGE_TEMPLATE[$i]),
                'post_content'  => '',
                'post_name'     => $POST_SLUG[$i], 
                'meta_input'    => array ( 
                        'prev_link' => $prev_link[$i], 
                        'next_link' => $next_link[$i] 
                ) 
            );
     
            global $wpdb;
            $params['menu_order'] = $wpdb->get_var("SELECT MAX(menu_order)+1 AS menu_order FROM {$wpdb->posts} WHERE post_type='page'");
            $wpdb->flush();

            wp_insert_post($params);
        }
    }
    
    /**
     * 
     */
    private function makeNavPageSelect( $varName, $counter, $value, $selectOptions ){
        echo "<td>";
            echo '<select " name="' . $varName . '[' . $counter .']">';
            foreach ($selectOptions as $s1 => $s2 ){
                ($s2[$varName] == $value) ? $selected = 'selected' : $selected = '';
                echo '<option value="'.$s2[$varName].'" ' . $selected . '>'. $s2[$varName] .'</option>';
            }
            echo "</select>";    
        echo  "</td>";
    }
    
    /**
     * createPages callback
     */
    public function createPages($volume_data, $page_data) {

        /*
         * Get POST variables.
         */
        $options = $_POST[ self::OPTIONS ];
        /*
         * Get 'Page Info' data: mode, (new?) parent page, template.
         */
        $mode                       = $options["mode"];
        $parent_page_select         = $options["parent_page_select"];  
        $new_parent_page            = $options["new_parent_page"];
        $template_id                = $options["template_id"];
        /*
         * Get 'Title words' data: slug/title for chapter/volume(?)
         */          
        $volume_prefix_for_title    = $options["volume_prefix_for_title"];
        $chapter_prefix_for_title   = $options["chapter_prefix_for_title"];
        $volume_prefix_for_slug     = $options["volume_prefix_for_slug"];
        $chapter_prefix_for_slug    = $options["chapter_prefix_for_slug"];
        /*
         * Get 'title/url padding' data: character(s), size, and turned on/off.
         */
        $volume_padding_checkbox    = $options["volume_padding_checkbox"];
        $volume_padding_ammount     = $options["volume_padding_ammount"];
        $volume_padding_character   = $options["volume_padding_character"];
        $chapter_padding_checkbox   = $options["chapter_padding_checkbox"];
        $chapter_padding_character  = $options["chapter_padding_character"];
        $chapter_padding_ammount    = $options["chapter_padding_ammount"];
        /*
         * Get 'Title Separator' data: character(s) and space before/after.
         */
        $title_sep_1                = $options["title_separator_1"];
        $title_sep_2                = $options["title_separator_2"];
        $title_sep_3                = $options["title_separator_3"];
        $title_sep_1_before         = $options["title_separator_1_before_checkbox"];
        $title_sep_2_before         = $options["title_separator_2_before_checkbox"];
        $title_sep_3_before         = $options["title_separator_3_before_checkbox"];
        $title_sep_1_after          = $options["title_separator_1_after_checkbox"];
        $title_sep_2_after          = $options["title_separator_2_after_checkbox"];
        $title_sep_3_after          = $options["title_separator_3_after_checkbox"];
        /*
         * Get 'Title Separator' data: character(s) and space before/after.
         */        
        $title_sep_1 = $this->convert_separator($title_sep_1, $mode, 'volume');
        $title_sep_2 = $this->convert_separator($title_sep_2, $mode, 'volume');
        $title_sep_3 = $this->convert_separator($title_sep_3, $mode, 'chapter');
        
        /*
         * Get 'Title Separator' data: character(s) and space before/after.
         */
        ( $title_sep_1_before == 1 ) ? $title_sep_1_before = ' ' : $title_sep_1_before = ''; 
        ( $title_sep_2_before == 1 ) ? $title_sep_2_before = ' ' : $title_sep_2_before = '';
        ( $title_sep_3_before == 1 ) ? $title_sep_3_before = ' ' : $title_sep_3_before = '';
        ( $title_sep_1_after  == 1 ) ? $title_sep_1_after  = ' ' : $title_sep_1_after  = '';
        ( $title_sep_2_after  == 1 ) ? $title_sep_2_after  = ' ' : $title_sep_2_after  = '';
        ( $title_sep_3_after  == 1 ) ? $title_sep_3_after  = ' ' : $title_sep_3_after  = '';
        
        if ($mode == 1) {
            $title_sep_1_before = '';
            $title_sep_2_before = '';
            $title_sep_1_after  = '';
            $title_sep_2_after  = '';
        }
        
        $title_sep_1 = $title_sep_1_before . $title_sep_1 . $title_sep_1_after;
        $title_sep_2 = $title_sep_2_before . $title_sep_2 . $title_sep_2_after;
        $title_sep_3 = $title_sep_3_before . $title_sep_3 . $title_sep_3_after;
        
        /*
         * Get 'Title Separator' data: character(s) and space before/after.
         */
        $title_sep_1_slug = ($mode == '2') ? '-' : ''; 
        $title_sep_2_slug = ($mode == '2') ? '-' : '';
        
        $volume_number = intval($volume_data);
        
        $volume_number_string = '';
                
        if ( $mode == '2' ){ // volume mode
            $volume_number_string = str_pad($volume_number, $volume_padding_ammount, $volume_padding_character, STR_PAD_LEFT);
        } else {
            $chapter_prefix_for_title = $chapter_prefix_for_title . ' ';
            $volume_prefix_for_title = '';
            $volume_number_string = '';
            $title_sep_1 = '';
            $title_sep_2 = '';
            $volume_prefix_for_slug = '';
        }
        
        $found = strpos( $page_data, PageCreationParser::PAGE_RANGE_SYMBOL );
        
        if ( $found !== false ) {
        
            $first_last_data = explode( PageCreationParser::PAGE_RANGE_SYMBOL , $page_data );
            $firstChapter = $first_last_data[0];
            $lastChapter = $first_last_data[1];

            for ($x = intval($firstChapter); $x <= intval($lastChapter); $x++) {

                $chapter_number = $x;

                $chapter_number_string = str_pad($chapter_number, $chapter_padding_ammount, $chapter_padding_character, STR_PAD_LEFT);
                
                $title = $volume_prefix_for_title . $title_sep_1 . $volume_number_string . $title_sep_2 . $chapter_prefix_for_title . $title_sep_3 . $chapter_number_string;

                $slug = $volume_prefix_for_slug . $title_sep_1_slug . $volume_number_string . $title_sep_2_slug . $chapter_prefix_for_slug . '-' . $chapter_number_string;
                
                $params = array( 
                    'post_type' => 'page',
                    'post_status' => 'draft',
                    'post_parent' => $parent_page_select,
                    'post_title' => $title,
                    'page_template' => rtrim($template_id),
                    'post_content' => '',
                    'post_name' => $slug);
                    
                array_push ( $this->pageParams, $params );
            }
        } else {
        
            $title = $volume_prefix_for_title . $title_sep_1 . $volume_number_string . $title_sep_2 . $page_data ;

            $slug = $volume_prefix_for_slug . $title_sep_1_slug . $volume_number_string . $title_sep_2_slug . strtolower( $page_data );
        
            $params = array( 
                'post_type' => 'page',
                'post_status' => 'draft',
                'post_parent' => $parent_page_select,
                'post_title' => $title ,
                'page_template' => rtrim($template_id),
                'post_content' => '',
                'post_name' => $slug );
                
            array_push ( $this->pageParams, $params );
        }
    }
}

/**
 * This actually creates the object and 
 */
if( is_admin() ){
    $my_righteous_bulk_page_creator = new righteous_bulk_page_creator();
}
    
?>
