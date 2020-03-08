<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserstampObserver
 * @package App\Observers
 */
class UserstampObserver
{

    /**
     * 新規作成時
     *
     * @param Model $model
     */
    public function creating(Model $model)
    {
        if (Auth::user() && Auth::user()->id) {
            $model->created_user_id = Auth::user()->id;
            $model->updated_user_id = Auth::user()->id;
        }
    }

    /**
     * 更新時
     *
     * @param Model $model
     */
    public function updating(Model $model)
    {
        if (Auth::user() && Auth::user()->id) {
            $model->updated_user_id = Auth::user()->id;
        }
    }
}
