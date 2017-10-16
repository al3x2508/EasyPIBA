<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/Utils/functions.php');
$adminController = new \Controller\AdminController();
$error = '';
if($adminController->checkPermission('Editează pages')) {
	if(isset($_FILES) && array_key_exists('edimagine', $_FILES)) {
		if($_FILES['edimagine']['size'] <= 5242880) {
			$allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
			$detectedType = exif_imagetype($_FILES['edimagine']['tmp_name']);
			if(in_array($detectedType, $allowedTypes)) {
				$uid = uniqid('img_');
				$filename = $uid . basename($_FILES['edimagine']['name']);
				$target_dir = dirname(dirname(dirname(__FILE__))) . '/uploads/';
				if(array_key_exists('targetdir', $_REQUEST)) $target_dir .= $_REQUEST['targetdir'] . '/';
				$target_file = $target_dir . $filename;
				$check = getimagesize($_FILES['edimagine']['tmp_name']);
				if($check !== false) {
					if (move_uploaded_file($_FILES['edimagine']['tmp_name'], $target_file)) {
						$path_parts = pathinfo($target_file);
						$thumb_file_name = 'thumb' . $path_parts['filename'] . '.jpg';
						$thumb_target_file = $target_dir . $thumb_file_name;
						$thumb = new \Utils\PHPThumb\GD($target_file);
						$thumb->resize(100, 100);
						$thumb->save($thumb_target_file, 'jpg');
						$targetThumbDir = _ADDRESS_ . _FOLDER_URL_ . 'uploads/';
						if(array_key_exists('targetdir', $_REQUEST)) $targetThumbDir .= $_REQUEST['targetdir'] . '/';
						echo json_encode(array('src' => $targetThumbDir . $thumb_file_name, 'imagine' => $filename));
						exit;
					}
					else $error = 'O eroare a aparut la momentul incarcarii.';
				}
				else $error = 'Fisierul nu este o imagine';
			}
			else $error = 'Fisierul nu este o imagine';
		}
		else $error = 'Fisierul incarcat depaseste 5MB';
	}
	elseif(array_key_exists('clearImg', $_POST)) {
		$filename = strip_tags($_POST['clearImg']);
		$target_dir = dirname(dirname(dirname(__FILE__))) . '/uploads/';
		if(array_key_exists('targetdir', $_REQUEST)) $target_dir .= $_REQUEST['targetdir'] . '/';
		$target_file = $target_dir . $filename;
		$path_parts = pathinfo($target_file);
		$thumb_file_name = 'thumb' . $path_parts['filename'] . '.jpg';
		$thumb_target_file = $target_dir . $thumb_file_name;
		if(file_exists($target_file)) unlink($target_file);
		if(file_exists($thumb_target_file)) unlink($thumb_target_file);
		exit;
	}
	else $error = 'Nici un fisier incarcat';
}
else $error = 'Nu ai acces';
echo json_encode(array('error' => $error));
exit;
?>