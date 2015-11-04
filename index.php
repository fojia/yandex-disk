<?php
ini_set ( 'display_errors', true );
class Disk {
	private $token = '';
	var $file = '';
	var $pathDisk = '';
	var $headers = '';
	var $method = "GET";
	function __construct($token = "null") {
		$this->token = $token;
		$this->setHeaders ();
	}
	function setHeaders() {
		$this->headers = array (
				'Accept: application/json',
				"Authorization: OAuth {$this->token}" 
		);
	}
	function getData($url) {
		$curl = curl_init ();
		curl_setopt ( $curl, CURLOPT_URL, $url );
		curl_setopt ( $curl, CURLOPT_CUSTOMREQUEST, $this->method );
		curl_setopt ( $curl, CURLOPT_HTTPHEADER, $this->headers );
		curl_setopt ( $curl, CURLOPT_HEADER, false );
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
		$result = curl_exec ( $curl );
		curl_close ( $curl );
		
		if ($result) {
			return json_decode ( $result );
		}
		
		return false;
	}
	function getUploadUrl() {
		$url = "https://cloud-api.yandex.net:443/v1/disk/resources/upload?path=" . urlencode ( $this->pathDisk . $this->file ) . "&fields=href&overwrite=true";
		return $this->getData ( $url );
	}
	function uploadFile($file, $path) {
		$uploadUrl = $this->getUploadUrl ();
		if ($uploadUrl == false) {
			return false;
		}
		
		$this->file = $file;
		$this->$pathDisk = $path;
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
	}
	function getFileUrl($path) {
		$url = "https://cloud-api.yandex.net:443/v1/disk/resources/download?path=" . urlencode ( $path ) . "&fields=href";
		$link = $this->getData ( $url );
		return $link->href;
	}
	function deleteFile($path, $permanently = false) {
		if ($permanently) {
			$permanently = "true";
		} else {
			$permanently = "false";
		}
		$this->method = "DELETE";
		$url = "https://cloud-api.yandex.net:443/v1/disk/resources?path=" . urlencode ( $path ) . "&permanently={$permanently}";
		return $this->getData ( $url );
	}
}

