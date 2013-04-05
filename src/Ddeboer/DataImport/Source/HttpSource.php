<?php

namespace Ddeboer\DataImport\Source;

class HttpSource implements SourceInterface
{
    protected $filters = array();
    protected $username;
    protected $password;

    public function __construct($url, $username = null, $password = null)
    {
        $this->url = $url;
        if ($username && $password) {
            $this->setAuthentication($username, $password);
        }
    }

    public function setAuthentication($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     *
     * @return \SplFileObject
     */
    public function getFile()
    {
        $file = $this->downloadFile();
        foreach ($this->filters as $filter) {
            $file = $filter->filter($file);
        }

        return $file;
    }

    /**
     * Download the file from the internet to a temporary location
     *
     * @return \SplFileObject
     */
    public function downloadFile($target = null)
    {
        if (!$target) {
            $target = tempnam('/tmp', 'data_import');
        }

        if ($this->username && $this->password) {
            $context = stream_context_create(
                array(
                    'http' => array(
                        'header'  => "Authorization: Basic "
                            . base64_encode("{$this->username}:{$this->password}")
                    )
                )
            );
        }

        file_put_contents($target, file_get_contents($this->url));

        return new \SplFileObject($target);
    }

    public function addFilter($filter)
    {
        $this->filters[] = $filter;
    }
}
