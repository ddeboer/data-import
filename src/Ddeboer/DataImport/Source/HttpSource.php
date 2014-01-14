<?php

namespace Ddeboer\DataImport\Source;

class HttpSource extends StreamSource
{
    protected $username;
    protected $password;
    protected $isDownloaded = false;

    public function __construct($url, $username = null, $password = null)
    {
        parent::__construct($url);

        if ($username && $password) {
            $this->setAuthentication($username, $password);
        }
    }

    /**
     * Set HTTP authentication
     *
     * @param string $username Username
     * @param string $password Password
     */
    public function setAuthentication($username, $password)
    {
        $context = stream_context_create(
            array(
                'http' => array(
                    'header'  => "Authorization: Basic "
                        . base64_encode("{$this->username}:{$this->password}")
                )
            )
        );

        $this->setContext($context);
    }
}