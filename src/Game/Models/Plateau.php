<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 22/11/2017
 * Time: 12:34
 */

namespace Game\Models;


class Plateau extends \Illuminate\Database\Eloquent\Model {
    protected$table = 'plateau';
    protected $primaryKey = 'idCarte,idJoueur ';
    public $timestamps = false;

    function Carte(){
        return $this->belongsTo('\Game\Models\Carte','idCarte');
    }
    function Joueur(){
        return $this->belongsTo('\Game\Models\User','idJoueur');
    }
}