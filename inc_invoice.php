<?php if ( isset( $_SESSION['invoice'] ) ): ?>
	<?php $invoice = $_SESSION['invoice']; ?>
	<div class="span4 offset4">
		<table class="table table-hover invoice">
		<?php foreach ( $invoice as $label => $value ): ?>
			<tr>
				<td class="title span2"><?php echo $label; ?></td>
				<?php if ( $label == 'Amount' ): ?>
					<td class="price span2">$<?php echo @$value; ?></td>
				<?php else: ?>
					<td class="detail span2"><?php echo @$value; ?></td>
				<?php endif; ?>
			</tr>
		<?php endforeach; ?>

		<?php if ( isset( $checkoutDetails['PAYERID'] ) ): ?>
			<tr class="summary-row">
				<td colspan="2" class="text-right">
					<form action="receipt.php">
						<input type="hidden" name="nonce" value ="<?=$_SESSION['nonce'] ?>" />
						<button class="btn btn-success">Confirm and purchase</button>
					</form>
				</td>
			</tr>

		<?php else: ?>
			<?php unset( $_SESSION['invoice'] ); ?>

		<?php endif; ?>
		</table>
	</div>

<?php else: ?>
	<div class="error">Oops.</div>

<?php endif; ?>