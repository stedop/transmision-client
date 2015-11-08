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
     * Allowed actions
     * Not implemented here are the Session actions, these to me should be in a TransmissionSession class
     *
     * @var array
     */
    public static $actions = [
        //ACTION REQUESTS
        "torrent-start",        // requires a $torrent_ids array or single id int
        "torrent-start-now",    // requires a $torrent_ids array or single id int
        "torrent-stop",         // requires a $torrent_ids array or single id int
        "torrent-verify",       // requires a $torrent_ids array or single id int
        "torrent-reannounce",    // requires a $torrent_ids array or single id int

        //MUTATORS
        "torrent-set",          // Arguments are in Transmission::$mutatorFields

        //ACCESSORS
        "torrent-get",          // Arguments are in Transmission::$basicGetFields and $extraGetFeidls

        //ADD TORRENT
        "torrent-add",

        //REMOVE TORRENT
        "torrent-remove",

        //MOVE TORRENT
        "torrent-set-location",

        //RENAME PATH
        "torrent-rename-path"
    ];

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
     * @param ClientAbstract $client
     */
    public function __construct(ClientAbstract $client)
    {
        $this->client = $client;
    }

    /**
     * @return array
     */
    public static function allGetFields()
    {
        return array_merge(Transmission::$basicGetFields, Transmission::$extraGetFields);
    }

    public function __call($name, $arguments)
    {

    }
}
