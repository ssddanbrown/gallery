<?php

const IMAGES_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'images';
const THUMBS_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'thumbs';

$categories = getCategories();
require "categories.php";

/**
 * Get details of all available categories.
 * @returns Category[]
 */
function getCategories(): array {
    $names = getCategoryFolderNames();
    $categories = [];

    foreach ($names as $name) {
        $category = new Category(
            name: $name,
            thumb: getCategoryThumbnail($name)
        );

        if ($category->thumb) {
            $categories[] = $category;
        }
    }

    return $categories;
}

/**
 * Get the thumbnail image uri for the given category.
 */
function getCategoryThumbnail(string $category): string {
    $categoryImages = getCategoryImageFiles($category);
    $firstImage = $categoryImages[0] ?? '';
    if (empty($firstImage)) {
        return '';
    }

    generateImageThumbnail($category, $firstImage);
    return "thumbs/{$category}/{$firstImage}";
}

/**
 * Generated a thumbnail for the given image filename withing
 * the given category folder.
 */
function generateImageThumbnail(string $category, string $image): void {
    $imagePath = buildPath(IMAGES_DIR, $category, $image);
    $thumbPath = buildPath(THUMBS_DIR, $category, $image);

    if (file_exists($thumbPath)) {
        return;
    }

    if (!file_exists($imagePath)) {
        error("Could not find image at {$imagePath}");
    }

    if (!str_ends_with(strtolower($imagePath), '.webp')) {
        error("Image at {$imagePath} is not webp as expected");
    }

    $thumbDir = dirname($thumbPath);
    if (!file_exists($thumbDir)) {
        mkdir($thumbDir);
    }

    $originalImage = imagecreatefromwebp($imagePath);
    $thumbImage = imagescale($originalImage, 1200);
    imagewebp($thumbImage, $thumbPath, 50);
}

/**
 * Get the categorized folder names within the image directory.
 * @return string[]
 */
function getCategoryFolderNames(): array {
    $dirs = glob(buildPath(IMAGES_DIR, '*'), GLOB_ONLYDIR);
    return array_map(fn(string $dir) => basename($dir), $dirs);
}

/**
 * Get the image filenames for the images within the given
 * category folder.
 * @return string[]
 */
function getCategoryImageFiles(string $category): array {
    $images = glob(buildPath(IMAGES_DIR, $category, '*.webp'));
    return array_map(fn(string $dir) => basename($dir), $images);
}

/**
 * Build a directory path from the given path parts.
 */
function buildPath(...$parts): string {
    return implode(DIRECTORY_SEPARATOR, $parts);
}

/**
 * Error out stop the application, showing the given message.
 */
function error(string $message): never {
    echo "An error occurred: {$message}";
    http_response_code(500);
    exit(1);
}

/**
 * Dump the given arguments and exit.
 * (Dump & die)
 */
function dd(...$args): never {
    foreach ($args as $arg) {
        print_r($arg);
    }
    exit(1);
}

class Category {
    public function __construct(
        public string $name,
        public string $thumb
    ) {}
}