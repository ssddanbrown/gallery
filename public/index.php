<?php
declare(strict_types=1);
//ini_set('display_errors', '1');
//error_reporting(E_ALL);

// Global constants
const IMAGES_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'images';
const THUMBS_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'thumbs';

// Load configuration
$config = loadConfig();

// Path management
$uri = $_SERVER['REQUEST_URI'];
$routes = [
    'categories' => function() {
        sendPage("categories", [
            'categories' => getCategories(),
        ]);
    },
    'category' => function(string $category) {
        sendPage("category", [
            'category' => $category,
            'images' => getCategoryImages($category),
        ]);
    },
    '404' => function(string $uri) {
        sendPage("404", [
            'uri' => $uri,
        ], 404);
    }
];

if ($uri === '/') {
    $routes['categories']();
} else {
    $category = urldecode(trim($uri, '/'));
    if (categoryExists($category)) {
        $routes['category']($category);
    } else {
        $routes['404']($uri);
    }
}


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
    $dirNames = array_map(fn(string $dir) => basename($dir), $dirs);
    return array_reverse($dirNames);
}

/**
 * Check that a given category exists.
 */
function categoryExists(string $category): bool {
    $expectedPath = buildPath(IMAGES_DIR, $category);
    return is_dir($expectedPath);
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
 * Get the images within the given category.
 * @return Image[]
 */
function getCategoryImages(string $category): array {
    $files = getCategoryImageFiles($category);
    $images = array_map(function(string $file) use ($category) {

        $imagePath = buildPath('images', $category, $file);
        [$width, $height] = getimagesize($imagePath);

        return new Image(
            name: $file,
            width: $width,
            height: $height,
            uri: "./images/{$category}/{$file}",
            thumb: "./thumbs/{$category}/{$file}"
        );

    }, $files);

    foreach ($files as $fileName) {
        generateImageThumbnail($category, $fileName);
    }

    return $images;
}

/**
 * Build a directory path from the given path parts.
 */
function buildPath(...$parts): string {
    return implode(DIRECTORY_SEPARATOR, $parts);
}

/**
 * Render and send the page of the given name to the user.
 */
function sendPage(string $name, array $data = [], int $status = 200): void {
    global $config;
    $mergedData = array_merge($data, ['config' => $config]);
    extract($mergedData);

    header('Content-Type: text/html; charset=utf-8');
    http_response_code($status);
    include "templates/shared/header.php";
    include "templates/{$name}.php";
    include "templates/shared/footer.php";
}

/**
 * Load the config file from the parent directory.
 */
function loadConfig(): array {
    $configPath = buildPath(dirname(__DIR__), 'config.php');
    $config = include $configPath;
    return $config;
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

class Image {
    public function __construct(
        public string $name,
        public int $width,
        public int $height,
        public string $uri,
        public string $thumb,
    ) {}
}