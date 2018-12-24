<?php

namespace Main\Service\traits;

use Main\Service\DB;

trait DBTrait
{
    /** @var DB */
    protected $db;

    public function setDBService(DB $db)
    {
        $this->db = $db;
    }
}
