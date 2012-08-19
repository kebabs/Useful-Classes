<?php

/*
// Name:    reCaptcha API Wrapper
// File:    class.recaptcha.php
// Version: 1.0
// 
// Author:  Simon Brown <simon@clanbug.com>
// Licence: DWTFYWPL v2
//
// To use, stick this alongside (probably at the top) your form submission processing code
//
// $reCaptcha = new reCaptcha('YOUR-PRIVATE-KEY');
// $reCaptcha->verify(); // Returns true or false based on what the user input
//
// For more information / documentation on the reCaptcha API visit
// https://developers.google.com/recaptcha/intro
//
*/

/*

           DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
                    Version 2, December 2004

        Copyright (C) 2004 Sam Hocevar <sam@hocevar.net>

 Everyone is permitted to copy and distribute verbatim or modified
 copies of this license document, and changing it is allowed as long
 as the name is changed.

            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
   TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

            0. You just DO WHAT THE FUCK YOU WANT TO.

*/

 /* 
 *
 * This file is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details. 
 *
*/

class reCaptcha {

	private $privateKey;
	private $remoteHost;
	private $challenge;
	private $response;

	private $verifyURL = 'http://www.google.com/recaptcha/api/verify';

	public function __construct($privateKey, $remoteHost = null){

		$this->privateKey = $privateKey;
		$this->remoteHost = $_SERVER['REMOTE_ADDR'];

		if(!is_null($remoteHost))
			$this->remoteHost = $remoteHost;

	}

	public function challenge($challenge){

		$this->challenge = $challenge;

	}

	public function response($response){

		$this->response = $response;

	}

	public function verify(){

		if (empty($this->challenge))
			$this->challenge = $_POST['recaptcha_challenge_field'];

		if(empty($this->response))
			$this->response = $_POST['recaptcha_response_field'];

		$package = array('privatekey'   => $this->privateKey,
	    		    	 'remoteip'     => $this->remoteHost,
	    		    	 'challenge'    => $this->challenge,
	    		    	 'response'     => $this->response);

		$result = explode(PHP_EOL, $this->execute($package));

		if(is_string($result[0])){

			if($result[0] == 'true')
				return true;

		}

		return false;

	}

	private function execute($package){

		$packageString = '';

		foreach($package as $key => $value){ 

			$packageString .= $key.'='.$value.'&'; 

		}		

		rtrim($packageString, '&');

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->verifyURL);
		curl_setopt($ch, CURLOPT_POST, count($package));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $packageString);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($ch);
		curl_close($ch);

		return $result;

	}

}

?>