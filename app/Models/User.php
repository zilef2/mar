<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
	
	use HasApiTokens, HasFactory, Notifiable, HasRoles;
	use SoftDeletes;
	
	/*
	$user->delete();
	$user->restore();
	$user->forceDelete(); //Eliminar (Permanente)	Borra el registro de la base de datos de verdad.
	Incluir Eliminados	Incluye los registros eliminados suavemente en la consulta.	User::withTrashed()->get();
	Solo Eliminados	Consulta solo los registros que han sido eliminados suavemente.	User::onlyTrashed()->get();
	*/
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'id',
		'name',
		'email',
		'password',
		'identificacion',
		'celular',
		'sexo',
		'fecha_nacimiento',
		'salario',
		'cargo',
		'area',
	];
	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];
	
	public function getCreatedAtAttribute() {
		return date('d-m-Y H:i', strtotime($this->attributes['created_at']));
	}
	
	public function getUpdatedAtAttribute() {
		return date('d-m-Y H:i', strtotime($this->attributes['updated_at']));
	}
	
	public function getEmailVerifiedAtAttribute() {
		return $this->attributes['email_verified_at'] == null ? null : date('d-m-Y H:i', strtotime($this->attributes['email_verified_at']));
	}
	
	public function getPermissionArray() {
		return $this->getAllPermissions()->mapWithKeys(function ($pr) {
			return [$pr['name'] => true];
		});
	}
	
	public function reportes(): HasMany {
		return $this->HasMany(Reporte::class, 'user_id');
	}
	
	// public function actividad(): BelongsToMany { return $this->BelongsToMany(Actividad::class); }
	// public function ordenproduccion(): BelongsToMany { return $this->BelongsToMany(ordenproduccion::class); }
	// public function reproceso(): BelongsToMany { return $this->BelongsToMany(Reproceso::class); }
	
}
