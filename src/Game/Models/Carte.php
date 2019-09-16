<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13/11/2017
 * Time: 17:38
 */

namespace Game\Models;


class Carte extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'carte';
    protected $primaryKey = 'id';
    public $timestamps = false;

}