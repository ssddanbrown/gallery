<?php
/**
 * @var Category[] $categories
 */
?>

<div class="gallery-grid">

    <?php foreach ($categories as $category): ?>

        <a href="./<?php echo $category->name; ?>" class="gallery-item">
            <img src="<?php echo $category->thumb ?>" alt="<?php echo $category->name; ?>" loading="lazy">
            <h3><?php echo $category->name; ?></h3>
        </a>

    <?php endforeach; ?>

</div>
