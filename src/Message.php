<?php

namespace NeoP\Http\Message;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use NeoP\Http\Message\Stream\Stream;

class Message implements MessageInterface
{
    protected $protocolVersion = "1.1";
    protected $headers = [];
    protected $body;

    function __construct() {
        $this->body = new Stream();
    }

    // Retrieve HTTP protocol version 1.0 or 1.1
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    // Returns new message instance with given HTTP protocol version	
    public function withProtocolVersion($version)
    {
        $this->protocolVersion = $version;
    }

    // Retrieve all HTTP Headers	Request Header List, Response Header List
    public function getHeaders()
    {
        return $this->headers;
    }

    // Checks if HTTP Header with given name exists
    public function hasHeader($name)
    {
        if (isset($this->headers[$name]))
            return true;
        return false;
    }
    
    // Retrieves a array with the values for a single header
    public function getHeader($name)
    {
        $name = strtolower($name);
        if ($this->hasHeader($name)) {
            return $this->headers[$name];
        }
        return NUll;
    }
    
    // Retrieves a comma-separated string of the values for a single header
    public function getHeaderLine($name)
    {
        $name = strtolower($name);
        if ($this->hasHeader($name)) {
            return implode(',', $this->headers[$name]);
        }
        return NUll;
    }
    
    // Returns new message instance with given HTTP Header
    public function withHeader($name, $value)
    {
        $name = strtolower($name);
        if (is_array($value)) {
            $this->headers[$name] = array_unique($value);
        } else {
            $this->headers[$name] = [$value];
        }
        return $this;
    }
    
    // Returns new message instance with given HTTP Header
    public function withHeaders(array $headers)
    {
        foreach ($headers as $name => $value) {
            $this->withHeader($name, $value);
        }
        return $this;
    }

    // if the header existed in the original instance, replaces the header 
    // value from the original message with the value provided when creating the new instance.
    // Returns new message instance with appended value to given header
    public function withAddedHeader($name, $value)
    {
        
        if (is_array($value)) {
            $values = array_unique($value);
            foreach($values as $value) {
                $this->headers[$name][] = $value;
            }
        } else {
            $this->headers[$name][] = $value;
        } 
        $this->headers[$name] = array_unique($this->headers[$name]);
        return $this;
    }

    // If header already exists value will be appended, if not a new header will be created
    // Removes HTTP Header with given name
    public function withoutHeader($name)
    {
        if ($this->hasHeader($name)) {
            unset($this->headers[$name]);
        }
        return $this;
    }

    // Retrieves the HTTP Message Body
    public function getBody()
    {
        return $this->body;
    }

    // Returns object implementing StreamInterface
    public function withBody(StreamInterface $body)
    {
        $this->body = $body;
        return $this;
    }
}