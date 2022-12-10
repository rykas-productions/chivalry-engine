<?php
require('globals.php');
echo "<h3>CID Milestones</h3><hr />
The following is a list of milestones that players have accomplished inside of Chivalry is Dead. This
 won't be constantly updated sadly. It'll only be updated when someone accomplishes something note
 worthy.
<div class='row'>
    <div class='col-12 col-md-6 col-xxl-4 col-xxxl-3'>
        <div class='card'>
            <div class='card-header'>
                2022 Halloween Pumpkin Chuck
            </div>
            <div class='card-body'>
                <a href='profile.php?user=143'>" . parseUsername(143) . " [143]</a>
            </div>
        </div>
    </div>
    <div class='col-12 col-md-6 col-xxl-4 col-xxxl-3'>
        <div class='card'>
            <div class='card-header'>
                2017 Thanksgiving Trivia Winner
            </div>
            <div class='card-body'>
                <a href='profile.php?user=143'>" . parseUsername(143) . " [143]</a>
            </div>
        </div>
    </div>
    <div class='col-12 col-md-6 col-xxl-4 col-xxxl-3'>
        <div class='card'>
            <div class='card-header'>
                2018 Thanksgiving Trivia Winner
            </div>
            <div class='card-body'>
                <a href='profile.php?user=220'>" . parseUsername(220) . " [220]</a>
            </div>
        </div>
    </div>
    <div class='col-12 col-md-6 col-xxl-4 col-xxxl-3'>
        <div class='card'>
            <div class='card-header'>
                Top Turkey Hunters (2022)
            </div>
            <div class='card-body'>
                <div class='row'>";
                    $turkq = $db->query("SELECT * FROM `user_pref` WHERE `preference` = '2022turkeyKills' ORDER BY `value` * 1 DESC LIMIT 5");
                    while ($r = $db->fetch_row($turkq))
                    {
                        echo "<div class='col-12 col-sm-6 col-lg-4 col-xl-6'>
                        <div class='row'>
                            <div class='col-12'>
                                <a href='profile.php?user={$r['userid']}'>" . parseUsername($r['userid']) . " [{$r['userid']}]</a>
                            </div>
                            <div class='col-12 text-muted small'>
                                " . shortNumberParse($r['value']) . " kills
                            </div>
                        </div>
                    </div>";
                    }
            echo"</div></div>
        </div>
    </div>
    <div class='col-12 col-md-6 col-xxl-4 col-xxxl-3'>
        <div class='card'>
            <div class='card-header'>
                2018 Referral Contest Winner
            </div>
            <div class='card-body'>
                <a href='profile.php?user=161'>" . parseUsername(161) . " [161]</a>
            </div>
        </div>
    </div>
    <div class='col-12 col-md-6 col-xxl-4 col-xxxl-3'>
        <div class='card'>
            <div class='card-header'>
                2019 Big Bang Event Victims
            </div>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12 col-sm-6'>
                        <a href='profile.php?user=184'>" . parseUsername(184) . " [184]</a>
                    </div>
                    <div class='col-12 col-sm-6'>
                        <a href='profile.php?user=102'>" . parseUsername(102) . " [102]</a></a>
                    </div>
                    <div class='col-12 col-sm-6'>
                        <a href='profile.php?user=161'>" . parseUsername(161) . " [161]</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class='col-12 col-md-6 col-xxl-4 col-xxxl-3'>
        <div class='card'>
            <div class='card-header'>
                2020 Big Bang Event Victims
            </div>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12 col-sm-6'>
                        <a href='profile.php?user=406'>" . parseUsername(406) . " [406]</a>
                    </div>
                    <div class='col-12 col-sm-6'>
                        <a href='profile.php?user=433'>" . parseUsername(433) . " [433]</a></a>
                    </div>
                    <div class='col-12 col-sm-6 col-lg'>
                        <a href='profile.php?user=181'>" . parseUsername(181) . " [181]</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class='col-12 col-md-6 col-lg col-xl-6 col-xxl'>
        <div class='card'>
            <div class='card-header'>
                Mastery Rank
            </div>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12 col-sm-6 col-lg-4 col-xl-6'>
                        <div class='row'>
                            <div class='col-12'>
                                <a href='profile.php?user=161'>" . parseUsername(161) . " [161]</a>
                            </div>
                            <div class='col-12 text-muted small'>
                                1st Mastery Rank I
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-6 col-lg-4 col-xl-6'>
                        <div class='row'>
                            <div class='col-12'>
                                <a href='profile.php?user=161'>" . parseUsername(161) . " [161]</a>
                            </div>
                            <div class='col-12 text-muted small'>
                                1st Mastery Rank II
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-6 col-lg-4 col-xl-6'>
                        <div class='row'>
                            <div class='col-12'>
                                <a href='profile.php?user=161'>" . parseUsername(161) . " [161]</a>
                            </div>
                            <div class='col-12 text-muted small'>
                                1st Mastery Rank III
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-6 col-lg col-xl-6'>
                        <div class='row'>
                            <div class='col-12'>
                                <a href='profile.php?user=161'>" . parseUsername(161) . " [161]</a>
                            </div>
                            <div class='col-12 text-muted small'>
                                1st Mastery Rank IV
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-6 col-lg col-xl-6'>
                        <div class='row'>
                            <div class='col-12'>
                                <a href='profile.php?user=161'>" . parseUsername(161) . " [161]</a>
                            </div>
                            <div class='col-12 text-muted small'>
                                1st Mastery Rank V
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>";
 
 $h->endpage();