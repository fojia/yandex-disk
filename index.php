<?php

namespace Fojia\Disk;

/**
 * Class for upload/delete files from yandex disk
 * Class Disk
 * @package Fojia\Disk
 */
class Disk {
	private $token = '';
	var $file = '';
	var $pathDisk = '';
	var $headers = '';
	var $method = "GET";

    /**
     * Init class and define token how parameter
     * Disk constructor.
     * @param string $token
     */
	function __construct($token = "null") {
		$this->token = $token;
		$this->setHeaders ();
	}

    /**
     * Set http headers
     */
	function setHeaders() {
		$this->headers = array (
				'Accept: application/json',
            "Authorization: OAuth {$this->token}"
		);
	}


    /**
     * Get content data by url
     * @param $url
     * @return bool|mixed
     */
    function getData($url)
    {
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

    /**
     * Get url for upload file
     * @return bool|mixed
     */
	function getUploadUrl() {
        $url = 'https://cloud-api.yandex.net:443/v1/disk/resources/upload?path=' . urlencode($this->pathDisk . $this->file) . '&fields=href&overwrite=true';
		return $this->getData ( $url );
	}

    /**
     * Upload file by path
     * @param $file
     * @param $path
     * @return bool
     */
	function uploadFile($file, $path) {
		$this->file = $file;
		$this->pathDisk = $path;
		$uploadUrl = $this->getUploadUrl ();

		if ($uploadUrl == false) {
			return false;
		}

        $datafile = fopen($this->file, 'rb');
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
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * Get url uploaded file
     * @param $path
     * @return mixed
     */
	function getFileUrl($path) {
        $url = 'https://cloud-api.yandex.net:443/v1/disk/resources/download?path=' . urlencode($path) . '&fields=href';
		$link = $this->getData ( $url );
		return $link->href;
	}

    /**
     * Delete file by path
     * @param $path
     * @param bool $permanently if need delete file forever and ever
     * @return bool|mixed
     */
	function deleteFile($path, $permanently = false) {
		$this->method = "DELETE";
        $url = 'https://cloud-api.yandex.net:443/v1/disk/resources?path=' . urlencode($path) . '&permanently=' . (bool)$permanently;
		return $this->getData ( $url );
	}
}

