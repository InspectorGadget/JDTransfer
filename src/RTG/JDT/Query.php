<?php

namespace RTG\JDT;

class Query {

	private $url;
	private $fetchedData;

	public function __construct($url) {
		$this->url = $url;
	}

	public function onCheck($url, int $port) {

        $socket = @fsockopen("udp://" . $host, $port);
        if (!$socket)
            return null;
        $online = @fwrite($socket, "\xFE\xFD\x09\x10\x20\x30\x40\xFF\xFF\xFF\x01");
        if (!$online)
            return null;
        $challenge = @fread($socket, 1400);
        if (!$challenge)
            return null;
        $challenge = substr(preg_replace("/[^0-9-]/si", "", $challenge), 1);
        $query = sprintf("\xFE\xFD\x00\x10\x20\x30\x40%c%c%c%c\xFF\xFF\xFF\x01",
            $challenge >> 24, $challenge >> 16, $challenge >> 8, $challenge >> 0);
        if (!@fwrite($socket, $query))
            return null;
        $response = array();
        $response[] = @fread($socket, 2048);
        $response = implode($response);
        $response = substr($response, 16);
        $response = explode("\0", $response);
        array_pop($response);
        array_pop($response);
        return $response;		
	}

    public function query($host, $port) {
        
        $this->server = $this->onCheck($host, $port);
        
        if ($this->server === null) {
            return true;
        }
        
        $this->fetchedData = [
            'server_on' => $this->server[15]
        ];
            
    }


    public function getPl($host, $port) {
        $this->query($host, $port);
        return $this->fetchedData['server_on'];
    }



}