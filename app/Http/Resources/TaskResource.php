<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\ApiResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/**
 * Class TaskResource
 * @package App\Http\Resources
 */
class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'task'             => $this->task,
            'owner'            => $this->user->only(['id', 'name']),
            'assignee'         => $this->assignedUser->only(['id', 'name']),
            'created_user'     => null,
            'modified_user'    => null,
            'scope_id'         => $this->task_scope_id,
            'status_id'        => $this->task_status_id,
            'created_ago'      => $this->created_at->diffForHumans(),
            'created_at'       => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'       => $this->updated_at->format('Y-m-d H:i:s'),
            'meta_information' => [
                'permissions' => [
                    'edit'   => (Auth::user()->can('update', $this->resource)) ? 'enabled' : 'hidden',
                    'delete' => (Auth::user()->can('delete', $this->resource)) ? 'enabled' : 'hidden',
                ],
            ]
        ];
    }
}
