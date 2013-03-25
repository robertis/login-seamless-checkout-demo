<?php require 'inc_header.php'; ?>

	<ul class="storefront homepage">
		<?php foreach ( $storeItems as $key => $item ): ?>
		<li class="item">
			<a href="cart.php?item=<?=$key ?>" class="item-link">
			<figure>
				<div class="image"><?php echo $item['image']; ?></div>
				<figurecaption>
					<h4 class="title"><?php echo $item['title']; ?></h4>
					<p class="price">$<?php echo $item['price']; ?></p>
				</figurecaption>
			</figure>
			</a>
		</li>
		<?php endforeach; ?>
	</ul>

<?php require 'inc_footer.php'; ?>
