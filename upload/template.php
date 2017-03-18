<?php

function run_template($template_array, $template)
{
    $tmp = 'genesis';

    $text = file_get_contents( 'template/' . $tmp .'/' . $template . '.tpl' );
    foreach( $template_array as $key => $value )
    {
        $text = str_replace( '{' . $key . '}', $value, $text );
    }
     echo $text;
}
?>