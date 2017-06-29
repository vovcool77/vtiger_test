<?php
class vtiger {

    protected $apiCredential = [];

    public function getApiInfo($userInfo, $crmUrl) {

        $headers = [
            "Content-Type: application/json",
            "Accept: application/json",
        ];

        return $this->request('GET', $headers, $crmUrl . '/webservice.php?operation=getchallenge&username=' . $userInfo['username']);
    }

    public function setApiCredential($apiCredential) {

        $this->apiCredential = $apiCredential;

    }

    public function login($userInfo, $crmUrl) {

        $headers = [
            "Content-Type: application/json",
            "Accept: application/json",
        ];

        $data = [
            "operation" => "login",
            "username" => $userInfo['username'],
            "accessKey" => md5($this->apiCredential['result']['token'] . $userInfo['accessKey']),
        ];
        $data = json_encode($data);

        $loginInfo = json_decode($this->request('POST', $headers, $crmUrl, $data));
        
        $this->apiCredential[] = [
            'sessionId' => $loginInfo['result']['sessionId'],
        ];

    }

    public function  vtiger($userInfo, $crmUrl) {

        $this->setApiCredential(json_decode($this->getApiInfo($userInfo, $crmUrl)));

        if (isset($this->apiCredential) && $this->apiCredential != 'false') {
            $this->login($userInfo, $crmUrl);

            $this->getInfoByTypes($crmUrl);

        }

    }

    public function request($type, $headers, $url, $data = NULL) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
//        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false); // required as of PHP 5.6.0

        if ($data != NULL) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        return curl_exec($ch);
    }

    public function getInfoByTypes($crmUrl) {

        $headers = [
            "Content-Type: application/json",
            "Accept: application/json",
        ];

        $types = json_decode($this->request('GET', $headers, $crmUrl . '/webservice.php?operation=listtypes&sessionName=' . $this->apiCredential['sessionId']));

        foreach ($types['result']['types'] as $type) {
            var_dump(json_decode($this->request('GET', $headers, $crmUrl . '/webservice.php?operation=describe&sessionName=' . $this->apiCredential['sessionId']) . '&elementType=' . $type));
        }

    }

}

$userInfo = [
    "username" => "vovcool11@gmail.com",
    "accessKey" => "KI4bLH5SVWpgofv",
];

$crmUrl = "https://company12425.od2.vtiger.com";

$task = new vtiger($userInfo, $crmUrl); ?>
