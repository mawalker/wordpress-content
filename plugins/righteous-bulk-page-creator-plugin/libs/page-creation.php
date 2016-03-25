<?php

class PageCreationParser {


    const VOLUME_SEPARATOR          = ';';
    const PAGE_SEPARATOR            = ',';
    const PAGE_RANGE_SYMBOL         = '->';
    const VOLUME_START_SYMBOL       = ':';
     
    private static function _splitVolumes ( $stringToParse ) {
        return explode( self::VOLUME_SEPARATOR , $stringToParse );
    }
    
    public static function parseString( $stringToParse ) {
    
        $volumes = self::_splitVolumes ( $stringToParse );
        
        //echo "<br /><br /> volumes______: ". $volumes . " " . count($volumes) . " <br /><br /> ";
    
        $rValue = array();
        
        foreach ($volumes as $index => $value) {
            
            $volume_data        = explode( self::VOLUME_START_SYMBOL , $value , 2 );
            if ( count($volume_data) >= 2 ){
                $volume_name    = $volume_data[0];
                $volume_pages   = $volume_data[1];
            } else {
                $volume_name    = '';
                $volume_pages   = $volume_data[0];
            }
            //echo "volume_name : " .$volume_name . " volume_pages : " . $volume_pages; 
                
            $pages = self::_getPages( $volume_pages );
            
            $volume_data_parsed = array (
                $volume_name,
                $pages
            );
            array_push ( $rValue , $volume_data_parsed ); 
        }
        return $rValue;
    }
    
    private static function _getPages ( $stringToParse ) {        
        $pageData = explode( self::PAGE_SEPARATOR , $stringToParse );       
        return $pageData;
    }
    
    public static function generatePagesWithFunct ( $volumeData, &$object, $pageFunction, $pringPageParams ) {
    
       // echo "<br /><br />generatePagesWithFunct  ";
     //   echo "<br />New Pages:<br /><table border='1'><tr><th>Title</th><th>Parent link+Slug</th></tr>";
        foreach ($volumeData as $index => $volume) {
                
            $volume_name = $volume[0];
                
            //echo "<tr>";
                //    echo "volume name: " . $volume_name;
              
                
            foreach ($volume[1] as $index2 => $pages) {
            
                if ( $pageFunction != null ) {
                    call_user_func ( array ($object, $pageFunction), $volume_name, $pages );
                } else {
                    echo ' was passed null please check your input string for "pages" to create, if that appears to be correct, then please report this error to the developer. '; 
                }
                
            }
//             echo "<tr />";     
        }
        
        call_user_func ( array ($object, $pringPageParams) );
    //    echo "</table><br /><br />";
    }
       
}

?>
