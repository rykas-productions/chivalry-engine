<?php
/*	File:		global_func_forms.php
	Created: 	Jul 15, 2021; 10:27:57 PM
	Info: 		
	Author:		TheMasterGeneral
	Website: 	https://chivalryisdeadgame.com/
*/
function createForm($method, $action, $inputsArray, $submitButtonName)
{
    echo "<form method='{$method}' action='{$action}'>";
    foreach ($inputsArray as $input)
    {
        if (!isset($input[3]))
            $input[3]='';
            echo "<div class='form-group'>
		<label for='{$input[1]}'>{$input[2]}</label>
		<input type='{$input[0]}' name='{$input[1]}' class='form-control' placeholder='{$input[2]}' value='{$input[3]}'>
		</div>";
    }
    echo "
	<input type='hidden' name='formSubmitValue' value='1'>
	<button class='btn btn-primary' type='submit'>{$submitButtonName}</button>
	</form>";
}
function createPostForm($action, $inputsArray, $submitButtonName)
{
    createForm('post', $action, $inputsArray, $submitButtonName);
}
function createGetForm($action, $inputsArray, $submitButtonName)
{
    createForm('get', $action, $inputsArray, $submitButtonName);
}