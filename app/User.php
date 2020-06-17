<?php

namespace App;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Spatie\Permission\Traits\HasRoles;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use SoftDeletes, Authenticatable, Authorizable, HasRoles;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    protected $appends = ['role_title'];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'status'
    ];

    protected $hidden = [
        'password'
    ];

    protected $guarded = [
        'id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'first_name' => 'string',
        'last_name' => 'string',
        'email' => 'string',
        'status' => 'boolean'
    ];


    public function getNameAttribute() {
        return $this->first_name.' '.$this->last_name;
    }

    public function setPasswordAttribute($password) {
        $this->attributes['password'] = Hash::make($password);
    }

    public function photoUrl(array $attributes) {
        if ($this->photo_path) {
            return URL::to(App::make(Server::class)->fromPath($this->photo_path, $attributes));
        }
    }

    public function scopeOrderByName($query) {
        $query->orderBy('last_name')->orderBy('first_name');
    }

    public function scopeFilter($query, array $filters) {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        })->when($filters['role'] ?? null, function ($query, $role) {
            if ($role != 'all') {
                $query->whereRole($role);
            }
        })->when($filters['status'] ?? null, function ($query, $status) {
            if ($status === 'active') {
                $query->whereActive();
            } elseif ($status === 'inactive') {
                $query->whereInactive();
            }
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }

    public function getRoleTitleAttribute(){
        return $this->roles->first()->name;
    }

    public function scopeWhereRole($query, $role) {
        return $query->whereHas('roles', function ($q) use ($role){
            return $q->where('roles.name', $role);
        });
    }

    public function scopeWhereInactive($query) {
        return $query->where('status', 0);
    }

    public function scopeWhereActive($query) {
        return $query->where('status', 1);
    }
}
