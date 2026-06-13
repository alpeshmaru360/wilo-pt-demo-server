<?php

namespace App\Helpers;

use App\User;
use DB;
use Carbon\Carbon;
use App\Role;
use App\FavouriteOffer;
use App\Notifications;
use App\Models\Company;
use App\Models\CompanyOffer;
use Auth;
use App\CompanyOfferRenewRequest;
use App\Country;

Class Helper {

    public static function getUserRole() {
        $role = \DB::table('role_user')
                ->where('role_user.user_id', '=', Auth::user()->id)
                ->join('roles', 'role_user.role_id', '=', 'roles.id')
                ->select('roles.title as title')
                ->first();
        return $role->title;
    }

    public static function generateArticleNumber($cpId) {

        $lastCount = $cpId;
        $code = 0;
        $digitLength = strlen((string) $lastCount);
        if ($digitLength == 1) {
            $numberId = $lastCount;

            $code = '000000' . '' . $numberId;
        } else if ($digitLength == 2) {
            $numberId = $lastCount + 1;
            $code = '00000' . '' . $numberId;
        } else if ($digitLength == 3) {
            $numberId = $lastCount + 1;
            $code = '0000' . '' . $numberId;
        } else if ($digitLength == 4) {
            $numberId = $lastCount + 1;
            $code = '000' . '' . $numberId;
        } else if ($digitLength == 5) {
            $numberId = $lastCount + 1;
            $code = '00' . '' . $numberId;
        } else if ($digitLength == 6) {
            $numberId = $lastCount + 1;
            $code = '0' . '' . $numberId;
        } else if ($digitLength == 7) {
            $numberId = $lastCount + 1;
            $code = '' . '' . $numberId;
        }
        return $code;
    }
    
    
    public static function atmosGenerateArticleNumber($cpId) {

        $lastCount = $cpId;
        $code = 0;
        $digitLength = strlen((string) $lastCount);
        if ($digitLength == 1) {
            $numberId = $lastCount;

            $code = '000000' . '' . $numberId;
        } else if ($digitLength == 2) {
            $numberId = $lastCount + 1;
            $code = '00000' . '' . $numberId;
        } else if ($digitLength == 3) {
            $numberId = $lastCount + 1;
            $code = '0000' . '' . $numberId;
        } else if ($digitLength == 4) {
            $numberId = $lastCount + 1;
            $code = '000' . '' . $numberId;
        } else if ($digitLength == 5) {
            $numberId = $lastCount + 1;
            $code = '00' . '' . $numberId;
        } else if ($digitLength == 6) {
            $numberId = $lastCount + 1;
            $code = '0' . '' . $numberId;
        } else if ($digitLength == 7) {
            $numberId = $lastCount + 1;
            $code = '' . '' . $numberId;
        }
        return $code;
    }
    /**
     * Converts numbers in string from western to eastern Arabic numerals.
     *
     * @param  string $str Arbitrary text
     * @return string Text with western Arabic numerals converted into eastern Arabic numerals.
     */
    public static function arabic_w2e($str) {
        $arabic_eastern = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
        $arabic_western = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        return str_replace($arabic_western, $arabic_eastern, $str);
    }

    /**
     * Converts numbers from eastern to western Arabic numerals.
     *
     * @param  string $str Arbitrary text
     * @return string Text with eastern Arabic numerals converted into western Arabic numerals.
     */
    public static function arabic_e2w($str) {
        $arabic_eastern = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
        $arabic_western = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        return str_replace($arabic_eastern, $arabic_western, $str);
    }

    public static function isFavouriteOffer($offerId) {
        $userId = Auth::user()->id;
        $favourite = FavouriteOffer::where('offer_id', $offerId)->where('user_id', $userId)->count();

        return $favourite;
    }

    public static function getNotificationCount() {
        $userId = Auth::user()->id;
        $notifications = Notifications::where('user_id', $userId)->where('status', 'unread')->count();

        return $notifications;
    }

    public static function getNotification() {
        $userId = Auth::user()->id;
        $notifications = Notifications::where('user_id', $userId)->orderBy('id', 'DESC')->get();

        return $notifications;
    }

    public static function isRenewOfferById($id) {

        $companyOffer = CompanyOffer::where('id', $id)->where('validate_date', '>', date('Y-m-d'))->get();
        $flag = 0;
        if (isset($companyOffer[0]->remark_status)) {
            if ($companyOffer[0]->remark_status == '' || $companyOffer[0]->remark_status == "Rejected") {
                $date1 = date('Y-m-d');
                $date2 = $companyOffer[0]->validate_date;
                $date1_ts = strtotime($date1);
                $date2_ts = strtotime($date2);
                $diff = $date2_ts - $date1_ts;
                $dateDiff = round($diff / 86400);
                if ($dateDiff > 0 && $dateDiff <= 90) {
                    $companyOfferRenewRequest = CompanyOfferRenewRequest::where('offer_id', $id)->where('status', 'Pending')->count();

                    if ($companyOfferRenewRequest == 0) {
                        $flag = 1;
                    }
                }
            }
        }


        return $flag;
    }

    public static function renewOfferNotifications() {

        $companyId = Company::select('id')->where('user_id', Auth::user()->id)->get()[0]->id;

        $from = Carbon::now()->format('Y-m-d');
        $to = Carbon::parse(date('y-m-d', strtotime('+ 90 days')))->format('Y-m-d');
//        echo 'from '. $from. 'to'.$to;
//        exit;
//         DB::enableQueryLog(); 
//        $companyOffer = CompanyOffer::whereDate('validate_date', '>=', $from)
//                ->whereDate('validate_date', '<=', $to)
//                ->where('status', 'Approved')
//                ->where('company_id', $companyId)
//                ->orWhere('remark_status', 'Rejected')
//                ->orWhere('remark_status', '')
//                ->get();
        $companyOffer = DB::select("select * from `company_offers` where date(`validate_date`) >= '" . $from . "' and date(`validate_date`) <= '" . $to . "' and `status` = 'Approved' and `company_id` = $companyId and ( `remark_status` = 'Rejected' or `remark_status` = '' or `remark_status` IS NULL)");
        $records = array();
        foreach ($companyOffer as $val) {
            $isCompanyOfferRenew = CompanyOfferRenewRequest::where('status', 'Pending')->where('offer_id', $val->id)->count();
//            dd($isCompanyOfferRenew);
            if ($isCompanyOfferRenew == 0) {

                $records[] = array(
                    'id' => $val->id,
                    'title' => $val->title,
                    'title_ar' => $val->title_ar,
                    'validate_date' => $val->validate_date
                );
            }
//            if(CompanyOfferRenewRequest::where('status','Pending'))
        }

//          $sql = "select  company_offers.*,renew.* from  company_offers inner join company_offer_renew_request  renew on renew.offer_id = company_offers.id  
//where date(company_offers.validate_date) >= '" . $from . "' and date(company_offers.validate_date) <= '" . $to . "'
// and company_offers.status = 'Approved' and company_offers.company_id = $companyId 
// and( company_offers.remark_status = 'Rejected' or company_offers.remark_status = '') 
// and (renew.status !='Pending')";
//        $companyOffer = DB::select($sql);
//dd(DB::getQueryLog()); 
//        dd($records);
        return $records;
    }

    public static function arabicStatus($string) {

        switch ($string) {
            case 'Approved':
                $string = 'وافق';
                break;
            case 'Rejected':
                $string = 'مرفوض';
                break;
            case 'Pending':
                $string = 'قيد الانتظار';
                break;

            default:
                $string = '';
        }
        return $string;
    }
	
	public static function country_name(){
        $country = "other";
        if(auth()->user()){
            $country_id = auth()->user()->country_id;
            if($country_id != null){
                $country = Country::find($country_id);
                $country = $country->country;
            }
            if($country != null)
            {
                if($country == "ksa"){
                    $country = "ksa";
                }   
            }
        }
        return $country;
    }

}
