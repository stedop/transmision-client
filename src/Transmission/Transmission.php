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
        "torrentStart" => "torrent-start",              // tr_torrentStart
        "torrentStartNow" => "torrent-start-now",       // tr_torrentStartNow
        "torrentStop" => "torrent-stop",                // tr_torrentStop
        "torrentVerify" => "torrent-verify",            // tr_torrentVerify
        "torrentReannounce" => "torrent-reannounce"     // tr_torrentManualUpdate ("ask tracker for more peers")
    ];

    /**
     * Used for the torent-set command
     *
     * Just as an empty "ids" value is shorthand for "all ids", using an empty array
     * for "files-wanted", "files-unwanted", "priority-high", "priority-low", or
     * "priority-normal" is shorthand for saying "all files".
     *
     * @var array
     */
    public static $mutatorFields = [
        "bandwidthPriority",   // number     this torrent's bandwidth tr_priority_t
        "downloadLimit",       // number     maximum download speed (KBps)
        "downloadLimited",     // boolean    true if "downloadLimit" is honored
        "files-wanted",        // array      indices of file(s) to download
        "files-unwanted",      // array      indices of file(s) to not download
        "honorsSessionLimits", // boolean    true if session upload limits are honored
        "ids" ,                // array      torrent list, as described in 3.1
        "location",            // string     new location of the torrent's content
        "peer-limit",          // number     maximum number of peers
        "priority-high",       // array      indices of high-priority file(s)
        "priority-low",        // array      indices of low-priority file(s)
        "priority-normal",     // array      indices of normal-priority file(s)
        "queuePosition",       // number     position of this torrent in its queue [0...n)
        "seedIdleLimit",       // number     torrent-level number of minutes of seeding inactivity
        "seedIdleMode",        // number     which seeding inactivity to use.  See tr_idlelimit
        "seedRatioLimit",      // double     torrent-level seeding ratio
        "seedRatioMode",       // number     which ratio to use.  See tr_ratiolimit
        "trackerAdd",          // array      strings of announce URLs to add
        "trackerRemove",       // array      ids of trackers to remove
        "trackerReplace",      // array      pairs of <trackerId/new announce URLs>
        "uploadLimit",         // number     maximum upload speed (KBps)
        "uploadLimited",       // boolean    true if "uploadLimit" is honored
    ];

    /**
     * @var array
     */
    public static $renameFields = [

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
     */
    public function torrentAction($action, array $ids)
    {
        $payload = [
            "method" => $action,
            "arguments" => []
        ];

        $payload["arguments"]["ids"] = $ids;

        $this->client->request($payload);
    }

    function torrentSet($parameters = [])
    {
        //todo: implement torrentSet
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

    function torrentSetLocation()
    {
        //todo implement torrentSetLocation()
    }

    function torrentRenamePath()
    {
        //todo implement torrentRenamePath
    }

    public function __call($name, $arguments)
    {
        if (array_key_exists($name, $this->allowedActions)) {
            $this->torrentAction($this->allowedActions[$name], $arguments);
        } else {
            throw new TransmissionException("Not an allowed action");
        }
    }
}
