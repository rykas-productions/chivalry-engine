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
require_once('global_func.php');
if (!is_ajax())
{
    header('HTTP/1.1 400 Bad Request');
    exit;
}

require_once('globals_nonauth.php');
$class=$_POST['team'];
if ($class == 'Warrior')
{
	echo "<div class='alert alert-info'>
  <strong>Warrior Class!</strong> A normal warrior has been 
  trained to use brute strength to win combat situations. Because 
  of this, they learn strength a lot quicker, but do not know how 
  to guard as well. They begin the game with more strength, but 
  less guard than others.</div>";
}
elseif ($class == 'Rogue')
{
	echo "<div class='alert alert-info'>
  <strong>Rogue Class!</strong> A rogue fighter has an 
  easier time training agility, however, at the cost of 
  not knowing how to train strength as well. They start 
  the game with more agility, and less strength.
</div>";
}
elseif ($class == 'Defender')
{
	echo "<div class='alert alert-info'>
  <strong>Defender Class!</strong> A defending fighter has 
  trained extensively in the art of blocking, thus making them 
  a quick learner of the Guard skill, however, at the cost of 
  being a slow learner at Agility. They start the game with more guard, 
  but less agility.
</div>";
}
else
{
	echo "<div class='alert alert-danger'>
  <strong>Error!</strong> We need you to select a class! Please do so!
</div>";
}
