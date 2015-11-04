<?php
ini_set ( 'display_errors', true );
class Disk {
	private $token = '77ec43e8a3724d6fb29d20412e276d57';
	var $file = '';
	var $pathDisk = '/test/';
	var $headers = '';
	function setHeaders() {
		$this->headers = array (
				'Accept: application/json',
				"Authorization: OAuth {$this->token}" 
		);
	}
	function getUploadUrl() {
		$url = "https://cloud-api.yandex.net:443/v1/disk/resources/upload?path=" . urlencode ( $this->pathDisk . $this->file ) . "&fields=href&overwrite=true";
		
		$curl = curl_init ();
		curl_setopt ( $curl, CURLOPT_URL, $url );
		curl_setopt ( $curl, CURLOPT_HTTPHEADER, $this->headers );
		curl_setopt ( $curl, CURLOPT_HEADER, false );
		curl_setopt ( $curl, CURLOPT_POST, false );
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
		$result = curl_exec ( $curl );
		curl_close ( $curl );
		
		if ($result) {
			return json_decode ( $result );
		}
		return false;
	}
	function uploadFile($file) {
		$this->setHeaders ();
		$this->file = $file;
		$uploadUrl = $this->getUploadUrl ();
		$filesize = filesize ( $this->file );
		$datafile = fopen ( $this->file, 'rb' );
		
		$curl = curl_init ();
		curl_setopt ( $curl, CURLOPT_HTTPHEADER, $this->headers );
		curl_setopt ( $curl, CURLOPT_BINARYTRANSFER, true );
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $curl, CURLOPT_URL, $uploadUrl->href );
		curl_setopt ( $curl, CURLOPT_PUT, true );
		curl_setopt ( $curl, CURLOPT_INFILE, $datafile );
		curl_setopt ( $curl, CURLOPT_INFILESIZE, filesize ( $this->file ) );
		curl_setopt ( $curl, CURLOPT_UPLOAD, true );
		$result = curl_exec ( $curl );
		curl_close ( $curl );
		print_r ( $result );
	}
}
$app = new Disk ();
$app->uploadFile ( '1.jpg' );
