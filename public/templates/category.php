<?php
/**
 * @var string $category
 * @var Image[] $images
 */
?>

<?php include "shared/header.php" ?>

<h1>Category: <?php echo $category; ?></h1>

<?php foreach ($images as $image): ?>

<h3>
    <?php echo $image->name; ?>
    <?php echo $image->width; ?>x<?php echo $image->height; ?>
</h3>
<a href="<?php echo $image->uri; ?>">View</a>
<img src="<?php echo $image->thumb; ?>" alt="<?php echo $image->name; ?>">

<?php endforeach; ?>

<?php include "shared/footer.php" ?>