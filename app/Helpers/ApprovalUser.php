<?php
namespace App\Helpers;
use App\Exceptions\ChangeConnectionException;

use App\GeneralSetting;
use App\Member;
use App\Position;
use App\Region;
use App\Resign;
use App\TsDeposits;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use function Psy\sh;

class ApprovalUser
{
    public static function getApproval($user){
        $approvals = [];
        if($user->hasRole('MEMBER')){
            $approvals = User::MemberApproval()->groupBy('position_id')->get();
        }

        // if($user->hasRole('KARYAWAN_PENGELOLA')){
        //     $approvals = User::MemberApproval($user)->groupBy('position_id')->get();
        // }

        // if($user->hasRole('DIREKTUR_UTAMA')){
        //     $approvals = User::MemberApproval($user)->groupBy('position_id')->get();
        // }

        // if($user->hasRole('KARYAWAN_KOPERASI')){
        //     $approvals = User::MemberApproval($user)->groupBy('position_id')->get();
        // }

        return $approvals;
    }

    public static function getPenjamin($user){
        $penjamin = [];
        if($user->hasRole('MEMBER')){
            $penjamin = User::MemberPenjamin()->get();
        }

        if($user->hasRole('DANSEK')){
            $penjamin = User::DansekPenjamin()->get();
        }

        if($user->hasRole('KARYAWAN_PENGELOLA')){
            $penjamin = User::KaryawanPengelolaPenjamin()->get();
        }

        if($user->hasRole('DIREKTUR_UTAMA')){
            $penjamin = User::DirekturPenjamin()->get();
        }

        if($user->hasRole('DIREKTUR')){
            $penjamin = User::DirekturPenjamin()->get();
        }

        if($user->hasRole('KARYAWAN_KOPERASI')){
            $penjamin = User::KaryawanKoperasiPenjamin()->get();
        }

        if($user->hasRole('ADMIN_KOPERASI')){
            $penjamin = User::AdminKoperasiPenjamin()->get();
        }

        if($user->hasRole('SUPERVISOR')){
            $penjamin = User::KaryawanKoperasiPenjamin()->get();
        }

        if($user->hasRole('PENGELOLA_AREA')){
            $penjamin = User::DansekPenjamin()->get();
        }

        if($user->hasRole('ADMIN_AREA')){
            $penjamin = User::DansekPenjamin()->get();
        }

        if($user->hasRole('GENERAL_MANAGER')){
            $penjamin = User::GeneralManagerPenjamin()->get();
        }

        if($user->hasRole('MANAGER')){
            $penjamin = User::ManagerPenjamin()->get();
        }

        if($user->hasRole('KOMISARIS')){
            $penjamin = User::GeneralManagerPenjamin()->get();
        }

        if($user->hasRole('PENGURUS_2') || $user->hasRole('PENGURUS_1')){
            $penjamin = User::PengurusPenjamin()->get();
        }

        if($user->hasRole('PENGAWAS_3') || $user->hasRole('PENGAWAS_2') || $user->hasRole('PENGAWAS_1')){
            $penjamin = User::PengawasPenjamin()->get();
        }

        return $penjamin;
    }
}
