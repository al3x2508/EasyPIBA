<?php
$body = '';
$mesaj = '';
$continutBody = '';
$page_title="Resetare parolă";
$description="Resetare parolă.";
$h1='';
$js=array('validate.js','recuperare.js');
$css=array();
if(array_key_exists('email',$_POST)) {
	$email = strip_tags(htmlspecialchars(stripslashes(trim($_POST['email']))));
	if(!\Utils\Util::checkFieldValue('email', $email)) $mesaj = 'Introduceți o adresă de e-mail validă';
	else {
		$client = new Model\Model('clienti');
		$rezClient = $client->getOneResult('email', strip_tags(htmlspecialchars(stripslashes(trim($_POST['email'])))));
		if($rezClient) {
			//E-mailul exista, trimitem o noua parola pe email
			$clientId = $rezClient->id;
			$nmcl = $rezClient->nume_client . ' ' . $rezClient->prenume_client;
			$resetareModel = new Model\Model('resetari_parole');
			$resetareModel->client = $clientId;
			$resetareModel->data_expirarii = array(date('Y-m-d H:i:s'), '>=');
			$resetare = $resetareModel->get();
			if(count($resetare) > 0) $cod = $resetare[0]['cod'];
			else $cod = \Utils\Util::storeResetare($clientId);
			$linkresetare = _ADDRESS_ . _FOLDER_URL_ . 'recuperare_parola.html?cheie=' . $cod;
			$mesaj = '<h2>Bună ziua ' . $nmcl . '</h2>
				<p>Pentru resetarea parolei accesați link-ul de mai jos:</p>
				<p><a href="' . $linkresetare . '">' . $linkresetare . '</a></p>';
			\Utils\Util::send_email($email, $nmcl, 'Resetare parolă', $mesaj, true);
			$continutBody = '<div class="col-lg-12"><h3>Un e-mail cu instrucțiunile pentru resetarea parolei a fost trimis către dumneavoastră!</h3></div>';
		}
		else $mesaj = 'Adresa de e-mail nu este înregistrată';
	}
}
elseif(array_key_exists('cheie', $_REQUEST)) {
	$campuri = array();
	$checkFields = array('password', 'cpassword');
	if(array_key_exists('password', $_POST)) {
		foreach($checkFields AS $field) if(array_key_exists($field, $_POST) && !\Utils\Util::checkFieldValue($field, $_POST[ $field ])) $campuri[$field] = 1;
		if($_POST['password'] != $_POST['cpassword']) $campuri['cpassword'] = 1;
	}
	if(array_key_exists('password', $_POST) && count($campuri) == 0) {
		$cheie = strip_tags(htmlspecialchars(stripslashes(trim($_REQUEST['cheie']))));
		$resetareModel = new Model\Model('resetari_parole');
		$resetare = $resetareModel->getOneResult('cod', $cheie);
		if($resetare) {
			$clientId = $resetare->client;
			$resetare->delete();
			$client = new Model\Model('clienti');
			$client->id = $clientId;
			$bcrypt = new Utils\Bcrypt(10);
			$client->parola = $bcrypt->hash(strip_tags(htmlspecialchars(stripslashes(trim($_POST['password'])))));
			$client->update();
			$continutBody = '<div class="col-lg-12"><h2>Parola a fost schimbată!</h2></div>';
		}
	}
	else {
		$continutBody = '<form method="post" action="/recuperare_parola.html" class="validateform contactform">
					<div class="col-lg-6 margintop10">
						<div class="col-lg-12 margintop10 field">
							<input type="hidden" name="cheie" value="' . $_REQUEST['cheie'] . '" />
							<div class="input input-hoshi">
								<input type="password" name="password" id="password" class="input__field input__field-hoshi" data-rule="maxlen:8" data-msg="Introduceți cel puțin 8 caractere"';
		if(array_key_exists('password', $_POST)) $continutBody .= " value=\"" . strip_tags(htmlspecialchars(stripslashes(trim($_POST['password'])))) . "\"";
		$continutBody .= ' required />
                                <label class="input__label input__label-hoshi input__label-hoshi-color-1" for="password" data-ex="Introduceți cel puțin 8 caractere">
									<span class="input__label-content input__label-content-hoshi"><i class="fa fa-eye-slash"></i> * Noua parolă</span>
								</label>
								<div class="validation">';
		if(array_key_exists('password', $campuri)) $continutBody .= 'Introduceți cel puțin 8 caractere';
		$continutBody .= '		</div>
							</div>
						</div>
						<div class="col-lg-12 margintop10 field">
							<div class="input input-hoshi">
								<input type="password" name="cpassword" id="cpassword" class="input__field input__field-hoshi" data-rule="maxlen:8" data-msg="Confirmați noua parolă"';
		if(array_key_exists('cpassword', $_POST)) $continutBody .= " value=\"" . strip_tags(htmlspecialchars(stripslashes(trim($_POST['cpassword'])))) . "\"";
		$continutBody .= ' required />
                                <label class="input__label input__label-hoshi input__label-hoshi-color-1" for="cpassword">
									<span class="input__label-content input__label-content-hoshi"><i class="fa fa-eye-slash"></i> * Confirmați noua parolă</span>
								</label>
								<div class="validation">';
		if(array_key_exists('cpassword', $campuri)) $continutBody .= 'Confirmați noua parolă';
		$continutBody .= '		</div>
							</div>
						</div>
						<div class="col-lg-12"><button type="submit" class="btn btn-theme margintop10">ACTUALIZEAZĂ</button></div>
					</div>
				</form>';
	}
}
if(empty($continutBody)) {
	$continutBody = '<form method="post" action="#" class="validateform contactform">
					<div class="col-lg-6 margintop10 field">
						<div class="input input-hoshi">
							<input type="email" name="email" id="email" class="input__field input__field-hoshi" data-rule="email" data-msg="Introduceți adresa de e-mail"';
	if(array_key_exists('email',$_POST)) $continutBody.=" value=\"".strip_tags(htmlspecialchars(stripslashes(trim($_POST['email']))))."\"";
	$continutBody.=' pattern="^(?:[\w\d-]+\.?)+\@(?:(?:[\w\d]\-?)+\.)+\w{2,4}$" required />
                            <label class="input__label input__label-hoshi input__label-hoshi-color-1" for="email" data-ex="ex: popescu.teodor@gmail.com">
								<span class="input__label-content input__label-content-hoshi"><i class="fa fa-envelope-o"></i> * Email</span>
							</label>
								<div class="validation">';
	if(!empty($mesaj)) $continutBody .= $mesaj;
	$continutBody .='						</div>
						</div>
					</div>
					<div class="col-lg-12"><button type="submit" class="btn btn-theme margintop10">RESETEAZĂ</button></div>
				</form>';
}
$continut = '<section id="content" class="marginbot20 margintop80">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="row">
					<div class="col-lg-12"><h2>Resetare parolă</h2></div>
					' . $continutBody . '
				</div>
			</div>
		</div>
	</div>
</section>';
?>