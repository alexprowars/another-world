<?php

namespace Sky\Core\Prophiler\DataCollector;

use Sky\Core\Prophiler\DataCollectorInterface;

class Files implements DataCollectorInterface
{
    public function getTitle()
    {
        return 'Files';
    }

    public function getIcon()
    {
        return '<i class="fa fa-arrow-circle-o-down"></i>';
    }

    public function getData()
    {
        $data = [
            'Included files' => get_included_files(),
            'APC cache info' => apcu_cache_info(),
        ];

        return $data;
    }
}
