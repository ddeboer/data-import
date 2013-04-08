<?php

namespace Ddeboer\DataImport\Util;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
final class TempFile extends \SplFileObject
{
    public function __construct()
    {
        $file_name = tempnam(sys_get_temp_dir(), 'data-import');
        parent::__construct($file_name, 'a+');
    }
}
