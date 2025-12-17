<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lawyer extends Model
{
    //
    protected $fillable = [
        'name',
        'grade',
        'city',
        'address',
        'email',
        'username',
        'password',
        'phone_1',
        'phone_2',
        'phone_3',
        'joined_at',
        'end_at',
        'note'
    ];
    public function contacts()
    {
        return $this->hasMany(LawyerContact::class);
    }

    public function attachments()
    {
        return $this->hasMany(LawyerAttachment::class);
    }
    public function legalcases()
    {
        return $this->belongsToMany(Legalcase::class, 'legalcase_lawyer');
    }

    public function hearings()
    {
        return $this->belongsToMany(Hearing::class, 'hearing_lawyer');
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
    public function authorizations()
    {
        return $this->hasMany(Authorization::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    public function revenueDistributionRules()
    {
        return $this->hasMany(RevenueDistributionRule::class, 'lawyer_id');
    }

}
