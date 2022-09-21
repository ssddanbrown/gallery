# Gallery

This is a minimal PHP-based image gallery. It reads from a directory of WebP images, categorized by a single level of folders, and provides a simple gallery interface based upon your content.

## Features

This project is very simple by design, but it does have the following features:

- Auto listing of images.
- Supports WebP image file type.
- Automatic thumbnail generation.
- Simple & efficient grid-based interface.
- Folder-based categorization (single layer).
- Display of image names and file-sizes.

## Setup

**Note: Support for this project is minimal and development/updates are not considered "stable"** 

This project requires PHP 8.1 or greater to use. The general steps to use the project would be as follows:

1. Upload the project files to your server system.
2. Point your webserver to serve from the `public` directory of the project.
  - Webserver should attempt to serve static files based on the path, but default back to the `public/index.php` file.
  - You may want to consider ensuring that image file caching is active and http2 (or greater) is in use for image load performance.
3. Copy the `config.php.example` file to `config.php` and fill with your own details.

## Usage

Create categories by creating folders within the `public/images` folder. The category name will be based upon the folder name. Categories will be shown sorted by name, descending (Primary to allow YYYY-MM-DD based organisation).

Upload images, in WebP format, to your category folder. When required in the interface, scaled-down thumbnail sizes will be auto-generated into the `public/thumbs` folder. These images will be used in the UI until a user clicks on an image within a category, in which case the original image file will be served.

If you've changed an image and want to regenerate the thumbnail, simply delete the image file of the same name within the `public/thumbs` directory.

## Low Maintenance Project

This is a low maintenance project. The scope of features and support are purposefully kept narrow for my purposes to ensure longer term maintenance is viable. I'm not looking to grow this into a bigger project at all.

Issues and PRs raised for bugs are perfectly fine assuming they don't significantly increase the scope of the project. Please don't open PRs for new features that expand the scope.