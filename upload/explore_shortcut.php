<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 3/9/2018
 * Time: 9:10 PM
 */
?>
<div class="modal fade" id="addShortcut" tabindex="-2" role="dialog" aria-labelledby="addShortcut" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addShortcutLabel">Adding Shortcut</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method='post'>
                	<div class='row'>
                		<div class='col-12 col-lg-5'>
                			Shortcut Link (Example: explore.php)
                			<br />
                		</div>
                		<div class='col-12 col-lg-7'>
                			<input type='text' name='sc_shortcut' class='form-control' required='1' placeholder='explore.php'>
                			<br />
                		</div>
                		<div class='col-12 col-lg-5'>
                			Shortcut Name
                			<br />
                		</div>
                		<div class='col-12 col-lg-7'>
                			<input type='text' name='sc_name' class='form-control' placeholder='Explore' required='1'>
                			<br />
                		</div>
                		<div class='col-12'>
                			<input type='submit' value='Add Shortcut' class='btn btn-primary btn-block'>
                			<br />
                		</div>
                	</div>
                </form>
            </div>
        </div>
    </div>
</div>