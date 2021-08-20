<?php

namespace App\Helper;

class Paginator
{

    private $limit;
    private $offset;
    private $page;

    public function __construct($page, $recordsPerPage)
    {
        $this->limit = $recordsPerPage;

        $cleanPage = filter_var($page, FILTER_VALIDATE_INT, [
            'options' => [
                'default' => 1,
                'min_range' => 1
            ]
        ]);

        $this->page = $cleanPage;

        $this->offset = $recordsPerPage * ($cleanPage - 1);
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function getPrev()
    {
        if ($this->getPage() > 1) {
            return $this->page - 1;
        }
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getNext()
    {
        return $this->page + 1;
    }
}
