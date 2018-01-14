<?php
if (!defined('MONO_ON')) {
    exit;
}

class form
{
	function createForm($method='get',$action='')
	{
		echo "<form method='{$method}' action='{$action}'>";
	}
	function endForm()
	{
		echo "</form>";
	}
	function requestCSRF($formid)
	{
		echo request_csrf_html($formid);
	}
	function inputPassword($name,$value='',$required='',$placeholder='')
	{
		echo "<input value='{$value}' placeholder='{$placeholder}' type='password' name='{$name}' {$required} class='form-control'>";
	}
	function inputNumber($name,$value='',$required=0,$placeholder='',$min=-9999999999999999,$max=9999999999999999)
	{
		echo "<input value='{$value}' placeholder='{$placeholder}' type='number' name='{$name}' min='{$min}' max='{$max}' {$required} class='form-control'>";
	}
	function inputText($name,$value='',$required=0,$placeholder='')
	{
		echo "<input value='{$value}' placeholder='{$placeholder}' type='text' name='{$name}' {$required} class='form-control'>";
	}
	function inputBigText($name,$value='',$required=0,$placeholder='')
	{
		echo "<textarea placeholder='{$placeholder}' name='{$name}' {$required} class='form-control'>{$value}</textarea>";
	}
	function buttonSubmit($value='Submit',$class='primary')
	{
		echo "<input type='submit' value='{$value}' class='btn btn-{$class}'>";
	}
}