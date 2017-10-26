<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/Utils/functions.php');
$adminController = new \Controller\AdminController();
$error = '';
if($adminController->checkPermission('Edit pages')) {
	if(isset($_FILES) && array_key_exists('edimage', $_FILES)) {
		if($_FILES['edimage']['size'] <= 5242880) {
			$allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
			$detectedType = exif_imagetype($_FILES['edimage']['tmp_name']);
			if(in_array($detectedType, $allowedTypes)) {
				$uid = uniqid('img_');
				$filename = $uid . basename($_FILES['edimage']['name']);
				$target_dir = _APP_DIR_ . 'uploads/';
				if(array_key_exists('targetdir', $_REQUEST)) $target_dir .= $_REQUEST['targetdir'] . '/';
				$target_file = $target_dir . $filename;
				$check = getimagesize($_FILES['edimage']['tmp_name']);
				if($check !== false) {
					if(move_uploaded_file($_FILES['edimage']['tmp_name'], $target_file)) {
						$path_parts = pathinfo($target_file);
						$thumb_file_name = 'thumb' . $path_parts['filename'] . '.jpg';
						$thumb_target_file = $target_dir . $thumb_file_name;
						$thumb = new \Utils\PHPThumb\GD($target_file);
						$thumb->resize(100, 100);
						$thumb->save($thumb_target_file, 'jpg');
						$targetThumbDir = _ADDRESS_ . _FOLDER_URL_ . 'uploads/';
						if(array_key_exists('targetdir', $_REQUEST)) $targetThumbDir .= $_REQUEST['targetdir'] . '/';
						echo json_encode(array('src' => $targetThumbDir . $thumb_file_name, 'image' => $filename));
						exit;
					}
					else $error = __('Error uploading');
				}
				else $error = __('File is not an image');
			}
			else $error = __('File is not an image');
		}
		else $error = __('Max upload 5MB');
	}
	elseif(array_key_exists('clearImg', $_POST)) {
		$filename = strip_tags($_POST['clearImg']);
		$target_dir = _APP_DIR_ . 'uploads/';
		if(array_key_exists('targetdir', $_REQUEST)) $target_dir .= $_REQUEST['targetdir'] . '/';
		$target_file = $target_dir . $filename;
		$path_parts = pathinfo($target_file);
		$thumb_file_name = 'thumb' . $path_parts['filename'] . '.jpg';
		$thumb_target_file = $target_dir . $thumb_file_name;
		if(file_exists($target_file)) unlink($target_file);
		if(file_exists($thumb_target_file)) unlink($thumb_target_file);
		exit;
	}
	else $error = __('No uploaded file');
}
else $error = __('You do not have access');
echo json_encode(array('error' => $error));
exit;