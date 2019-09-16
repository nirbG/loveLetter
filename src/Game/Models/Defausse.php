<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 27/11/2017
 * Time: 15:35
 */

namespace Game\Models;


class Defausse extends \Illuminate\Database\Eloquent\Model {
    protected $table = 'defausse';
    protected $primaryKey = 'idPioche,idCarte ';
    public $timestamps = false;

    function carte(){
        return $this->belongsTo('Game\Models\Carte','idCarte');
    }
    function pioche(){
        return $this->belongsTo('Game\Models\Pioche','idPioche');
    }
}