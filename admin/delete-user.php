<?php
	$title = 'Delete Account';

	require '../inc_header.php';
?>

	<?php
		try {
			$id = $_GET[ 'id' ];

			if ( strlen($id) <= 0 ) {
				throw new Exception( "ID not provided" );
			}

			echo '<p><b>Deleting user</b> "' . $id . '"</p>';

			echo '<p>Verifying user is in the database...</p>';

			$sql = $pdo->prepare( 'SELECT * from users where id=:id' );
			$sql->bindParam( ':id', $id );
			$sql->execute();

			$result = $sql->fetch( PDO::FETCH_ASSOC );

			if ( count($result) == 0 ) {
				throw new Exception( 'User not found.' );
			}

			echo '<p>verified.</p>';

			echo '<p>Deleting user...</p>';

			$sql = $pdo->prepare( 'DELETE FROM users WHERE id=:id' );
			$sql->bindParam( ':id', $id );
			$sql->execute();

			echo '<p>...deleted.</p>';

			echo '<p>Successfully deleted user "' . $id . '"</p>';

		} catch( Exception $e ) {
			echo '<p>PHP Exception while deleting user:<p>';
			echo '<p>' . $e->getMessage() . '</p>';
		}
	?>

<?php require '../inc_footer.php'; ?>