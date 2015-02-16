<?php
/**
 * This file is part of the Lsv\SysorbApi
 */
namespace Lsv\SysorbApi;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

/**
 * Api for parsing sysorb information
 *
 * @author Martin Aarhof <martin.aarhof@gmail.com>
 */
class Api
{

    /**
     * Base url to sysorb
     * @var string
     */
    private $url;

    /**
     * Username to sysorb
     * @var string
     */
    private $username;

    /**
     * Password to sysorb
     * @var string
     */
    private $password;

    /**
     * Domain to sysorb
     * @var string
     */
    private $domain;

    /**
     * HTTP client
     * @var Client
     */
    private $client;

    /**
     * Cookie holder
     * @var CookieJar
     */
    private $cookieJar;

    /**
     * Construct
     *
     * @param string $baseurl : Baseurl to sysorb
     * @param string $username : Username to sysorb
     * @param string $password : Password to sysorb
     * @param string $domain : Domain to sysorb
     */
    public function __construct($baseurl, $username, $password, $domain)
    {
        $this->url = $baseurl;
        $this->username = $username;
        $this->password = $password;
        $this->domain = $domain;

        $this->client = new Client;
        $this->cookieJar = new CookieJar();
    }

    /**
     * Set another client
     *
     * @param Client $client : HTTP client to fetch the data
     * @return Api
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Set another cookie jar
     *
     * @param CookieJar $jar : Cookie jar to keep login when parsing a server
     * @return Api
     */
    public function setCookieJar(CookieJar $jar)
    {
        $this->cookieJar = $jar;
        return $this;
    }

    /**
     * Parse servers
     *
     * @return ServerEntity[]
     */
    public function parse()
    {
        $html = \phpQuery::newDocumentHTML($this->login());
        $trline = 0;
        $statusses = [];

        foreach ($html->find('table.optiontree > tr:not(:last)') as $server) {
            if (++$trline < 3) {
                continue;
            }

            $statusses[] = $this->parseServerLine($server);
        }

        return $statusses;
    }

    /**
     * Parse the server info line
     *
     * @param string $server : The HTML for the server status
     * @return ServerEntity
     */
    private function parseServerLine($server)
    {
        $tabletd = 0;
        $entity = new ServerEntity();
        foreach (pq($server)->find('> td') as $td) {
            switch (++$tabletd) {
                case 3:
                    $link = pq($td)->find('a')->attr('href');
                    $entity->setName(pq($td)->text());
                    break;
                case 5:
                    $entity->setNetworkStatus($this->parseImage(pq($td)));
                    break;
                case 7:
                    $entity->setCheckinStatus($this->parseImage(pq($td)));
                    break;
                case 9:
                    $entity->setAgentStatus($this->parseImage(pq($td)));
                    break;
                default:
                    continue;
            }
        }

        if (isset($link) && $entity->hasError()) {
            $entity->setErrors($this->parseError($link));
        }

        return $entity;
    }

    /**
     * Parse erorrs
     *
     * @param string $link : Link to the server information page
     * @return ErrorEntity[]
     */
    private function parseError($link)
    {
        $html = \phpQuery::newDocumentHTML($this->loadStatusPage($link));
        $trs = $html->find('table.checklisting > tr:not(:last)');
        $tabletd = 0;

        $errors = [];
        foreach ($trs as $tr) {
            if (++$tabletd < 4) {
                continue;
            }

            $tds = pq($tr)->find('> td');
            $s = 0;
            foreach ($tds as $td) {
                switch (++$s) {
                    default:
                        continue;
                    case 1:
                        $code = trim(pq($td)->find('table > tr > td:eq(2)')->text());
                        break;
                    case 2:
                        $message = trim(pq($td)->text());
                        break;
                    case 3:
                        $status = trim(pq($td)->text());
                        break;
                }
            }

            if (isset($code, $message, $status)) {
                $e = new ErrorEntity();
                $e
                    ->setCode($code)
                    ->setMessage($message)
                    ->setStatus($status)
                ;
                $errors[] = $e;
            }
        }

        return $errors;
    }

    /**
     * Do the login
     *
     * @return string
     */
    private function login()
    {
        $response = $this->client->post($this->url . '/index.cgi?path=1', [
            'cookies' => $this->cookieJar,
            'body' => [
                'USERNAME' => $this->username,
                'PASSWD' => $this->password,
                'TLD' => $this->domain,
                'ok' => ['x' => 28, 'y' => 5]
            ]
        ]);

        $body = (string)$response->getBody();
        return $body;
    }

    /**
     * Load the status page for the server with error or warning
     *
     * @param string $link : Link to the server information page
     * @return string
     */
    private function loadStatusPage($link)
    {
        $response = $this->client->post($this->url . '/' . $link, [
            'cookies' => $this->cookieJar,
        ]);
        return (string)$response->getBody();
    }

    /**
     * Parse the status icon
     *
     * @param \phpQueryObject $image : Get the status from a image src
     * @return int
     */
    private function parseImage(\phpQueryObject $image)
    {
        $src = $image->find('img')->attr('src');
        switch ($src) {
            default:
                return -1;
            case 'unknown_lamp.png':
                return ServerEntity::UNKNOWN_STATUS;
            case 'blank_lamp.png':
                return ServerEntity::BLANK_STATUS;
            case 'ok_lamp.png':
                return ServerEntity::OK_STATUS;
            case 'warning_lamp.png':
                return ServerEntity::WARNING_STATUS;
            case 'error_lamp.png':
                return ServerEntity::ERROR_STATUS;
        }
    }
}
