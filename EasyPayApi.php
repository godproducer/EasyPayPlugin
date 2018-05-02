<?php

require_once __DIR__ . '/vendor/autoload.php';

class EasyPayApi {

    const base_url = 'https://api.easypay.ua/';
    const AppId = '3919c8fe-bf60-40db-b5c2-ae6d3a1cd37a';
    const UserAgent = 'okhttp/3.9.0';
    const PartnerKey = 'easypay-v2-android';

    private $RequestedSessionId;
    private $PageId;
    private $Last_error = '';
    private $User;
    private $Password;
    private $Access_token;
    private $Token_type;
    private $Expires;
    private $Refresh_token;
    private $UserId;
    private $ClientId;
    private $inIssued;
    private $inExpires;
    private $Wallets;

    public function __construct($pUser , $pPassword ) {
        $this->User = empty($pUser)? '380733247278' : $pUser ;
        $this->Password = empty($pPassword)? 'caac136541asD' : $pPassword ;
    }

    
    
    private function getWallets() {
        $result = false;
                try {
                    $vAuth = \sprintf('%s %s', $this->Token_type, $this->Access_token);
                    $client = new \GuzzleHttp\Client(['base_uri' => self::base_url]);
                    $vReqId = $this->getRequestedSessionId();
                    $vPageId = $this->getPageId();
                    $response = $client->request('GET', '/api/wallets/get', [
                        'headers' => ['User-Agent' => self::UserAgent, 'Accept' => 'application/json',
                            'AppId' => self::AppId, 'Authorization' => $vAuth,
                            'PartnerKey' => self::PartnerKey, 'RequestedSessionId' => $vReqId,
                            'PageId' => $vPageId, 'Locale' => 'Ua']]);
                    $code = $response->getStatusCode();
                    if ($code === 200) {
                            $this->processResponse($response);
                            $result = true;
                    }
                } catch (Exception $e) {
                    $this->Last_error = $e->getMessage();
                }
        return $result;
    }
    
    private function getToken() {
        $result = false;
        try {
            $payload = \sprintf('client_id=easypay-v2-android&grant_type=password&username=%s&password=%s', $this->User, $this->Password);
            $client = new \GuzzleHttp\Client(['base_uri' => self::base_url]);
            $vReqId = $this->getRequestedSessionId();
            $vPageId = $this->getPageId();
            $response = $client->request('POST', '/api/token', [
                'body' => $payload,
                'headers' => ['User-Agent' => self::UserAgent, 'Accept' => 'application/json',
                    'AppId' => self::AppId, 'No-Authentication' => true,
                    'PartnerKey' => self::PartnerKey, 'RequestedSessionId' => $vReqId,
                    'PageId' => $vPageId, 'Locale' => 'Ua']]);
            $code = $response->getStatusCode();
            if ($code === 200) {
                $this->processResponse($response);
                $result = true;
            }
        } catch (Exception $e) {
            $this->Last_error = $e->getMessage();  }
        return $result;
    }

    private function getSession() {
        $result = false;
        try {
            $client = new \GuzzleHttp\Client(['base_uri' => self::base_url]);
            $response = $client->request('POST', '/api/system/createSession', [
                'headers' => ['User-Agent' => self::UserAgent,
                    'Accept' => 'application/json', 'AppId' => self::AppId]]);
            $code = $response->getStatusCode();
            if ($code === 200) {
                $this->processResponse($response);
                $result = true;
            }
        } catch (Exception $e) {
            $this->Last_error = $e->getMessage();
        }
        return $result;
    }

    private function processResponse($response) {
        $json = $response->getBody();
        $data = \GuzzleHttp\json_decode($json,true);
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'requestedSessionId':
                    $this->RequestedSessionId = $value;
                    break;
                case 'pageId':
                    $this->PageId = $value;
                    break;
                case 'access_token':
                    $this->Access_token = $value;
                    break;
                case 'token_type':
                    $this->Token_type = $value;
                    break;
                case 'expires_in':
                    $this->Expires = $value;
                    break;
                case 'refresh_token':
                    $this->Refresh_token = $value;
                    break;
                case 'userId':
                    $this->UserId = $value;
                    break;
                case 'client_id':
                    $this->ClientId = $value;
                    break;
                case '.issued':
                    $this->inIssued = $value;
                    break;
                case '.expires':
                    $this->inExpires = $value;
                    break;
                case 'wallets':
                    $this->Wallets = $value;
                    break;
                default:
                    break;
            }
        }
    }
    
    public function actionGetWallets() {
        $result = false;
        if ($this->getSession()) {
            if ($this->getToken()) {
                $result = $this->getWallets();
            }
        }
        return $result;
    }

    public function renderGetWallets() {
        if ($this->actionGetWallets())
        {
            $vHtml = "<table border = \"1\" width = \"1\" cellspacing = \"3\" cellpadding = \"3\"><thead>";
            $vHtml .= "<tr><th>Тип кошелька</th><th>Название кошелька</th><th>Номер кошелька</th><th>Балланс кошелька</th></tr></thead><tbody>";
            $vRows = "";
            foreach ($this->Wallets as $value) {
                $vRows .= \sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',$value['walletType'] === 'Current' ? 'Основной': 'Дополнительный', $value['name'], $value['instrumentId'] , $value['balance'] );
            };
            $vHtml .= \sprintf('%s</tbody></table>',$vRows);
        }
        else {$vHtml = \sprintf("Произошла ошибка:%s", $this->getLast_error());};
        return $vHtml;
    }
    
    
    protected function getRequestedSessionId() {
        return $this->RequestedSessionId;
    }

    protected function getPageId() {
        return $this->PageId;
    }

    public function getLast_error() {
        return $this->Last_error;
    }

    private function setLast_error($Last_error) {
        $this->Last_error = $Last_error;
    }

}
