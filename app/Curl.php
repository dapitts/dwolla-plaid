<?php

namespace App;

class Curl
{
    private $ch;
    private $headers = [];

    public function __construct($method, $url, $headerFields, $postFields = null) {
        $this->ch = curl_init();

        switch ($method) {
            case 'POST':
                curl_setopt($this->ch, CURLOPT_POST, true);

                if ($postFields)
                    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postFields);
                break;
            case 'PUT':
                curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');

                if ($postFields)
                    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postFields);
                break;
            case 'DELETE':
                curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        if (is_array($headerFields))
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headerFields);

        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($this->ch, CURLOPT_HEADERFUNCTION,
            function($curl, $header) {
                $len = strlen($header);

                $header = explode(':', $header, 2);

                if (count($header) < 2) // ignore invalid headers
                    return $len;

                $name = strtolower(trim($header[0]));

                if (!array_key_exists($name, $this->headers))
                    $this->headers[$name] = [trim($header[1])];
                else
                    $this->headers[$name][] = trim($header[1]);

                return $len;
            }
        );
    }

    public function exec() {
        $response = app()->make('stdClass');

        if (($response->result = curl_exec($this->ch)) !== false) {
            $response->http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
            $response->headers = $this->headers;
        } else {
            $response->errno = curl_errno($this->ch);
            $response->error = curl_error($this->ch);
        }

        curl_close($this->ch);

        return $response;
    }
}