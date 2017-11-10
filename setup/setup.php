<?php
$utilsDir = dirname(dirname(__FILE__)) . '/Utils/';
if(!is_writable($utilsDir)) die($utilsDir . ' directory is not writable!');
if(!count($_POST)) echo file_get_contents(dirname(__FILE__) . '/setup.html');
else {
	$configFile = dirname(dirname(__FILE__)) . '/Utils/config.php';
	$filenameLogo = basename($_FILES['logo']['name']);
	$target_dir = realpath(dirname(dirname(__FILE__))) . '/img/';
	$target_fileLogo = $target_dir . $filenameLogo;
	@move_uploaded_file($_FILES['logo']['tmp_name'], $target_fileLogo);
	$filenameOgimg = false;
	if(array_key_exists('ogimage', $_FILES) && array_key_exists('name', $_FILES['ogimage'])) {
		$filenameOgimg = basename($_FILES['ogimage']['name']);
		$target_fileOgimg = $target_dir . $filenameOgimg;
		@move_uploaded_file($_FILES['ogimage']['tmp_name'], $target_fileOgimg);
	}
	$configFileContents = '<?php
/**
* Website root address
*/
define("_ADDRESS_", "' . $_POST['rootAddress'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Website folder
*/
define("_FOLDER_URL_", "' . $_POST['folderUrl'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Default redirect after login
*/
define("_DEFAULT_REDIRECT_", "' . $_POST['defaultRedirect'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Default application language
*/
define("_DEFAULT_LANGUAGE_", "' . $_POST['language'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Application name
*/
define("_APP_NAME_", "' . $_POST['appName'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Enter your hash key
*/
define("_HASH_KEY_", "' . $_POST['hash'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Email server address (for contact forms, newsletter etc.)
*/
define("_MAIL_HOST_", "' . $_POST['mailHost'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Email server port (for contact forms, newsletter etc.)
*/
define("_MAIL_PORT_", "' . $_POST['mailPort'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Email username (for contact forms, newsletter etc.)
*/
define("_MAIL_USER_", "' . $_POST['mailUser'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Email password (for contact forms, newsletter etc.)
*/
define("_MAIL_PASS_", "' . $_POST['mailPass'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Email address (for contact forms, newsletter etc.)
*/
define("_MAIL_FROM_", "' . $_POST['mailFrom'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Email display name (for contact forms, newsletter etc.)
*/
define("_MAIL_NAME_", "' . $_POST['mailName'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Email for respond (for contact forms, newsletter etc.)
*/
define("_MAIL_ADDRESS_", "' . $_POST['mailReply'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Company name
*/
define("_COMPANY_NAME_", "' . $_POST['companyName'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Company address (full address)
*/
define("_COMPANY_ADDRESS_", "' . $_POST['companyFullAddress'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Company address (line 1)
*/
define("_COMPANY_ADDRESS_L1_", "' . $_POST['companyAddressLine1'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Company address (line 2)
*/
define("_COMPANY_ADDRESS_L2_", "' . $_POST['companyAddressLine2'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Company phone
*/
define("_COMPANY_PHONE_", "' . $_POST['companyPhone'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Database host
*/
define("_DB_HOST_", "' . $_POST['databaseHost'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Database port
*/
define("_DB_PORT_", "' . $_POST['databasePort'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Database name
*/
define("_DB_NAME_", "' . $_POST['databaseName'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Database username
*/
define("_DB_USER_", "' . $_POST['databaseUser'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Database password
*/
define("_DB_PASS_", "' . $_POST['databasePassword'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Facebook App Id
*/
define("_FBAPPID_", "' . $_POST['fbappid'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Facebook App Secret
*/
define("_FBAPPSECRET_", "' . $_POST['fbappsecret'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Google Analytics ID
*/
define("_GOOGLEANALYTICSID_", "' . $_POST['gaid'] . '");' . PHP_EOL;
	$configFileContents .= '/**
* Facebook page url
*/
define("_FB_LINK_", "' . (!empty(trim($_POST['fblink']))?'https://www.facebook.com/' . $_POST['fblink']:'') . '");' . PHP_EOL;
	$configFileContents .= '/**
* Twitter page url
*/
define("_TWITTER_LINK_", "' . (!empty(trim($_POST['twlink']))?'https://www.twitter.com/' . $_POST['twlink']:'') . '");' . PHP_EOL;
	$configFileContents .= '/**
* LinkedIn page url
*/
define("_LINKEDIN_LINK_", "' . (!empty(trim($_POST['lilink']))?'https://www.linkedin.com/' . $_POST['lilink']:'') . '");' . PHP_EOL;
	$configFileContents .= '/**
* Pinterest page url
*/
define("_PINTEREST_LINK_", "' . (!empty(trim($_POST['pilink']))?'https://www.pinterest.com/' . $_POST['pilink']:'') . '");' . PHP_EOL;
	$configFileContents .= '/**
* Google+ page url
*/
define("_GPLUS_LINK_", "' . (!empty(trim($_POST['gplink']))?'https://plus.google.com/' . $_POST['gplink']:'') . '");' . PHP_EOL;
	$configFileContents .= '/**
* Instagram page url
*/
define("_INSTAGRAM_LINK_", "' . (!empty(trim($_POST['iglink']))?'https://www.instagram.com/' . $_POST['iglink']:'') . '");' . PHP_EOL;
	$configFileContents .= '/**
* Logo url
*/
define("_LOGO_", \'' . $filenameLogo . '\');' . PHP_EOL;
	if($filenameOgimg) $configFileContents .= '/**
* Open Graph image url
*/
define("_OG_IMAGE_", \'' . $filenameOgimg . '\');' . PHP_EOL;
	file_put_contents($configFile, $configFileContents);
	require_once($configFile);
	require_once($utilsDir . 'Database.class.php');
	require_once($utilsDir . 'Bcrypt.class.php');
	$bcrypt = new Utils\Bcrypt(10);
	$adminPassword = $bcrypt->hash($_REQUEST['adminPassword']);
	$sql = file_get_contents(dirname(__FILE__) . '/install.sql');
	$sql .= "INSERT INTO `admins` (`id`, `name`, `username`, `password`, `status`) VALUES(1, 'Administrator', 'admin', '{$adminPassword}', 1);
	INSERT INTO `admins_permissions` VALUES(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6);";
	$db = \Utils\Database::getInstance();
	$db->multi_query($sql);
	while(mysqli_more_results($db)) mysqli_next_result($db);
	require_once dirname(dirname(__FILE__)) . '/admin/modules.php';
	reread();
	echo "Everything done. You can now delete the folder /setup from your installation directory.";
}