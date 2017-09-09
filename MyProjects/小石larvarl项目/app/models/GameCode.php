<?php
class GameCode extends Eloquent {

    protected $table = 'game_code';

    protected $primaryKey = 'code_id';

    protected function getDateFormat()
    {
        return 'U';
    }
}