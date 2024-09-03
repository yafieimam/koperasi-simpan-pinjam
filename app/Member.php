<?php

namespace App;

use App\Notifications\NewLoanNotification;
use App\Notifications\NewMemberBlastNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use NotificationChannels\OneSignal\OneSignalChannel;
use Carbon\Carbon;

class Member extends Model
{
    use SoftDeletes;
    protected $table='ms_members';
    protected $dates=['dob','join_date','start_date','end_date','verified_at'];
    protected $casts = [
		'is_active'=> 'boolean',
		'join_date'=> 'date:Y-m-d',
		'end_date' => 'date:Y-m-d',
        'status' => 'string',
    ];
    protected $appends = ['full_name'];

    protected $fillable = [
        'email', 'first_name', 'region_id', 'user_id', 'position_id', 'is_active', 'status'
    ];

    public function getFullNameAttribute()
    {
        $firstName ='';
        $lastName = '';
        if(isset($this->attributes['first_name']))
        {
            $firstName = $this->attributes['first_name'];
        }
        if(isset($this->attributes['last_name']))
        {
            $lastName = $this->attributes['last_name'];
        }
        return $firstName.' '.$lastName;
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function comrade()
    {
        $project = $this->project;
        return Member::where('id','<>', $this->id)->fComrade($project);
    }

    public function scopeFComrade($q, $project)
    {
        return $q->whereHas('project', function($q1) use($project){
            $q1->whereHas('region', function($q2) use($project){
                $q2->where('id', $project->region_id);
            });
        });
    }

    public function superior()
    {
        return Member::where('id','<>', $this->id)->fDansekOnly()->fComrade($this->project);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFDansekOnly($q)
    {
//        return $q->whereNotNull('id');
        return $q->whereHas('user', function($query){
            $query->fDansekOnly();
        });
    }

    public function isActive()
    {
        return $this->status;
    }

    public function scopeFActive($q)
    {
        return $q->where('status', 'aktif');
    }

    public function bank()
    {
        return $this->hasMany(Bank::class);
    }

    public function getFullName()
	{
		return $this['fullname'] = $this->first_name .' '. $this->last_name;
	}

    public function scopeFVerified($q)
    {
        return $q->whereNotNull('verified_at');
	}

	public function pokok()
	{
		return $this->hasOne('App\ConfigDepositMembers')->where('config_deposit_member.type', '=', 'pokok');
	}

	public function deposit()
    {
        return $this->hasMany(DepositTransaction::class);
	}

    public function depositPokok()
	{
		return $this->hasMany(DepositTransaction::class)->where('ts_deposits.status', '=', 'paid')
			->leftJoin('ts_deposit_details', 'ts_deposits.id', '=', 'ts_deposit_details.transaction_id')
			->where('ts_deposit_details.deposits_type', '=', 'pokok');
	}

	public function depositWajib()
	{
		return $this->hasMany(DepositTransaction::class)->where('ts_deposits.status', '=', 'paid')
			->leftJoin('ts_deposit_details', 'ts_deposits.id', '=', 'ts_deposit_details.transaction_id')
			->where('ts_deposit_details.deposits_type', '=', 'wajib');
	}

	public function depositSukarela()
	{
		return $this->hasMany(DepositTransaction::class)->where('ts_deposits.status', '=', 'paid')
            ->where('ts_deposits.type', '=', 'debit')
			->leftJoin('ts_deposit_details', 'ts_deposits.id', '=', 'ts_deposit_details.transaction_id')
			->where('ts_deposit_details.deposits_type', '=', 'sukarela');
	}

    public function pencairanSukarela()
	{
		return $this->hasMany(DepositTransaction::class)->where('ts_deposits.status', '=', 'paid')
            ->where('ts_deposits.type', '=', 'credit')
			->leftJoin('ts_deposit_details', 'ts_deposits.id', '=', 'ts_deposit_details.transaction_id')
			->where('ts_deposit_details.deposits_type', '=', 'sukarela');
	}

	public function depositLainlain()
	{
		return $this->hasMany(DepositTransaction::class)->where('ts_deposits.status', '=', 'paid')
			->leftJoin('ts_deposit_details', 'ts_deposits.id', '=', 'ts_deposit_details.transaction_id')
			->where('ts_deposit_details.deposits_type', '=', 'lain-lain');
	}

	public function loanBelumLunas()
	{
		return $this->hasMany(TsLoans::class)->where('ts_loans.status', '=', 'belum lunas');
	}

	public function detailloanBelumLunas()
	{
		return $this->hasMany(TsLoans::class)->where('ts_loans.status', '=', 'belum lunas')
			->leftJoin('ts_loan_details', 'ts_loans.id', '=', 'ts_loan_details.loan_id');
	}

	public function pencairan_simpanan()
	{
		return $this->belongsTo('App\PencairanSimpanan');
	}

	public function project()
	{
		return $this->belongsTo('App\Project');
	}

	public function hasProject()
    {
        return $this->project()->count() > 0;
    }

	public function plafon()
	{
		return $this->belongsTo('App\MemberPlafon');
	}

	public function ts_loans()
    {
        return $this->hasMany(TsLoans::class);
	}

	public function isVerified()
    {
        return $this->verified_at !== null && $this->verified_at->lte(now());
    }
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function configDeposit()
    {
        return $this->hasOne(ConfigDepositMembers::class);
    }

	public function configDeposits()
	{
		return $this->hasMany(ConfigDepositMembers::class);
	}

    public function storeMonthlyDeposit()
    {
//        $ConfigDepositMembers = ConfigDepositMembers::where('member_id', '=', $member->id);
        $configDeposit = $this->configDeposit();
//        return $ConfigDepositMembers->get();
        // $total = $this->configDeposit()->select(\DB::raw('sum(value) as total'))->groupBy('member_id')->get();
        if($configDeposit->count() > 0){
            // $configDeposit = $configDeposit->get();
            $configDeposit = $configDeposit->whereIn('type', ['wajib', 'sukarela'])->get();
            $ts_deposits = null;
            // $ts_deposits                = new DepositTransaction();
            // $ts_deposits->member_id     = $this->id;
            // $ts_deposits->ms_deposit_id = 2;
            // $ts_deposits->deposit_number = rand();
            // $ts_deposits->total_deposit = $total[0]['total'];
            // $ts_deposits->status        = 'paid';
            // $ts_deposits->post_date     = new \DateTime();
            // $ts_deposits->save();
            foreach ($configDeposit as $depositMember) {
                $ts_deposits                = new DepositTransaction();
                $ts_deposits->member_id     = $this->id;
                $ts_deposits->ms_deposit_id = ($depositMember->type == "sukarela") ? 3 : 2;
                $ts_deposits->type = 'debit';
                $ts_deposits->deposits_type = $depositMember->type;
                $ts_deposits->deposit_number = rand();
                $ts_deposits->total_deposit = $depositMember->value;
                $ts_deposits->status        = 'paid';
                $ts_deposits->post_date     = new \DateTime();
                $ts_deposits->save();

                // $ts_deposit_detail                  = new DepositTransactionDetail();
                // $ts_deposit_detail->transaction_id  = $ts_deposits->id;
                // $ts_deposit_detail->deposits_type   = $depositMember->type;
                // $ts_deposit_detail->debit           = 0;
                // $ts_deposit_detail->credit          = $depositMember->value;
                // $ts_deposit_detail->total           = $depositMember->value;
                // $ts_deposit_detail->status          = 'paid';
                // $ts_deposit_detail->payment_date    = new \DateTime();
                // $ts_deposit_detail->save();

                $ts_deposit_detail                  = new DepositTransactionDetail();
                $ts_deposit_detail->transaction_id  = $ts_deposits->id;
                $ts_deposit_detail->deposits_type   = $depositMember->type;
                $ts_deposit_detail->debit           = $depositMember->value;
                $ts_deposit_detail->credit          = 0;
                $ts_deposit_detail->total           = $depositMember->value;
                $ts_deposit_detail->status          = 'paid';
                $ts_deposit_detail->payment_date    = new \DateTime();
                $ts_deposit_detail->save();

                $totalDepositMember = TotalDepositMember::where([
                    'member_id' => $this->id,
                    'ms_deposit_id' => $ts_deposits->ms_deposit_id
                ])->first();

                if(isset($totalDepositMember)){
                    $value = $totalDepositMember['value'] + $depositMember->value;
                    $totalDepositMember->value = $value;
                    $totalDepositMember->save();
                }else{
                    $totalDepositMember = new TotalDepositMember();
                    $totalDepositMember->member_id = $this->id;
                    $totalDepositMember->ms_deposit_id = $ts_deposits->ms_deposit_id;
                    $totalDepositMember->value = $depositMember->value;
                    $totalDepositMember->save();
                }
            }
            return $ts_deposits;
        }
        return false;
    }

    public function storeMonthlyDepositManually($tanggal)
    {
//        $ConfigDepositMembers = ConfigDepositMembers::where('member_id', '=', $member->id);
        $configDeposit = $this->configDeposit();
        // dd($configDeposit);
//        return $ConfigDepositMembers->get();
        if($configDeposit->count() > 0){
            $configDeposit = $configDeposit->whereIn('type', ['wajib', 'sukarela'])->get();
            $ts_deposits = null;

            foreach ($configDeposit as $depositMember) {
                $ts_deposits                = new DepositTransaction();
                $ts_deposits->member_id     = $this->id;
                $ts_deposits->ms_deposit_id = ($depositMember->type == "sukarela") ? 3 : 2;
                $ts_deposits->type = 'debit';
                $ts_deposits->deposits_type = $depositMember->type;
                $ts_deposits->deposit_number = rand();
                $ts_deposits->total_deposit = $depositMember->value;
                $ts_deposits->status        = 'paid';
                $ts_deposits->post_date     = Carbon::createFromFormat('Y-m-d', $tanggal);
                // dd($ts_deposits);
                $ts_deposits->save();

                $ts_deposit_detail                  = new DepositTransactionDetail();
                $ts_deposit_detail->transaction_id  = $ts_deposits->id;
                $ts_deposit_detail->deposits_type   = $depositMember->type;
                $ts_deposit_detail->debit           = $depositMember->value;
                $ts_deposit_detail->credit          = 0;
                $ts_deposit_detail->total           = $depositMember->value;
                $ts_deposit_detail->status          = 'paid';
                $ts_deposit_detail->payment_date    = Carbon::createFromFormat('Y-m-d', $tanggal);
                $ts_deposit_detail->save();

                $totalDepositMember = TotalDepositMember::where([
                    'member_id' => $this->id,
                    'ms_deposit_id' => $ts_deposits->ms_deposit_id
                ])->first();

                if(isset($totalDepositMember)){
                    $value = $totalDepositMember['value'] + $depositMember->value;
                    $totalDepositMember->value = $value;
                    $totalDepositMember->save();
                }else{
                    $totalDepositMember = new TotalDepositMember();
                    $totalDepositMember->member_id = $this->id;
                    $totalDepositMember->ms_deposit_id = $ts_deposits->ms_deposit_id;
                    $totalDepositMember->value = $depositMember->value;
                    $totalDepositMember->save();
                }
            }
            return $ts_deposits;
        }
        return false;
    }

    public static function getMemberArea($region){
        $selected = self::all();
        if(!empty($region)){
            $selected = self::whereHas('region', function ($query) {
                return $query->where('region_id', '=', auth()->user()->region['id']);
            })->get();
        }
        return $selected;
    }

    public static function getMemberAreaCount($region){
        $selected = self::whereHas('region');
        if(!empty($region)){
            $selected = self::whereHas('region', function ($query) {
                return $query->where('region_id', '=', auth()->user()->region['id']);
            });
        }
        return $selected;
    }

    public function newMemberBlastTo($users, $via = [OneSignalChannel::class])
    {
        foreach ($users as $user)
        {
            $user->notify(new NewMemberBlastNotification($this, $via));
        }
    }
}
