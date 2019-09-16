<?php

namespace Game\Models;


class Contient extends \Illuminate\Database\Eloquent\Model {
    protected $table = 'contient';
    protected $primaryKey = 'idCarte,idPioche';
    public $timestamps = false;
}