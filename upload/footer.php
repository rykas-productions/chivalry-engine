</div>
        </div>
        <!-- /.row -->

    </div>
    <!-- /.container -->

    <!-- jQuery Version -->
	<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
			
	<!-- Other JavaScript -->
	<script src="js/register.js"></script>
	<script src="js/game.js"></script>
	<script src='https://www.google.com/recaptcha/api.js'></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
</body>
	<footer>
		<p>
			<?php 
					echo "<hr />
					{$lang['MENU_TIN']}  
						" . date('F j, Y') . " " . date('g:i:s a') . "<br />
					{$lang['MENU_OUT']}";
					?>
					&copy; <?php echo date("Y");
			?>
		</p>
	</footer>
</html>
<?php
die();