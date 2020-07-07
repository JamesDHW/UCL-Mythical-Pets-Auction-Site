<?php
require_once '../private/init.php';
session_start();
?>
<!DOCTYPE html>
<html>
<?php require TEMPLATE_PATH . "/html_head.php"; ?>
	<body>
		<?php
			require TEMPLATE_PATH . '/nav.php';
		?>
		<div class="container" style="background-color: white">
			<div class="row m-3">
				<h1 align="left">Most Viewed Items</h1>
			</div>
			<div class="row m-3">
				<?php require PUBLIC_PATH . "/forms/most_viewed.php"; ?>
			</div>
			<div class="row m-3">
				<h1 align="left">Hottest Items</h1>
			</div>
			<div class="row m-3">
				<?php require PUBLIC_PATH . "/forms/hottest_auctions.php"; ?>
			</div>
		</div>
	</body>
</html>

<?php
#notification if user already exists on registration
if(isset($_SESSION['userExists'])){ ?>
	<script>
		$('#regModal').modal('show');
		$('#reg-modal-title').text("ERROR: User Already Exists!");
	</script>;
	<style>#reg-modal-content{background-color:#d9534f !important;}</style>
	<?php
	unset($_SESSION['userExists']);
 }
#notification if incorrect credidentials on sign in
if ($_SESSION['badCredentials']){ ?>
	<script>
		$('#signInModal').modal('show');
		$('#sign-in-modal-title').text("ERROR: Incorrect Credentials, please try again.");
	</script>;
	<style>#sign-in-modal-content{background-color:#d9534f !important;}</style>
	<?php
	unset($_SESSION['badCredentials']);
}
if (isset($_SESSION['registration'])){ ?>
	<script>
		$('#signInModal').modal('show');
		$('#sign-in-modal-title').text("SUCCESS: Please now sign in");
	</script>;
	<style>#sign-in-modal-content{background-color:#5cb85c !important}</style>
	<?php
	unset($_SESSION['registration']);
}
if (isset($_SESSION['registrationError'])){ ?>
	<script>
		$('#regModal').modal('show');
		$('#reg-modal-title').text("ERROR: Something went wrong! Try again!");
	</script>;
	<style>#reg-modal-content{background-color:#d9534f !important}</style>
	<?php
	unset($_SESSION['registrationError']);
} ?>
</script>
