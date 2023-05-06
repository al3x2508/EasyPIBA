<?php
if (isset($_GET['REQUEST_URI'])) {
    require_once 'functions.php';
    $filename = $_ENV['APP_DIR'] . 'assets' . $_GET['REQUEST_URI'];
    if (file_exists($filename)) {
        $info = pathinfo($filename);
        switch ($info['extension']) {
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($filename);
                ob_start();
                imagejpeg($image, null, 100);
                break;
            case 'png':
                $image = imagecreatefrompng($filename);
                imagepalettetotruecolor($image);
                imageAlphaBlending($image, true);
                imageSaveAlpha($image, true);

                ob_start();
                imagepng($image);
                break;
            default:
                exit;
        }
        $cont = ob_get_contents();
        ob_end_clean();
        imagedestroy($image);
        $content = imagecreatefromstring($cont);
        $webp_filename = $info['dirname'] . '/' . $info['filename'] . '.webp';
        imagewebp($content, $webp_filename);
        imagedestroy($content);
        $fp = fopen($webp_filename, 'rb');
        header('Content-Type: image/webp');
        header("Content-Length: " . filesize($webp_filename));
        fpassthru($fp);
        exit;
    }
}