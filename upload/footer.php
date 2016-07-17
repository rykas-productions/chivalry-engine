</div>
        </div>
        <!-- /.row -->

    </div>
    <!-- /.container -->

    <!-- jQuery Version 1.11.1 -->
    <script src="js/jquery-1.12.0.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
	<?php
	//<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	//<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	?>
	<!-- Other JavaScript -->
	<script src="js/register.js"></script>
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