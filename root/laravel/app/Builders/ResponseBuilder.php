<?php
namespace App\Builders;

class CustomResponse {
    function release(int $staus,$json){
       return response($json,$staus);
    }
}

interface ResponseBuilderInterface {
    function withLibrary(CustomResponse $response) : ResponseBuilderInterface;
    function addStatus(int $status) : ResponseBuilderInterface;
    function addData(mixed $data) : ResponseBuilderInterface;
    function release();
}

class ResponseBuilder implements ResponseBuilderInterface {
    private CustomResponse $res;
    private int $status = 200;
    private array $data = array(
        'response' => array()
    );
    function withLibrary(CustomResponse $response) : ResponseBuilder {
        $this->res = $response;
        return $this;
    }
    function addStatus(int $status): ResponseBuilder
    {
        $this->status = $status;
        return $this;
    }
    function addData(mixed $data): ResponseBuilder
    {
        $this->data['response'] = $data;
        return $this;
    }
    function release()
    {
        return $this->res->release($this->status,$this->data);
    }
}