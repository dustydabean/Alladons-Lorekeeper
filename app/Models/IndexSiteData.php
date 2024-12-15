<?php

namespace App\Models\IndexSiteData;

class IndexSiteData extends Model {

    public function truncateDesc($string) {
        $desc = strip_tags($string);
        if (strlen($desc) > 1024) {
            $truncate = substr_replace($desc, '...', 1000);
        } else {
            $truncate = $desc;
        }
    }
}