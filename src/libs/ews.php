<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class EWS_Error extends Exception {

}

class EWS_Result implements Serializable {

    private $result;
    private $count;

    public function __construct($ret) {
        $this->result = $ret['result'];
        $this->count = isset($ret['count']) ? $ret['count'] : 1;
    }

    public function serialize() {
        return json_encode(array(
            'result' => $this->result,
            'count' => $this->count,
        ));
    }

    public function unserialize($serialized) {
        $decode = json_decode($serialized);
        $this->result = $decode['result'];
        $this->count = $decode['count'];
    }

    public function getResult() {
        return $this->result;
    }

    public function getCount() {
        return $this->count;
    }

}

class EWS {

    private $url;

    public function __construct($URL) {
        $this->url = $URL;
    }

    public function apiCall($apiName, $apiArgs = NULL) {
        if ($apiArgs === NULL) {
            $apiArgs = array();
        }
        $apiArgs['cmd'] = $apiName;

        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $apiArgs);
        $ret = json_decode(curl_exec($ch), TRUE);

        if ($ret === NULL) {
            throw new Exception("Communication error", 10);
        } elseif (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200 || $ret['isError']) {
            throw new EWS_Error($ret['errorMsg'], $ret['errorCode']);
        } else {
            return new EWS_Result($ret);
        }
    }

    public function getList() {
        return $this->apiCall('getList');
    }

    public function addTournament($tag, $name, $page, $date) {
        return $this->apiCall('addTournament', array(
                    'tag' => $tag,
                    'name' => $name,
                    'page' => $page,
                    'date' => $date,
        ));
    }

    public function delTournament($id) {
        return $this->apiCall('delTournament', array(
                    'id' => $id,
        ));
    }

    public function getTournament($id) {
        return $this->apiCall('getTournament', array(
                    'id' => $id,
        ));
    }

    public function lockTournament($id, $state) {
        return $this->apiCall('lockTournament', array(
                    'id' => $id,
                    'state' => strtoupper($state),
        ));
    }

    public function getUsers($id, $order) {
        return $this->apiCall('getUsers', array(
                    'id' => $id,
                    'order' => $order,
        ));
    }

}
