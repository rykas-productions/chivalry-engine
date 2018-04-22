<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 3/9/2018
 * Time: 9:10 PM
 */
?>
<div class="modal fade" id="addShortcut" tabindex="-2" role="dialog" aria-labelledby="addShortcut" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addShortcutLabel">Adding Shortcut</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method='post'>
                    Shortcut Link (Example: explore.php)
                    <input type='text' name='sc_shortcut' class='form-control' required='1' placeholder='explore.php'>
                    Shortcut Name
                    <input type='text' name='sc_name' class='form-control' placeholder='Explore' required='1'>
                    <input type='submit' value='Add Shortcut' class='btn btn-primary'>
                </form>
            </div>
        </div>
    </div>
</div>