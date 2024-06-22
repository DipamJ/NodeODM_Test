<?php
/*$password_length = 8;

function password_strength($password) {
	$returnVal = True;

	if ( strlen($password) < $password_length ) {
		$returnVal = False;
	}

	if ( !preg_match("#[0-9]+#", $password) ) {
		$returnVal = False;
	}

	if ( !preg_match("#[a-z]+#", $password) ) {
		$returnVal = False;
	}

	if ( !preg_match("#[A-Z]+#", $password) ) {
		$returnVal = False;
	}

	if ( !preg_match("/[\'^£$%&*()}{@#~?><>,|=_+!-]/", $password) ) {
		$returnVal = False;
	}

	return $returnVal;

}*/


public function checkPassword($password, &$errors) {
$errors_init = $errors;

if (strlen($password) < 8) {
		$errors[] = "Password too short!";
}

if (!preg_match("#[0-9]+#", $password)) {
		$errors[] = "Password must include at least one number!";
}

if (!preg_match("#[a-zA-Z]+#", $password)) {
		$errors[] = "Password must include at least one letter!";
}

return ($errors == $errors_init);
}
?>
