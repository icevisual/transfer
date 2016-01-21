<?php
namespace App\Models;

class Transfer extends \Eloquent
{

    protected $table = 'transfer';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = false;

}