<?php
if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD']))
{
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST')
    {
        // Ignore a GET request
        header('HTTP/1.1 400 Bad Request');
        exit;
    }
}
require_once('../../global_func.php');
if (!is_ajax())
{
    header('HTTP/1.1 400 Bad Request');
    exit;
}
require_once('../../globals_nonauth.php');
if ($_POST['class'] == 'Warrior')
{
	echo "
	<script>document.getElementById('strength').value = '1100'</script>
	<script>document.getElementById('agility').value = '1000'</script>
	<script>document.getElementById('guard').value = '900'</script>
	";
}
elseif ($_POST['class'] == 'Rogue')
{
	echo "
	<script>document.getElementById('strength').value = '900'</script>
	<script>document.getElementById('agility').value = '1100'</script>
	<script>document.getElementById('guard').value = '1000'</script>
	";
}
else
{
	echo "
	<script>document.getElementById('strength').value = '1000'</script>
	<script>document.getElementById('agility').value = '900'</script>
	<script>document.getElementById('guard').value = '1100'</script>
	";
}