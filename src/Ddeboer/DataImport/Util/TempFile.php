<?php

namespace Ddeboer\DataImport\Util;

/**
 * Solves shortcomings with PHP's \SplTempFileObject, such as getPathname()
 * not working
 *
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class TempFile extends \SplFileObject
{
    protected $isLoaded = false;

    public function __construct()
    {
        $file_name = tempnam(sys_get_temp_dir(), 'data-import');
        parent::__construct($file_name, 'a+');
    }

    public function rewind()
    {
        $this->lazyLoad();

        parent::rewind();
    }

    public function getPathname()
    {
        $this->lazyLoad();

        return parent::getPathname();
    }

    protected function lazyLoad()
    {
        if (!$this->isLoaded) {
            $this->doLazyLoad();
            $this->isLoaded = true;
        }
    }

    /**
     * Template method that can be overridden to provide lazy load functionality
     */
    protected function doLazyLoad()
    {
    }
}
