<?php
/**
 * Created by PhpStorm.
 * User: quent
 * Date: 12/01/2017
 * Time: 22:35
 */

namespace Game\Models;


class Message extends \Illuminate\Database\Eloquent\Model
{

    protected $table = 'message';
    protected $primaryKey = 'id';
    public $timestamps = false;
}