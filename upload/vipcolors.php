<?php
require('globals.php');
echo "<font size=32><b><u>VIP Colors on Other Themes</u></b><br />
        Default - <span class='text-danger'>{$ir['username']}</span><br />
        Option 1 - <span class='text-success'>{$ir['username']}</span><br />
        Option 2 - <span class='text-primary'>{$ir['username']}</span><br />
        Option 3 - <span class='text-secondary'>{$ir['username']}</span><br />
        Option 4 - <span class='text-warning'>{$ir['username']}</span><br />
        Option 5 - <span class='text-info'>{$ir['username']}</span>
        <hr /></font>";
$h->endpage();