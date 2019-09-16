<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13/11/2017
 * Time: 17:38
 */

namespace Game\Models;


class Partie extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'partie';
    protected $primaryKey = 'id';
    public $timestamps = false;

    function joueurs(){
        return $this->belongsToMany('Game\Models\User','joueur','id_partie','id_user');
    }

    function players(){
        return $this->hasMany('Game\Models\Joueur','id_partie');
    }

    function auteur(){
        return $this->belongsTo('Game\Models\User','createur');
    }

    function winner(){
        return $this->belongsTo("Game\Models\User","idgagnant");
    }
    function entrainDeJouer(){
        return $this->belongsTo("Game\Models\User","joue");
    }

    function descript(){
        return $this->hasMany('Game\Models\Desc','idPartie');
    }

}