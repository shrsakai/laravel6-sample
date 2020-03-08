<?php

namespace App\Http\Requests;

use App\Models\TaskScope;
use App\Models\TaskStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Class TaskStoreRequest
 * @package App\Http\Requests
 * @see https://readouble.com/laravel/5.6/ja/validation.html#form-request-validation
 */
class TaskStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
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
        ];
    }

    public function messages()
    {
        return [
            //// 標準的なバリデーションの場合 message は不要
            // 'task.required'     => ':attributeは必須入力です。',
            // 'task.max'          => ':attributeは最大255文字です。',
            // 'status.id.integer' => ':attributeは整数入力です。',
            // 'scope.id.integer'  => ':attributeは整数入力です。',
        ];
    }

    public function attributes()
    {
        return [
            'task'       => 'Task',
            'status_id'  => 'ステータス',
            'scope_id'   => '公開範囲',
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
            // 完了済みのTODO新規作成を抑止する
            if ($this->status_id == TaskStatus::COMPLETED) {
                $validator->errors()->add('status_id', '完了済みのTODOは登録できません');
            }
        });
    }
}
