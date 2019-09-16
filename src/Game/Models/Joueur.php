<?php

namespace Game\Models;


class Joueur extends \Illuminate\Database\Eloquent\Model {
    protected $table = 'joueur';
    protected $primaryKey = 'id_partie,id_user';
    public $timestamps = false;
    function User(){
        return $this->belongsTo('\Game\Models\User','id_user');
    }
    function Partie(){
        return $this->belongsTo('\Game\Models\Partie','id_partie');
    }
}