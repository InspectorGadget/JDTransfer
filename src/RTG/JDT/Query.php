<?php

namespace RTG\JDT;

/**
 * Created by PhpStorm.
 * User: RTG
 * Date: 29/7/2017
 * Time: 7:20 PM
 */

class Query {

    private $fetchedData;
    private $server;
    public $plugin;
    public $name;

    public function __construct($host, $port, Loader $plugin, $name)
    {

        $this->plugin = $plugin;
        $this->query($host, $port);
        $this->name = $name;
        $count = $this->fetchedData['server_on'];


        $file = new \SQLite3($this->plugin->getDataFolder() . "sqlite.db");

        // SQL

        $sql = "SELECT * FROM servers WHERE name = '$name'";
        $res = $file->query($sql);

        if (!$row = $res->fetchArray(1)) {
            $sql = "INSERT INTO servers (name, count) VALUES ('$name', '$count')";
            $file->query($sql);
        } else {
            $sql = "UPDATE servers SET count = '$count' WHERE name = '$name'";
            $file->query($sql);
        }


    }

    public function query($host, $port) {

        $this->server = $this->UT3Query($host, $port);

        if ($this->server === null) {
            echo "Nope";
            return true;
        }

        $this->fetchedData = [
            'server_name' => $this->server[1],
            'server_on' => $this->server[15]
        ];

    }

    private function UT3Query($host, $port) {

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


}