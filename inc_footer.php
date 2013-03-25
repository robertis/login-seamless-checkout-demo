		</div> <!-- .row-fluid -->
	</div> <!-- .container.guts -->

	<footer class="footer" role="contentinfo">
		<div class="container" role="navigation">
			<a href="<?=BASE_URL ?>">storefront</a> |
			<a href="<?=BASE_URL ?>cart.php">shopping cart</a> |
			<a href="<?=BASE_URL ?>admin/" class="tiny">admin</a>
		</div>
	</footer>

	<?php
		if ( isset($_SESSION['debug_mode']) && isset($_SESSION['http_log'] ) ) {

			$html = '';

			foreach ( $_SESSION['http_log'] as $entry ) {
				$html .= '<table>';

				foreach ( $entry as $key => $value ) {
					if ( is_array($value) ) {
						$value = implode( ',', $value );
					}
					$html .= '<tr><td>' . $key . '</td><td>' . $value . '</td></tr>';
				}

				$html .= '</table>';
			}

			unset( $_SESSION['http_log'] );

			echo $html;
		}
	?>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="<?=BASE_URL ?>js/bootstrap.min.js"></script>
	<script src="<?=BASE_URL ?>js/demo.js"></script>
</body>
</html>
