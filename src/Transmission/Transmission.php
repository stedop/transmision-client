<?php namespace Playground\Transmission;

use Playground\Transmission\Clients\ClientAbstract;
use Playground\Framework\Piles\Pile;


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
    public static $fields = array(
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
    );

    /**
     * @var array
     */
    public static $basicFields = array(
        'id', 'name', 'addedDate', 'dateCreated', 'files', 'isFinished'
    );

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
    public static function allFields()
    {
        return array_merge(self::$basicFields, self::$fields);
    }

    /**
     * @param bool|false $sort
     * @param bool|false $getAllFields
     * @return Pile
     */
    public function getAll($sort=false, $getAllFields=false)
    {
        return $this->get([], $sort, $getAllFields);
    }

    /**
     * @param array $torrentList
     * @param bool|false $sort
     * @param bool|false $allFields
     * @return Pile
     */
    public function get(array $torrentList = [], $sort=false, $allFields=false)
    {
        ($allFields) ? $fields = Transmission::allFields() : $fields = Transmission::$basicFields;

        $jsonData = $this->generateGetJson($torrentList, $fields);

        $torrents = new Pile(json_decode(
            $this->client->request(
                "POST",
                $jsonData
            ),true)['arguments']['torrents']);

        $torrents = $this->calculateCompletion($torrents);
        $torrents->sort($sort);

        return $torrents;
    }

    /**
     * Generate the json payload for the get function
     *
     * @param array $torrentList
     * @param array $fields
     *
     * @return array
     */
    public function generateGetJson(array $torrentList = [], array $fields = [])
    {
        $jsonData = [
            'method' => 'torrent-get',
            'arguments'=> [
                'fields' => $fields
            ]
        ];

        if (count($torrentList) > 0) {
            if (count($torrentList) == 1)
                $jsonData['arguments']['ids'] = (int) $torrentList[0];
            else
                $jsonData['arguments']['ids'] = $torrentList;
        }

        return $jsonData;
    }

    /**
     * Calculates the completion percentages for the returned torrents
     *
     * @param Pile $torrents
     *
     * @return Pile
     */
    private function calculateCompletion(Pile $torrents)
    {
        foreach ($torrents as $torrent) {
            $totalLength = 0;
            $totalCompleted = 0;

            foreach ($torrent->files as $file) {
                $totalLength = $totalLength + $file->length;
                $totalCompleted = $totalCompleted + $file->bytesCompleted;
            }

            $torrent['completedPercentage'] = round(($totalCompleted/$totalLength) * 100, 2);
        }

        return $torrents;
    }

    /**
     * @param $magnet_uri
     * @param string $download_location
     */
    public function add($magnet_uri, $download_location = "") {

    }

    /**
     * @param $torrent_id
     */
    public function remove($torrent_id) {

    }
}
