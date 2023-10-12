<?php
require_once('globals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'name':
        name();
        break;
    default:
        home();
        break;
}
function home()
{
    echo "  <div class='card'>
                <div class='card-header'>
                    Item Search
                </div>
                <div class='card-body'>
                    <form>
                    <div class='row'>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Search by Name</b></small>
                                </div>
                                <div class='col-12'>
                                    <input type='hidden' name='action' value='name'>
                                    <input class='form-control' required='1' name='name'>
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Search</b></small>
                                </div>
                                <div class='col-12'>
                                    <button type='submit' class='btn btn-primary'><i class='fas fa-search'></i> Search</button>
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Return</b></small>
                                </div>
                                <div class='col-auto'>
                                    <a href='itemappendix.php' class='btn btn-danger btn-block'>Go Back</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>";
}
function name()
{
    global $db,$api,$h,$userid;
    $_GET['name'] = (isset($_GET['name']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])*$/i", $_GET['name'])) ? $db->escape(strip_tags(stripslashes($_GET['name']))) : '';
    if (empty($_GET['name']))
    {
        alert('danger',"Uh Oh!","Please fill out the form and try again.",true,'search.php');
        die($h->endpage());
    }
    if (((strlen($_GET['name']) > 128) OR (strlen($_GET['name']) < 1)))
    {
        alert('danger',"Uh Oh!","Wtf you doing?",true,'search.php');
        die($h->endpage());
    }
    $name="%{$_GET['name']}%";
    $q = $db->query("/*qc=on*/SELECT *
                     FROM `items`
                     WHERE `itmname` LIKE ('{$name}')");
    echo "<div class='card'>
            <div class='card-header'>
                " . $db->num_rows($q) . " items have been found.
            </div>
            <div class='card-body'>";
    while ($r = $db->fetch_row($q)) 
	{
		$r['itmname'];
		//var_dump($r);
		$pic = returnIcon($r['itmid'],3.5);
		echo "
		<div class='row'>
			<div class='col-12 col-sm-6 col-md-2 col-xl col-xxl-1'>
                <div class='row'>
                    <div class='col-12'>
				        <small><b>Picture</b></small>
                    </div>
                    <div class='col-12'>
				        {$pic}
                    </div>
                </div>
			</div>
            <div class='col-12 col-sm-6 col-md-4 col-xl col-xxl-3'>
                <div class='row'>
                    <div class='col-12'>
				        <small><b>Item</b></small>
                    </div>
                    <div class='col-12'>
				        <a href='iteminfo.php?ID={$r['itmid']}'>{$r['itmname']}</a> " . parseUserID($r['itmid']) . "
                    </div>
                </div>
			</div>
            <div class='col-12 col-sm-6 col-md-auto col-xxl-2'>
                <div class='row'>
                    <div class='col-12'>
				        <small><b>Value</b></small>
                    </div>
                    <div class='col-12'>
				        " . shortNumberParse($r['itmbuyprice']) . " Copper Coins
                    </div>
                </div>
			</div>
            <div class='col-12 col-sm-6 col-md-2 col-xxl-1'>
                <div class='row'>
                    <div class='col-12'>
				        <small><b>Circulating</b></small>
                    </div>
                    <div class='col-12'>
				        " . shortNumberParse(returnTotalItemCount($r['itmid'])) . "
                    </div>
                </div>
			</div>";
	        $towns='';
	        $sq=$db->query("/*qc=on*/SELECT `sitemSHOP` FROM `shopitems` WHERE `sitemITEMID` = {$r['itmid']}");
	        if ($db->num_rows($sq) > 0)
	        {
	            while ($sr=$db->fetch_row($sq))
	            {
	                $shop=$db->fetch_single($db->query("/*qc=on*/SELECT `shopLOCATION` FROM `shops` WHERE `shopID` = {$sr['sitemSHOP']}"));
	                $towns.= "<div class='col-auto'><a href='travel.php?to={$shop}'>{$api->SystemTownIDtoName($shop)}</a></div>";
	            }
	            echo"
                <div class='col-12 col-md-10 col-lg col-xl-12 col-xxl'>
                    <div class='row'>
                        <div class='col-12'>
    				        <small><b>Town(s) Available</b></small>
                        </div>
                        <div class='col-12'>
                            <div class='row'>
                                {$towns}
                            </div>
                        </div>
                    </div>
                </div>";
	        }
	        echo"
		</div>";
	}
    
    echo"</div></div><br />
    <a href='searchitem.php' class='btn btn-primary'>Go Back</a>";
}

$h->endpage();