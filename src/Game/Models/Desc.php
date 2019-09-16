<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13/11/2017
 * Time: 17:38
 */

namespace Game\Models;


class Desc extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'descPartie';
    protected $primaryKey = 'idpartie, date';
    public $timestamps = false;

}