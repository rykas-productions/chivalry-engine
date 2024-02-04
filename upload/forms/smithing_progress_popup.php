<?php
echo"
<div class='modal fade' id='smithing_info' tabindex='-3' role='dialog' aria-labelledby='smithing_info' aria-hidden='true'>
    <div class='modal-dialog modal-lg' role='document'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='addShortcutLabel'>Smithing in Progress</h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                    <span aria-hidden='true'>&times;</span>
                </button>
            </div>
            <div class='modal-body'>";
                $q = $db->query("SELECT * FROM `smelt_inprogress` WHERE `sip_user` = {$userid}");
                while ($r = $db->fetch_row($q))
                {
                    $timeleft = $r['sip_time'] - time();
                    $or = $db->fetch_row($db->query("SELECT `smelt_output`, `smelt_qty_output`, `smelt_time` FROM `smelt_recipes` WHERE `smelt_id` = {$r['sip_recipe']}"));
                    echo "<div class='row'>
                            <div class='col-12 col-sm'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Smelting</b></small>
                                    </div>
                                    <div class='col-12'>
                                        " . shortNumberParse($or['smelt_qty_output']) ." x {$api->SystemItemIDtoName($or['smelt_output'])}
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Time Remaining</b></small>
                                    </div>
                                    <div class='col-12'>
                                        " . TimeUntil_Parse($r['sip_time']) . "
                                    </div>
                                </div>
                            </div>
                    </div>";
                }
            echo"</div>
        </div>
    </div>
</div>";
?>