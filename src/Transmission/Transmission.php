<?php

namespace Transmission;

use Transmission\Clients\ClientAbstract;


/**
 * Class Transmission
 * @package Playground\Transmission
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
        'eta', 'etaIdle', 'files', 'fileStats', 'hashString', 'haveUnchecked', 'haveValid',
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
     * Used for "torrent-add" command
     *
     * Either "filename" OR "metainfo" MUST be included.
     * All other arguments are optional.
     *
     * @var array
     */
    public static $addFields = [
        "cookies" => '',            // string      pointer to a string of one or more cookies.
        "download-dir" => '',       // string      path to download the torrent to
        "filename" => '',           // string      filename or URL of the .torrent file
        "metainfo" => '',           // string      base64-encoded .torrent content
        "paused" => false,          // boolean     if true, don't start the torrent
        "peer-limit" => 0,          // number      maximum number of peers
        "bandwidthPriority" => 1,   // number      torrent's bandwidth tr_priority_t
        "files-wanted" => [],       // array       indices of file(s) to download
        "files-unwanted" => [],     // array       indices of file(s) to not download
        "priority-high",            // array       indices of high-priority file(s)
        "priority-low",             // array       indices of low-priority file(s)
        "priority-normal",          // array       indices of normal-priority file(s)
    ];

    /**
     * Used in "torrent-delete"
     *
     * @var array
     */
    public static $deleteFields = [
        "ids" => [],                  // array      torrent list of ids
        "delete-local-data" => false, // boolean    delete local data. (default: false)
    ];

    /**
     * @var array
     */
    public static $moveFields = [
        "ids" => [],             // array      torrent list
        "location" => '',        // string     the new torrent location
        "move" => false,         // boolean    if true, move from previous location.
                                 //            otherwise, search "location" for files
                                 //            (default: false)
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

    function torrentStart($ids = [])
    {
    }

    function torrentStartNow($ids = [])
    {
    }

    function torrentStop($ids = [])
    {
    }

    function torrentVerify($ids = [])
    {
    }

    function torrentReannounce($ids = [])
    {
    }

    function torrentSet($parameters = [])
    {
    }

    /**
     * @param bool|false $allFields
     * @param array $ids
     * @param array $chosenFields
     *
     * @return mixed
     */
    function torrentGet($allFields = false, array $ids = [], array $chosenFields = [])
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

        return $this->client->request("torrent-get", $payload);
    }

    function torrentAdd()
    {
    }

    function torrentRemove()
    {
    }

    function torrentSetLocation()
    {
    }

    function torrentRenamePath()
    {
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
}
