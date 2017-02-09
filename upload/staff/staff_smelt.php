<?php
require('sglobals.php');
echo "<h3>{$lang['STAFF_SMELT_HOME']}</h3><hr />";
if (!isset($_GET['action']))
{
    $_GET['action'] = 'add';
}
switch ($_GET['action'])
{
case 'add':
    add();
    break;
}
function add()
{
	global $db,$api,$lang,$h,$userid;
	if (isset($_POST['smelted_item']))
	{
		
	}
	else
	{
		echo "<form id='craft' method='post'>
			<table class='table table-bordered'>
				<tr>
					<th>
						{$lang['STAFF_SMELT_ADD_TH']}
					</th>
					<th>
						{$lang['STAFF_SMELT_ADD_TH1']}
					</th>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_SMELT_ADD_TH2']}
					</th>
					<td>
						" . item_dropdown("smelted_item") . "
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_SMELT_ADD_TH5']}
					</th>
					<td>
						<input type='number' class='form-control' required='1' name='smelt_item_qty' value='1' min='1'>
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_SMELT_ADD_TH3']}
					</th>
					<td>
						<select class='form-control' name='timetocomplete'>
							<option value='0'>{$lang['STAFF_SMELT_ADD_SELECT1']}</option>
							<option value='5'>5 {$lang['STAFF_SMELT_ADD_SELECT2']}</option>
							<option value='30'>30 {$lang['STAFF_SMELT_ADD_SELECT2']}</option>
							<option value='60'>1 {$lang['STAFF_SMELT_ADD_SELECT3']}</option>
							<option value='300'>5 {$lang['STAFF_SMELT_ADD_SELECT3']}</option>
							<option value='600'>10 {$lang['STAFF_SMELT_ADD_SELECT3']}</option>
							<option value='3600'>1 {$lang['STAFF_SMELT_ADD_SELECT4']}</option>
							<option value='86400'>1 {$lang['STAFF_SMELT_ADD_SELECT5']}</option>
						</select>
					</td>
				</tr>
					<tr>
						<th>
							{$lang['STAFF_SMELT_ADD_TH4']}
						</th>
						<td>
							<div id='input1' class='clonedInput'>" . item_dropdown("required_item") . "<br /></div>
						</td>
					</tr>
				<tr>
				</div>
					<tr>
						<th>
							{$lang['STAFF_SMELT_ADD_TH6']}
						</th>
						<td>
							<input type='number' class='form-control' required='1' name='required_item_qty' value='1' min='1'>
						</td>
					</tr>
				</tr>
				<tr>
					<td>
						<input type='button' class='btn btn-success' id='btnAdd' value='{$lang['STAFF_SMELT_ADD_BTN']}' />
					</td>
					<td>
						<input type='button' class='btn btn-danger' id='btnDel' value='{$lang['STAFF_SMELT_ADD_BTN2']}' />
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-default' value='{$lang['STAFF_SMELT_ADD_BTN3']}' />
					</td>
				</tr>
			</table>
		</form>";
	}
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#btnAdd').click(function() {
            var num     = $('.clonedInput').length;
            var newNum  = new Number(num + 1);
        
            var newElem = $('#input' + num).clone().attr('id', 'input' + newNum);

            newElem.children(':first').attr('id', 'required_item' + newNum).attr('name', 'required_item' + newNum);
            

            $('#input' + num).after(newElem);
            $('#btnDel').attr('disabled','');

            if (newNum == 5)
                $('#btnAdd').attr('disabled','disabled');
        });
        $('#btnDel').click(function()
		{
            var num = $('.clonedInput').length;

            $('#input' + num).remove();
            $('#btnAdd').attr('disabled','');

            if (num-1 == 1)
                $('#btnDel').attr('disabled','disabled');
        });
        $('#btnDel').attr('disabled','disabled');
    });
</script>
<?php
$h->endpage();