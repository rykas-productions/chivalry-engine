<?php
require('globals.php');
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
    alert('danger',"Uh Oh!","You cannot use the smeltery while in the infirmary or dungeon.",true,'index.php');
    die($h->endpage());
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'smelt':
        smelt();
        break;
    default:
        home();
        break;
}

function home()
{
    global $db, $api, $userid, $h;
    $q = $db->query("
        SELECT au.*, at.*
        FROM animals_users au
        INNER JOIN animal_types at ON au.au_animal_type = at.at_id
        WHERE au.au_userid = {$userid}
    ");
    echo "<div class='card'>
            <div class='card-header'>
                Your Animals
            </div>
            <div class='card-body'>";
            while ($r = $db->fetch_row($q))
            {
                $modalId = "animalModal_" . $r['au_id'];
                $animalName = (!empty($r['au_name'])) ? $r['au_name'] . " ({$r['at_name']})": $r['at_name'];
                echo "  <div class='row'>
                            <div class='col-12 col-md-3'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Animal</b></small>
                                    </div>
                                    <div class='col-12'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <a href='#' data-toggle='modal' data-target='#{$modalId}'>{$animalName}</a>
                                            </div>
                                            <div class='col-12'>
                                                <small>{$r['au_gender']}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-md-9 col-xxxl'>
                                <div class='row'>
                                    <div class='col-12 col-md-6'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <small><b>Health</b></small>
                                            </div>
                                            <div class='col-12 col-xxxl'>
                                                " . scaledColorProgressBar($r['au_health']) . "
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-md-6'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <small><b>Weight</b></small>
                                            </div>
                                            <div class='col-12 col-xxxl'>
                                                " . scaledColorProgressBar($r['au_weight'], $r['at_avg_weight']*0.5, $r['at_avg_weight']*1.5) . "
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-md-6'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <small><b>Age</b></small>
                                            </div>
                                            <div class='col-12 col-xxxl'>
                                                " . scaledColorProgressBar($r['au_weight'], 0, $r['at_max_age']) . "
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-md-6'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <small><b>Next Produce Time</b></small>
                                            </div>
                                            <div class='col-12 col-xxxl'>
                                                " . TimeUntil_Parse($r['au_produce_time']) . "
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class='modal fade' id='{$modalId}' tabindex='-1' role='dialog' aria-labelledby='{$modalId}Label' aria-hidden='true'>
                            <div class='modal-dialog modal-lg modal-dialog-scrollable' role='document'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h5 class='modal-title' id='{$modalId}Label'>Animal Details: {$animalName}</h5>
                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                            <span aria-hidden='true'>&times;</span>
                                        </button>
                                    </div>
                                    <div class='modal-body'>
                                        <p><strong>Name:</strong> {$r['au_name']}</p>
                                        <p><strong>Type:</strong> {$r['at_name']}</p>
                                        <p><strong>Gender:</strong> {$r['au_gender']}</p>
                                        <p><strong>Age:</strong> {$r['au_age']} / {$r['at_max_age']} days</p>
                                        <p><strong>Weight:</strong> {$r['au_weight']} / Avg: {$r['at_avg_weight']}</p>
                                        <p><strong>Health:</strong> {$r['au_health']}</p>
                                        <p><strong>Produce Time:</strong> {$r['au_produce_time']}</p>
                                    </div>
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>";
            }
                
            echo "</div>
        </div>";
            $h->endpage();
}

//Husbandry functions

function creditAnimal($userid, $animalID, $gender = null, $weight = null)
{
    
}