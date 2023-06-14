<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\HeaderBag;

class ApiResponser
{
    private $success = true;

    private $message = 'Request Successful.';

    private $payload = null;

    private $code;

    private $headers = [];

    public function __construct()
    {

    }

    /**
     * The operation was successful.
     *
     * @return ApiResponse
     */
    public function success()
    {
        $this->success = true;
        if (! $this->code) {
            $this->code = 200;
        }

        return $this;
    }

    /**
     * The operation was not successful, we are returning an error
     *
     * @return ApiResponse
     */
    public function failed()
    {
        $this->success = false;
        if (! $this->code) {
            $this->code = 500;
        }

        return $this;
    }

    /**
     * set the message to respond with
     *
     * @param  string  $message
     * @return ApiResponse
     */
    public function message($message = '')
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set the payload to the response
     *
     * @param $payload | The data to send to the client
     * @return ApiResponse
     */
    public function payload($payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * Set the response code according to the status of the response
     *
     * @param  int  $httpCode
     * @return ApiResponse
     */
    public function code($httpCode = 200)
    {
        $this->code = $httpCode;

        return $this;
    }

    /**
     * Set custom response headers if necessary
     *
     * @param  array|HeaderBag  $headers
     */
    public function headers($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    public function send()
    {
        $response = [
            'success' => $this->success,
            'message' => $this->message,
        ];
        if ($this->payload) {
            $response['payload'] = $this->payload;
        }

        return response()->json($response, $this->code)->withHeaders($this->headers);
    }

    public function fetchMessage($message_title, $count)
    {
        return $count ? $message_title.' fetched successfully' : 'no '.$message_title.' founded';
    }
}
