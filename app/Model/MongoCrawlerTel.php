<?php


namespace App\Model;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoCrawlerTel extends Model
{
    protected $collection = 'crawler_tels';

    protected $connection = 'mongodb_self';

    protected $guarded = [];
}