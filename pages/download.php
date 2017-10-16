<?php
preg_match('/^download\/(.*)/', $pinf, $m);
if(count($m) > 1) {
	$file = $m[1] . ".pdf";
	$spath = _APP_DIR_ . $file;
	if(file_exists($spath)) {
		$pi = pathinfo($spath);
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=" . urlencode($pi['basename']));
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Description: File Transfer");
		header("Content-Length: " . filesize($spath));
		flush(); // this doesn't really matter.
		$fp = fopen($spath, "r");
		while(!feof($fp)) {
			echo fread($fp, 65536);
			flush();
		}
		fclose($fp);
	}
}
exit;