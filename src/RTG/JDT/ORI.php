<?php

namespace RTG\JDT;

class Query
{
    /** @var string[] */
    private $server;
    /** @var string[] */
    private $fetchedData;

    public function __construct($host, $port)
    {
        $this->server = $this->UT3Query($host, $port);
        if ($this->server === null) {
            return false;
        }
        $this->fetchedData =
            [
                'server' => $this->server[1],
                'server_gm' => $this->server[3],
                'server_gn' => $this->server[5],
                'version' => $this->server[7],
                'server_engine' => $this->server[9],
                'plugins' => $this->server[11],
                'server_lobby' => $this->server[13],
                'server_on' => $this->server[15],
                'server_max' => $this->server[17],
                'server_wl' => $this->server[19],
                'server_ip' => $this->server[21],
                'server_port' => $this->server[23],
                'server_online' => implode('<br>', array_slice($this->server, 27))
            ];
        return true;
    }
    /**
     * @param string $host
     * @param int $port
     * @return $this
     */
    public function putServer($host, $port = 19132)
    {
        $this->server = $this->UT3Query($host, $port);
        return $this;
    }
    /**
     * @return string
     */
    public function status()
    {
        return $this->server === null ? 'offline' : 'online';
    }
    /**
     * @return string[]
     */
    public function getAll()
    {
        return $this->fetchedData;
    }
    /**
     * @return string
     */
    public function getServerName()
    {
        return $this->fetchedData['server'];
    }
    /**
     * @return string
     */
    public function getServerGameMode()
    {
        return $this->fetchedData['server_gm'];
    }
    /**
     * @return string
     */
    public function getServerGameName()
    {
        return $this->fetchedData['server_gn'];
    }
    /**
     * @return string
     */
    public function getServerVersion(){
        return $this->fetchedData['version'];
    }
    /**
     * @return string
     */
    public function getServerEngine()
    {
        return $this->fetchedData['server_engine'];
    }
    /**
     * @return string
     */
    public function getServerPlugins()
    {
        return $this->fetchedData['plugins'];
    }
    /**
     * @return string
     */
    public function serverLobbyName()
    {
        return $this->fetchedData['server_lobby'];
    }
    /**
     * @return string
     */
    public function getPlayersCount()
    {
        return $this->fetchedData['server_on'];
    }
    /**
     * @return string
     */
    public function getServerMaxPlayers()
    {
        return $this->fetchedData['server_max'];
    }
    /**
     * @return string
     */
    public function getServerWhiteList()
    {
        return $this->fetchedData['server_wl'];
    }
    /**
     * @return string
     */
    public function getServerIP()
    {
        return $this->fetchedData['server_ip'];
    }
    /**
     * @return string
     */
    public function getServerPort()
    {
        return $this->fetchedData['server_port'];
    }
    /**
     * @return string
     */
    public function getServerOnline()
    {
        return $this->fetchedData['server_online'];
    }
    /**
     * @param string $host
     * @param int $port
     * @return array|null|string
     */
    private function UT3Query($host, $port)
    {
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