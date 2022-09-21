<h1>Categories</h1>

<?php foreach ($categories as $category): ?>

<h3><?php echo $category->name; ?></h3>
<img src="<?php echo $category->thumb ?>" alt="">

<?php endforeach; ?>
