<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13/11/2017
 * Time: 17:38
 */

namespace Game\Models;


class Pioche extends \Illuminate\Database\Eloquent\Model {

    protected $table = 'pioche';
    protected $primaryKey = 'idPioche';
    public $timestamps = false;

    function cartes(){

        return $this->belongsToMany('Game\Models\Carte','contient','idPioche','idCarte');
    }
    function partie(){
        return $this->belongsTo('Game\Models\Partie',"idPartie");
    }

    function defausse(){
        return $this->hasMany('Game\Models\Defausse',"idPioche");
    }


}