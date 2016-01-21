<?php 
/*
Eksempel på brug
*/

if(class_exists('WACC')){
    
    $new = new WACC(array(

        // Kræves, typen af post
        'post_type' => 'post',


        // Ændr standarder eller sæt til false for at fjerne
        'defaults' => array(
            'title' => 'Navn',
        ),

        // definer nye rækker
        'columns' => array(

            // Position 
            'position' => array(
                'slug' => 'position',
                'output' => 'Position',
                'data_type' => 'post_meta',
                'meta_key' => 'medlem_position',
            ),
        ),
    ));
}