<?php
namespace App\Repositories;

use App\Models\User;
use App\Models\Main\UserAccount;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use stdClass;

class AccountRepository
{
    public function findUserAccounts($userId1, $userId2 = false)
    {
        $realUserId = Session::get('real_userid');
        if(
            !$realUserId
            || !in_array($realUserId, [16,27,47,52,62,63,66,78,50,166,154,139])
        ){
            return false;
        }
        //PERMITIR MAS DE 5 TIENDAS O USUARIOS
        $query = UserAccount::where('user_id1', '=', $userId1)
            ->orWhere('user_id2', '=', $userId1)
            ->orWhere('user_id3', '=', $userId1)
            ->orWhere('user_id4', '=', $userId1)
            ->orWhere('user_id5', '=', $userId1)
            ->orWhere('user_id6', '=', $userId1)
            ->orWhere('user_id7', '=', $userId1)
            ->orWhere('user_id8', '=', $userId1)
            ->orWhere('user_id9', '=', $userId1)
            ->orWhere('user_id10', '=', $userId1)
            ->orWhere('user_id11', '=', $userId1)
            ->orWhere('user_id12', '=', $userId1)
            ->orWhere('user_id13', '=', $userId1)
            ->orWhere('user_id14', '=', $userId1)
            ->orWhere('user_id15', '=', $userId1)
            ->orWhere('user_id16', '=', $userId1)
            ->orWhere('user_id17', '=', $userId1)
            ->orWhere('user_id18', '=', $userId1)
            ->orWhere('user_id19', '=', $userId1)
            ->orWhere('user_id20', '=', $userId1)
            ->orWhere('user_id21', '=', $userId1)
            ->orWhere('user_id22', '=', $userId1)
            ->orWhere('user_id23', '=', $userId1)
            ->orWhere('user_id24', '=', $userId1)
            ->orWhere('user_id25', '=', $userId1)
            ->orWhere('user_id26', '=', $userId1)
            ->orWhere('user_id27', '=', $userId1)
            ->orWhere('user_id28', '=', $userId1)
            ->orWhere('user_id29', '=', $userId1)
            ->orWhere('user_id30', '=', $userId1)
            ->orWhere('user_id31', '=', $userId1)
            ->orWhere('user_id32', '=', $userId1)
            ->orWhere('user_id33', '=', $userId1)
            ->orWhere('user_id34', '=', $userId1)
            ->orWhere('user_id35', '=', $userId1)
            ->orWhere('user_id36', '=', $userId1)
            ->orWhere('user_id37', '=', $userId1)
            ->orWhere('user_id38', '=', $userId1)
            ->orWhere('user_id39', '=', $userId1)
            ->orWhere('user_id40', '=', $userId1)
            ->orWhere('user_id41', '=', $userId1)
            ->orWhere('user_id42', '=', $userId1)
            ->orWhere('user_id43', '=', $userId1)
            ->orWhere('user_id44', '=', $userId1)
            ->orWhere('user_id45', '=', $userId1)
            ->orWhere('user_id46', '=', $userId1)
            ->orWhere('user_id47', '=', $userId1)
            ->orWhere('user_id48', '=', $userId1)
            ->orWhere('user_id49', '=', $userId1)
            ->orWhere('user_id50', '=', $userId1)
            ->orWhere('user_id51', '=', $userId1)
            ->orWhere('user_id52', '=', $userId1)
            ->orWhere('user_id53', '=', $userId1)
            ->orWhere('user_id54', '=', $userId1)
            ->orWhere('user_id55', '=', $userId1)
            ->orWhere('user_id56', '=', $userId1)
            ->orWhere('user_id57', '=', $userId1)
            ->orWhere('user_id58', '=', $userId1)
            ->orWhere('user_id59', '=', $userId1)
            ->orWhere('user_id60', '=', $userId1)
            ->orWhere('user_id61', '=', $userId1)
            ->orWhere('user_id62', '=', $userId1)
            ->orWhere('user_id63', '=', $userId1)
            ->orWhere('user_id64', '=', $userId1)
            ->orWhere('user_id65', '=', $userId1)
            ->orWhere('user_id66', '=', $userId1)
            ->orWhere('user_id67', '=', $userId1)
            ->orWhere('user_id68', '=', $userId1)
            ->orWhere('user_id69', '=', $userId1)
            ->orWhere('user_id70', '=', $userId1)
            ->orWhere('user_id71', '=', $userId1)
            ->orWhere('user_id72', '=', $userId1)
            ->orWhere('user_id73', '=', $userId1)
            ->orWhere('user_id74', '=', $userId1)
            ->orWhere('user_id75', '=', $userId1)
            ->orWhere('user_id76', '=', $userId1)
            ->orWhere('user_id77', '=', $userId1)
            ->orWhere('user_id78', '=', $userId1)
            ->orWhere('user_id79', '=', $userId1)
            ->orWhere('user_id80', '=', $userId1)
            ->orWhere('user_id81', '=', $userId1)
            ->orWhere('user_id82', '=', $userId1)
            ->orWhere('user_id83', '=', $userId1)
            ->orWhere('user_id84', '=', $userId1)
            ->orWhere('user_id85', '=', $userId1)
            ->orWhere('user_id86', '=', $userId1)
            ->orWhere('user_id87', '=', $userId1)
            ->orWhere('user_id88', '=', $userId1)
            ->orWhere('user_id89', '=', $userId1)
            ->orWhere('user_id90', '=', $userId1)
            ->orWhere('user_id91', '=', $userId1)
            ->orWhere('user_id92', '=', $userId1)
            ->orWhere('user_id93', '=', $userId1)
            ->orWhere('user_id94', '=', $userId1)
            ->orWhere('user_id95', '=', $userId1)
            ->orWhere('user_id96', '=', $userId1)
            ->orWhere('user_id97', '=', $userId1)
            ->orWhere('user_id98', '=', $userId1)
            ->orWhere('user_id99', '=', $userId1)
            ->orWhere('user_id100', '=', $userId1);

        if ($userId2) {
            $query->orWhere('user_id1', '=', $userId2)
                ->orWhere('user_id2', '=', $userId2)
                ->orWhere('user_id3', '=', $userId2)
                ->orWhere('user_id4', '=', $userId2)
                ->orWhere('user_id5', '=', $userId2)
                ->orWhere('user_id6', '=', $userId2)
                ->orWhere('user_id7', '=', $userId2)
                ->orWhere('user_id8', '=', $userId2)
                ->orWhere('user_id9', '=', $userId2)
                ->orWhere('user_id10', '=', $userId2)
                ->orWhere('user_id11', '=', $userId2)
                ->orWhere('user_id12', '=', $userId2)
                ->orWhere('user_id13', '=', $userId2)
                ->orWhere('user_id14', '=', $userId2)
                ->orWhere('user_id15', '=', $userId2)
                ->orWhere('user_id16', '=', $userId2)
                ->orWhere('user_id17', '=', $userId2)
                ->orWhere('user_id18', '=', $userId2)
                ->orWhere('user_id19', '=', $userId2)
                ->orWhere('user_id20', '=', $userId2)
                ->orWhere('user_id21', '=', $userId2)
                ->orWhere('user_id22', '=', $userId2)
                ->orWhere('user_id23', '=', $userId2)
                ->orWhere('user_id24', '=', $userId2)
                ->orWhere('user_id25', '=', $userId2)
                ->orWhere('user_id26', '=', $userId2)
                ->orWhere('user_id27', '=', $userId2)
                ->orWhere('user_id28', '=', $userId2)
                ->orWhere('user_id29', '=', $userId2)
                ->orWhere('user_id30', '=', $userId2)
                ->orWhere('user_id31', '=', $userId2)
                ->orWhere('user_id32', '=', $userId2)
                ->orWhere('user_id33', '=', $userId2)
                ->orWhere('user_id34', '=', $userId2)
                ->orWhere('user_id35', '=', $userId2)
                ->orWhere('user_id36', '=', $userId2)
                ->orWhere('user_id37', '=', $userId2)
                ->orWhere('user_id38', '=', $userId2)
                ->orWhere('user_id39', '=', $userId2)
                ->orWhere('user_id40', '=', $userId2)
                ->orWhere('user_id41', '=', $userId2)
                ->orWhere('user_id42', '=', $userId2)
                ->orWhere('user_id43', '=', $userId2)
                ->orWhere('user_id44', '=', $userId2)
                ->orWhere('user_id45', '=', $userId2)
                ->orWhere('user_id46', '=', $userId2)
                ->orWhere('user_id47', '=', $userId2)
                ->orWhere('user_id48', '=', $userId2)
                ->orWhere('user_id49', '=', $userId2)
                ->orWhere('user_id50', '=', $userId2)
                ->orWhere('user_id51', '=', $userId2)
                ->orWhere('user_id52', '=', $userId2)
                ->orWhere('user_id53', '=', $userId2)
                ->orWhere('user_id54', '=', $userId2)
                ->orWhere('user_id55', '=', $userId2)
                ->orWhere('user_id56', '=', $userId2)
                ->orWhere('user_id57', '=', $userId2)
                ->orWhere('user_id58', '=', $userId2)
                ->orWhere('user_id59', '=', $userId2)
                ->orWhere('user_id60', '=', $userId2)
                ->orWhere('user_id61', '=', $userId2)
                ->orWhere('user_id62', '=', $userId2)
                ->orWhere('user_id63', '=', $userId2)
                ->orWhere('user_id64', '=', $userId2)
                ->orWhere('user_id65', '=', $userId2)
                ->orWhere('user_id66', '=', $userId2)
                ->orWhere('user_id67', '=', $userId2)
                ->orWhere('user_id68', '=', $userId2)
                ->orWhere('user_id69', '=', $userId2)
                ->orWhere('user_id70', '=', $userId2)
                ->orWhere('user_id71', '=', $userId2)
                ->orWhere('user_id72', '=', $userId2)
                ->orWhere('user_id73', '=', $userId2)
                ->orWhere('user_id74', '=', $userId2)
                ->orWhere('user_id75', '=', $userId2)
                ->orWhere('user_id76', '=', $userId2)
                ->orWhere('user_id77', '=', $userId2)
                ->orWhere('user_id78', '=', $userId2)
                ->orWhere('user_id79', '=', $userId2)
                ->orWhere('user_id80', '=', $userId2)
                ->orWhere('user_id81', '=', $userId2)
                ->orWhere('user_id82', '=', $userId2)
                ->orWhere('user_id83', '=', $userId2)
                ->orWhere('user_id84', '=', $userId2)
                ->orWhere('user_id85', '=', $userId2)
                ->orWhere('user_id86', '=', $userId2)
                ->orWhere('user_id87', '=', $userId2)
                ->orWhere('user_id88', '=', $userId2)
                ->orWhere('user_id89', '=', $userId2)
                ->orWhere('user_id90', '=', $userId2)
                ->orWhere('user_id91', '=', $userId2)
                ->orWhere('user_id92', '=', $userId2)
                ->orWhere('user_id93', '=', $userId2)
                ->orWhere('user_id94', '=', $userId2)
                ->orWhere('user_id95', '=', $userId2)
                ->orWhere('user_id96', '=', $userId2)
                ->orWhere('user_id97', '=', $userId2)
                ->orWhere('user_id98', '=', $userId2)
                ->orWhere('user_id99', '=', $userId2)
                ->orWhere('user_id100', '=', $userId2);
        }
        $query->first();
        if (!$query) {
            return false;
        }
        return UserAccount::get();
    }

    public function getUserAccounts($record, $with = null)
    {
        if (!$record) {
            return false;
        }

        $userIds = [];
        $userRecords = [];

        if (count($record) > 1) {
            foreach ($record as $rec) {
                $f = "user_id";
                if ($rec->$f) {
                    $userIds[] = $rec->$f;
                    $userRecords[$rec->$f] = $rec->id;
                }
                for ($i=1; $i<=100; $i++) {
                    $field = "user_id$i";
                    if ($rec->$field) {
                        $userIds[] = $rec->$field;
                        $userRecords[$rec->$field] = $rec->id;
                    }
                }
            }
        }else{
            $f = "user_id";
            if ($record->$f) {
                $userIds[] = $record->$f;
                $userRecords[$record->$f] = $record->id;
            }
            for ($i=1; $i<=100; $i++) {
                $field = "user_id$i";
                if ($record->$field) {
                    $userIds[] = $record->$field;
                    $userRecords[$record->$field] = $record->id;
                }
            }
        }

        $users = User::with('account')
                    ->whereIn('id', $userIds);
        if ($with) {
            $users->with($with);
	    }
        $users = $users->get();
        foreach ($users as &$user) {
            $user->userAccountId = $userRecords[$user->id];
        }
        if(Auth::user()){
            if(Auth::user()->realUser()->allowed_account_ids){
                $allowedAccountIds = explode(',', Auth::user()->realUser()->allowed_account_ids);
                foreach($users as $key => $user){
                    if($user->account_id != Auth::user()->account_id && in_array( $user->account_id,  $allowedAccountIds) == false){
                        unset($users[$key]);
                    }
                }
            }
        }
        return $users;
    }

    public function prepareUsersData($record)
    {
        if (!$record) {
            return false;
        }

        $users = $this->getUserAccounts($record);

        $data = [];
	    foreach ($users as $user) {
            $item = new stdClass();
            $item->id = $user->userAccountId;
            $item->user_id = $user->id;
            $item->public_id = $user->public_id;
            $item->user_name = $user->name;//$user->getDisplayName();
            $item->account_id = $user->account->id;
            $item->account_name = $user->name;//$user->account->getDisplayName();
            //$item->logo_url = $user->account->hasLogo() ? $user->account->getLogoUrl() : null;
            $data[] = $item;
        }

        return $data;
    }

    public function loadAccounts($userId) {
        $record = self::findUserAccounts($userId);
        return self::prepareUsersData($record);
    }
}
