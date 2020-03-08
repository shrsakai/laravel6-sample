<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Class TaskUpdateRequest
 * @package App\Http\Requests
 * @see https://readouble.com/laravel/5.6/ja/validation.html#form-request-validation
 */
class TaskUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // ここで権限チェックは行わない
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @see https://readouble.com/laravel/5.6/ja/validation.html#available-validation-rules
     */
    public function rules()
    {
        return [
            'task'       => 'required|max:255',
            'status_id'  => 'required|exists:task_statuses,id', // task_statuses.id 存在チェック
            'scope_id'   => 'required|exists:task_scopes,id', // task_scopes.id 存在チェック
            'files.*.id' => 'required|exists:task_files,id', // task_files.id 存在チェック
        ];
    }

    public function messages()
    {
        return [
            //// 標準的なバリデーションの場合 message は不要
            // 'task.required'     => ':attributeは必須入力です。',
            // 'task.max'          => ':attributeは最大255文字です。',
            // 'status_id.integer' => ':attributeは整数入力です。',
            // 'scope_id.integer'  => ':attributeは整数入力です。',
        ];
    }

    public function attributes()
    {
        return [
            'task'       => 'Task',
            'status_id'  => 'ステータスID',
            'scope_id'   => '公開範囲ID',
        ];
    }

    /**
     * Afterフックを追加する場合
     *
     * @param Validator $validator
     */
    protected function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            // do nothing
        });
    }
}
