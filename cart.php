<?php
	$title = 'Shopping cart';

	require 'inc_header.php';
?>

	<?php
		manage_cart_items();

		$cartItems = $_SESSION['cartItems'];

		$costTotal = 0.00;
	?>

	<div class="span8 offset2">
	<?php if ( count( $cartItems ) ): ?>
		<table class="table table-hover cart">
			<?php foreach ( $cartItems as $itm ): ?>
				<?php
					$item = $storeItems[$itm];
					$costTotal = $costTotal + $item['price'];
				?>
				<tr class="">
					<td class="image span1"><?php echo $item['image']; ?></td>
					<td class="title span2"><?php echo $item['title']; ?></td>
					<td class="price span3">$<?php echo $item['price']; ?></td>
					<td class="action span2"><a href="?delete-item=<?php echo $itm; ?>" class="delete">delete</a></td>
				</tr>
			<?php endforeach; ?>
			<tr class="summary-row">
				<td class="total price" colspan="3">
					<span class="tiny">total:</span> $<?php echo number_format( $costTotal, 2 ); ?>
				</td>
				<td class="action">
					<p><a href="<?=BASE_URL ?>">Continue shopping</a></p>
					<!-- <p><a href="?delete-cart=true" class="delete tiny">clear cart</a></p> -->
				</td>
			</tr>
			<tr class="summary-row">
				<td colspan="3" class="text-right">
					<?php if ( is_logged_in() ): ?>
						<form action="<?=BASE_URL ?>checkout.php">
							<input type="hidden" name="nonce" value ="<?=$_SESSION['nonce'] ?>" />
							<button class="btn btn-success">Proceed to checkout</button>
						</form>
					<?php else: ?>
						<button class="login-trigger btn btn-success" data-toggle="modal" data-target="#myModal">Log in and checkout</button>
					<?php endif; ?>
				</td>
				<td>
					<?php if ( !isset($_SESSION['email']) || !does_user_have_paypal_id( $_SESSION['email'] ) ): ?>
						<a href="<?=BASE_URL ?>checkout.php?paymentType=paypal&nonce=<?= $_SESSION['nonce'] ?>" class="tiny"><img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="left" style="margin-right:7px;"></a>
					<?php endif; ?>
				</td>
			</tr>
		</table>

	<?php else: ?>
		<h2>Empty cart</h2>
		<p class="plea">Take a moment to <a href="<?=BASE_URL ?>">add some items your cart</a> and some joy to your heart.</p>

	<?php endif; ?>

	</div>

<?php require 'inc_footer.php'; ?>
