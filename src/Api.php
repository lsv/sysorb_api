<?php
namespace Lsv\SysorbApi;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class Api
{

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var CookieJar
     */
    private $cookieJar;

    /**
     * @param string $baseurl
     * @param string $username
     * @param string $password
     * @param string $domain
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
     * @param Client $client
     * @return Api
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @param CookieJar $jar
     * @return Api
     */
    public function setCookieJar(CookieJar $jar)
    {
        $this->cookieJar = $jar;
        return $this;
    }

    /**
     * @return ServerEntity[]
     */
    public function parse()
    {
        $data = $this->login();

        $html = \phpQuery::newDocumentHTML($data);
        $servers = $html->find('table.optiontree > tr:not(:last)');
        $i = 0;

        $statusses = [];
        foreach($servers as $server) {
            if (++$i < 3) continue;

            $errorParsed = false;

            $tds = pq($server)->find('> td');
            $s = 0;

            $entity = new ServerEntity();
            foreach($tds as $td) {
                switch(++$s) {
                    case 3:
                        $link = pq($td)->find('a')->attr('href');
                        $entity->setName(pq($td)->text());
                        break;
                    case 5:
                        $entity->setNetworkStatus($this->parseImage(pq($td)));
                        if ($entity->getNetworkStatus() > ServerEntity::OK_STATUS && ! $errorParsed && isset($link)) {
                            $entity->setErrors($this->parseError($link));
                            $errorParsed = true;
                        }
                        break;
                    case 7:
                        $entity->setCheckinStatus($this->parseImage(pq($td)));
                        if ($entity->getCheckinStatus() > ServerEntity::OK_STATUS && ! $errorParsed && isset($link)) {
                            $entity->setErrors($this->parseError($link));
                            $errorParsed = true;
                        }
                        break;
                    case 9:
                        $entity->setAgentStatus($this->parseImage(pq($td)));
                        if ($entity->getAgentStatus() > ServerEntity::OK_STATUS && ! $errorParsed && isset($link)) {
                            $entity->setErrors($this->parseError($link));
                            $errorParsed = true;
                        }
                        break;
                    default:
                        continue;
                }
            }

            $statusses[] = $entity;

        }

        return $statusses;
    }

    /**
     * @param string $link
     * @return ErrorEntity[]
     */
    private function parseError($link)
    {
        $html = \phpQuery::newDocumentHTML($this->loadStatusPage($link));
        $trs = $html->find('table.checklisting > tr:not(:last)');
        $i = 0;

        $errors = [];
        foreach($trs as $tr) {
            if (++$i < 4) continue;

            $tds = pq($tr)->find('> td');
            $s = 0;
            foreach($tds as $td) {
                switch(++$s) {
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

    private function loadStatusPage($link)
    {
        $response = $this->client->post($this->url . '/' . $link, [
            'cookies' => $this->cookieJar,
        ]);
        return (string)$response->getBody();
    }

    private function parseImage(\phpQueryObject $image)
    {
        $src = $image->find('img')->attr('src');
        switch($src) {
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
