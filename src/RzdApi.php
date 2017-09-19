<?php

namespace Rudnikov\RzdTicketsApi;

use Rudnikov\RzdTicketsApi\Http\RequestMethod;
use Rudnikov\RzdTicketsApi\Http\RzdClient;

class RzdApi
{
    public function searchTickets(TicketSearchOptions $options) {
        $client = new RzdClient();

        $queryParams = [
            'layer_id' => 5827,
        ];

        $params = [
            'tfl'        => 1,
            'st0'        => $options->getOriginCode(),
            'code0'      => $options->getOriginCode(),
            'st1'        => $options->getDestinationCode(),
            'code1'      => $options->getDestinationCode(),
            'dir'        => 0,
            'dt0'        => (new \DateTime($options->getDate()))->format('d.m.Y'),
            'dt1'        => (new \DateTime())->format('d.m.Y'),
            'checkSeats' => 1
        ];

        return $client->makeRequest(RequestMethod::POST, 'timetable/public/ru', $queryParams, $params);
    }

    public function getStationsByName(string $name)
    {
        $name = mb_strtoupper($name);

        $client = new RzdClient();

        $queryParams = [
            'stationNamePart' => mb_substr($name, 0, 2),
            'lat'             => 0,
            'compactMode'     => 'y',
            'lang'            => 'ru',
        ];

        $data = $client->makeRequest(RequestMethod::GET, 'suggester', $queryParams);

        $data = array_map(function (array $station) {
            return [
                'name' => $station['n'],
                'code' => $station['c'],
            ];
        }, $data);

        return array_filter($data, function(array $station) use ($name) {
           return mb_strpos($station['name'], $name) === 0;
        });
    }
}