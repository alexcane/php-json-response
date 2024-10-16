<?php

namespace PhpJsonResp;

class JsonResp
{
    private array $_data = [];
    private string $_status = 'error';
    private array $_errMsg = [];
    private $_response;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->setData($data);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->_data;
    }

    /**
     * Clear the data passed at the time of class instantiation.
     * @return void
     */
    public function clearData(): void
    {
        $this->_data = [];
    }

    /**
     * Process actions on $data iteration :
     * - trim() string variable
     * - true and false string replace as boolean
     * @param array $data
     */
    public function setData(array $data): void
    {
        $trimmedData = array();
        foreach($data as $k=>$v){
            if(is_string($v)) $v = trim($v);
            if('false'===$v) $v = false;
            if('true'===$v) $v = true;
            $trimmedData[$k] = $v;
        }
        $this->_data = $trimmedData;
    }

    /**
     * @param string $errMsg
     */
    public function addErrMsg(string $errMsg): void
    {
        $this->_errMsg[] = $errMsg;
    }

    /**
     * @param array $errMsg
     */
    public function setErrMsg(array $errMsg): void
    {
        $this->_errMsg = $errMsg;
    }

    /**
     * Check if response contains no error(s)
     * @return bool
     */
    public function isSuccess(): bool
    {
        if(empty($this->_errMsg)) return true;
        return false;
    }

    /**
     * Check if response contains error(s)
     * @return bool
     */
    public function isError(): bool
    {
        if(!empty($this->_errMsg)) return true;
        return false;
    }

    /**
     * Count number of error
     */
    public function errorCount(): int
    {
        return count($this->_errMsg);
    }

    /**
     * Clear the array of errors messages
     * @return void
     */
    protected function clearErrMsg(): void
    {
        $this->_errMsg = [];
    }

    public function getResponse()
    {
        return $this->_response;
    }

    public function setResponse($response): void
    {
        $this->_response = $response;
    }

    /**
     * @param bool $json
     * @return array|string
     */
    public function returnResponse(bool $json=false)
    {
        if(empty($this->_errMsg)) $this->_status = 'success';
        $res = [
            'status' => $this->_status,
            'error_msg' => $this->_errMsg,
            'data' => $this->_data,
            'response' => $this->_response,
        ];
        if(empty($res['data'])) unset($res['data']);
        if(empty($res['response'])) unset($res['response']);

        if($json) $res = $this->returnJson($res);
        return $res;
    }

    /**
     * json encode the response
     * @param array $res
     * @return string
     */
    private function returnJson(array $res): string
    {
        return json_encode($res);
    }
}