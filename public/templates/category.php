<?php
/**
 * @var string $category
 * @var Image[] $images
 */
?>

<header>
    <a href="./">Back</a>
    <span>|</span>
    <h1>Category: <?php echo $category; ?></h1>
</header>

<div class="gallery-grid">

    <?php foreach ($images as $image): ?>

        <a href="<?php echo $image->uri; ?>" target="_blank" class="gallery-item">
            <img src="<?php echo $image->thumb; ?>" alt="<?php echo $image->name; ?>" loading="lazy">
            <h3>
                <?php echo $image->name; ?>
                <small>[<?php echo $image->width; ?>x<?php echo $image->height; ?>]</small>
            </h3>
        </a>

    <?php endforeach; ?>

</div>
