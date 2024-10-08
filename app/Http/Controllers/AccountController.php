<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Main\Account;
use App\Models\User;

class AccountController extends Controller
{
    public function getAccountsForEmail(Request $request){

        $email = $request->searchEmail;

        $user = User::where('email', $email)->first();

        if (!isset($user)) {
            $error = 'Introduzca un email valido.';
            return response()->json(['error' => $error]);
        }

        $allowedAccountIds = isset($user) ? explode(",", $user->allowed_account_ids) : null;
        $allowedAccountIds = array_filter($allowedAccountIds, function($value) {
            return !empty($value);
        });
        if (empty($allowedAccountIds)) {
            $allowedAccountIds = null;
        }
        $isSuperUser = isset($user) ? $user->is_superuser : null;

        if(isset($allowedAccountIds) && !empty($allowedAccountIds[0])) {
            $accounts = Account::select(array('id','name'))->whereIn('id', $allowedAccountIds)->get();
        }elseif ((!isset($allowedAccountIds) || empty($allowedAccountIds[0])) && $isSuperUser) {
            $accounts = Account::select(array('id','name'))->get();
        } else {
            $accountSingle = Account::select(array('id','name'))->where('id', $user->account_id)->first();
            $accounts = array($accountSingle);
        }

        $accounts = isset($accounts) ? $accounts : null;
        $error = isset($error) ? $error : null;

        return response()->json(['accounts' => $accounts, 'error' => $error]);
    }
}
