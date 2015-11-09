<?php

namespace Transmission;

use Transmission\Clients\ClientAbstract;


/**
 * Class Transmission
 * @package Playground\Transmission
 *
 * A wrapper for making calls to Transmission
 * For more information on the Transmission RPC https://trac.transmissionbt.com/browser/trunk/extras/rpc-spec.txt
 *
 * @method string torrentStart(array $ids) Start a torrent when next available
 * @method string torrentStartNow(array $ids) Start now
 * @method string torrentStop(array $ids) Stop a torrent
 * @method string torrentVerify(array $ids) Verify a torrent
 * @method string torrentReannounce(array $ids) Get more peers
 *
 */
class Transmission
{
    /**
     * @var array
     */
    protected $config = [
        'base_uri' => 'http://localhost:9091/transmission/rpc/',
        'http_error' => false
    ];

    /**
     * @var ClientAbstract
     */
    private $client;

    /**
     * @var array
     */
    public static $basicGetFields = array(
        'id', 'name', 'addedDate', 'dateCreated', 'files', 'isFinished'
    );

    /**
     * @var array
     */
    public static $extraGetFields = [
        'bandwidthPriority', 'comment', 'corruptEver',
        'creator', 'desiredAvailable', 'doneDate', 'downloadDir',
        'downloadedEver', 'downloadLimit', 'downloadLimited', 'error', 'errorString',
        'eta', 'etaIdle', 'fileStats', 'hashString', 'haveUnchecked', 'haveValid',
        'honorsSessionLimits', 'isPrivate', 'isStalled',
        'leftUntilDone', 'magnetLink', 'manualAnnounceTime', 'maxConnectedPeers',
        'metadataPercentComplete', 'peer', 'peers', 'peersConnected',
        'peersFrom', 'peersGettingFromUs', 'peersSendingToUs', 'percentDone', 'pieces',
        'pieceCount', 'pieceSize', 'priorities', 'queuePosition', 'rateDownload',
        'rateUpload', 'recheckProgress', 'secondsDownloading', 'secondsSeeding',
        'seedIdleLimit', 'seedIdleMode', 'seedRatioLimit', 'seedRatioMode',
        'sizeWhenDone', 'startDate', 'status', 'trackers', 'trackerStats',
        'totalSize', 'torrentFile', 'uploadedEver', 'uploadLimit', 'uploadLimited',
        'uploadRatio', 'wanted', 'webseeds', 'webseedsSendingToUs'
    ];

    protected $allowedActions = [
        "torrentStart" => "torrent-start",
        "torrentStartNow" => "torrent-start-now",
        "torrentStop" => "torrent-stop",
        "torrentVerify" => "torrent-verify",
        "torrentReannounce" => "torrent-reannounce"
    ];

    /**
     * @param ClientAbstract $client
     */
    public function __construct(ClientAbstract $client)
    {
        $this->client = $client;
    }

    /**
     * Runs one of the basic actions
     *
     * @param $action
     * @param array $ids
     *
     * @return mixed
     */
    public function torrentAction($action, array $ids)
    {
        $payload = [
            "method" => $action,
            "arguments" => []
        ];

        $payload["arguments"]["ids"] = $ids;

        return $this->client->request($payload);
    }

    /**
     * Mutate the torrent, see the rpc documentation
     *
     * @param array $ids
     * @param array $parameters
     *
     * @return mixed
     */
    function torrentSet(array $ids, array $parameters = [])
    {
        $payload = [
            "method" => "torrent-set",
            "arguments" => [

            ]
        ];
        array_merge($payload["arguments"], $ids, $parameters);

        return $this->client->request($payload);
    }

    /**
     * Gets torrents based on id, if no id is set then returns all torrents
     *
     * @param bool|false $allFields
     * @param array $ids
     * @param array $chosenFields
     *
     * @return mixed
     */
    public function torrentGet($allFields = false, array $ids = [], array $chosenFields = [])
    {
        $payload = [
            "method" => "torrent-get",
            "arguments" => []
        ];

        if (count($chosenFields) == 0)
            $payload["arguments"]['fields'] = ($allFields) ? Transmission::allGetFields() : Transmission::$basicGetFields;
        else
            $payload["arguments"]['fields'] = $chosenFields;

        if (count($ids) != 0)
            $payload["arguments"]['ids'] = $ids;

        return $this->client->request($payload);
    }

    /**
     * Returns the complete lists of fields for the get command
     *
     * @return array
     */
    public static function allGetFields()
    {
        return array_merge(Transmission::$basicGetFields, Transmission::$extraGetFields);
    }

    /**
     * Adds a torrent to transmission
     * Requires EITHER a base64 encoded magnet link or a location to a .torrent file which can be local or URL
     *
     * @param null $metaInfo
     * @param null $torrentFile
     * @param array $params
     *
     * @return mixed
     *
     * @throws TransmissionException
     */
    function torrentAdd($metaInfo = null, $torrentFile = null, $params = [])
    {
        if (!is_null($metaInfo))
            $params['metainfo'] = $metaInfo;
        else if (!is_null($torrentFile))
            $params['filename'] = $torrentFile;
        else
            throw new TransmissionException("A magnet link or .torrent file location is required");

        $payload = [
            "method" => 'torrent-add',
            "arguments" => $params
        ];

        return $this->client->request($payload);
    }

    /**
     * Will remove the torrent if deleteLocal is true then the file will be deleted also
     *
     * @param array $ids
     * @param bool|false $deleteLocal
     *
     * @return mixed
     */
    function torrentRemove(array $ids = [], $deleteLocal = false)
    {
        $payload = [
            "method" => "torrent-remove",
            "arguments" => [
                "ids" => $ids,
                "delete-local-data" => $deleteLocal
            ]
        ];

        return $this->client->request($payload);
    }

    /**
     * Sets the location of a torrent, if move is true it will move the file otherwise it will look in the new location
     * for existing files
     *
     * @param array $ids
     * @param string $newLocation
     * @param bool|false $move
     *
     * @return mixed
     */
    function torrentSetLocation(array $ids, $newLocation, $move = false)
    {
        $payload = [
            "method" => "torrent-remove",
            "arguments" => [
                "ids" => $ids,
                "location" => $newLocation,
                "move" => $move
            ]
        ];

        return $this->client->request($payload);
    }

    /**
     * @param array $ids
     * @param string $path
     * @param string $name
     *
     * @return mixed
     */
    function torrentRenamePath(array $ids, $path, $name)
    {
        $payload = [
            "method" => "torrent-remove",
            "arguments" => [
                "ids" => $ids,
                "path" => $path,
                "name" => $name
            ]
        ];

        return $this->client->request($payload);
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @throws TransmissionException
     */
    public function __call($name, $arguments)
    {
        if (array_key_exists($name, $this->allowedActions)) {
            $this->torrentAction($this->allowedActions[$name], $arguments);
        } else {
            throw new TransmissionException("Not an allowed action");
        }
    }
}
