<?php

namespace App;

use Carbon\Carbon;
use Hash;
use DB;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use SoftDeletes, Notifiable;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
        'email_verified_at',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token',
        'country_id',
        'email_verified_at',
        'atmos_access',
        'scp_access',
        'scpv_access',
        'fire_fighting_access',
        'booster_access',
        'sch_access',
        'control_panel_access',
    ];

    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function isAdmin() {
        return $this->roles()->where('title', 'Admin')->exists();
    }

    public function isUser() {
        return $this->roles()->where('title', 'User')->exists();
    }

    public function isSupervisor() {
        return $this->roles()->where('title', 'Supervisor')->exists();
    }

    public function getAdmin(){
        return $this->whereHas('roles', function($q){$q->whereIn('title', ['Admin']);})->get();
    }

    public function isSubadmin() {
        return $this->roles()->where('title', 'Subadmin')->exists();
    }

    public function isNotSubadmin() {
        return $this->roles()->whereNotIn('title', ['Subadmin'])->exists();
    }

    public function isAgent() {
        return $this->roles()->where('title', 'Agent')->exists();
    }

    public function isNotAgent() {
        return $this->roles()->whereNotIn('title', ['Agent'])->exists();
    }

    public function news()
    {
        $this->hasMany('App\Models\News', 'user_id', 'id');
    }

    protected function ic_margin_booster(){
        return DB::table('ic_margin')->where('country_id',auth()->user()->country_id)->where('part_id',1)->pluck('value')[0];
    }
    protected function ic_margin_control_panel(){
        return DB::table('ic_margin')->where('country_id',auth()->user()->country_id)->where('part_id',2)->pluck('value')[0];
    }
    protected function ic_margin_scp(){
        return DB::table('ic_margin')->where('country_id',auth()->user()->country_id)->where('part_id',3)->pluck('value')[0];
    }
    // A Code: 04-11-2025 Start
    protected function ic_margin_scpv(){
        return DB::table('ic_margin')->where('country_id',auth()->user()->country_id)->where('part_id',6)->pluck('value')[0];
    }
    // A Code: 04-11-2025 End
    protected function ic_margin_atmos(){
        return DB::table('ic_margin')->where('country_id',auth()->user()->country_id)->where('part_id',4)->pluck('value')[0];
    }
	protected function ic_margin_fire_fighting(){
		return DB::table('ic_margin')->where('country_id',auth()->user()->country_id)->where('part_id',5)->pluck('value')[0];
	}

    protected function otp_margin_atmos(){
        if(auth()->user()){
            return DB::table('otp_margin')->where('country_id',auth()->user()->country_id)->where('part_id',4)->pluck('value')[0];
        }
        else{
            return DB::table('otp_margin')->where('country_id','5')->where('part_id',4)->pluck('value')[0];
        }
    }
}