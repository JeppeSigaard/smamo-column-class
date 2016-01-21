<?php 

/*
    Plugin name: WACC
    Plugin URI: 
    Description: WordPress Admin Column Class, som gør det nemt at skrive egne kolonner til administratorpanelet
    Version: 1.0
    Author: SmartMonkey
    Author URI: http://smartmonkey.dk
    
*/


class WACC{
    
    private $settings;
    
    function __construct($var){
        
        
        $this->settings = $var;
        $post_type = $this->settings['post_type'];
        
        /* ------------------------  */
        // Tilføj Kollonnernes header
        $filter = 'manage_edit-'.$post_type.'_columns';
        add_filter( $filter, function ($columns) {
        
            // Default felter
            $new_columns = array(
                'cb' => '<input type="checkbox" />',
                'title' => __('Title'),
                'author' => __('Author'),
                'date' => __('Date'),
            );
            
            
            
            // Overskriv eller fjern default
            if(isset($this->settings['defaults'])){
                foreach($this->settings['defaults'] as $key => $val){
                    if ($val !== false){
                        if(array_key_exists($key, $new_columns)){
                            $new_columns[$key] = $val;
                        }
                    }
                    else{unset($new_columns[$key]);}
                }
            }
            
            // Tilføj nye headers
            foreach($this->settings['columns'] as $field){
                $key = $field['slug'];
                $new_columns[$key] = $field['output'];
            }
            
            
            // Returner kolonner
            return $new_columns;
            
        });
        
        /* ------------------------  */
        // Tilføj indhold til klolonnen
        $filter = 'manage_'.$post_type.'_posts_custom_column';
        add_action($filter,function($column_name){
            
            global $post;

            foreach($this->settings['columns'] as $field){
                if($field['slug'] === $column_name){

                    // Post_meta
                    if ($field['data_type'] === 'post_meta'){
                    
                        if(isset($field['link']) && $field['link'] === 'post'){
                            echo edit_post_link( get_post_meta($post->ID,$field['meta_key'],true), '<b>', '</b>', $post->ID );
                        }

                        else if(isset($field['field_type']) && $field['field_type'] === 'options'){
                            echo $field['options'][get_post_meta($post->ID,$field['meta_key'],true)];   
                        }

                        else{
                            echo get_post_meta($post->ID,$field['meta_key'],true);
                        }

                    }
                    
                    // Tilføj andet?
                }
            }    
            
        });
        
        /* ------------------------  */
        // Tilføj sortering
        $filter = 'manage_edit-'.$post_type.'_sortable_columns';
        add_action($filter,function($columns){
            
            $custom = array();

            foreach($this->settings['columns'] as $field){
                $key = $field['slug'];
                $custom[$key] = $field['slug'];

            }

            return wp_parse_args($custom, $columns);
            
        });
        
        /* ------------------------  */
        // Tilføj query var
        add_action('request',function($vars){
            

            foreach($this->settings['columns'] as $field){
               if ( isset( $vars['orderby'] ) && $field['slug'] == $vars['orderby'] ) {
                    $order_by = (isset($field['order_by'])) ? $field['order_by'] : 'meta_value';

                    $merge_array = array(
                        'meta_key' => $field['meta_key'],
                        'orderby' => $order_by
                    );

                    if(isset($field['meta_type'])){
                        $merge_array['meta_type'] = $field['meta_type'];

                    }

                    $vars = array_merge( $vars, $merge_array);
                } 
            }
            return $vars;
            
        });
    }
};