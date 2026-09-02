<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Ticket extends \Coderflex\LaravelTicket\Models\Ticket implements HasMedia
{
    use InteractsWithMedia;


}
