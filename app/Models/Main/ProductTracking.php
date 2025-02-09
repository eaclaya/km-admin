<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Session;
use App\Models\Main\User;
use Auth;

class ProductTracking extends ModelDBMain
{

    protected $connection = 'main';
	protected $primaryKey = 'id';
	protected $table = 'products_tracking';
	public $timestamps = false;


	public function getOriginalProductBeforeSave($product, $transaction_type, $model)
	{
		$data = [];
		$data['product_id'] = $product->id;
		$data['user_id'] = (Auth::check()) ? Auth::user()->realUser()->id : ($model->real_user_id ? $model->real_user_id : $model->user_id);
		$data['original_account_id'] = $product->account_id;
		$data['product_key'] = $product->product_key;
		$data['notes'] = $product->notes;
		$data['original_quantity_before'] = $product->qty;
		$data['original_price'] = $product->wholesale_price;
		$data['final_price'] = $product->price;
		$data['original_cost'] = $product->cost;
		$data['transaction_type'] = $transaction_type;
		if ($transaction_type == 'invoice') {
			$data['invoice_id'] = isset($model->invoice_id) ?  $model->invoice_id : null;
			$data['item_id'] = isset($model->id) ? $model->id : null;
			$data['transfer_id'] = null;
			$data['import_id'] = null;
			$data['stock_id'] = null;
			$data['refund_id'] = null;
		} elseif ($transaction_type == 'transfer') {
			$data['transfer_id'] = $model->id;
			$data['invoice_id'] = null;
			$data['import_id'] = null;
			$data['stock_id'] = null;
			$data['refund_id'] = null;
			$data['final_account_id'] = $model->to_account_id;
		} elseif ($transaction_type == 'import') {
			$data['import_id'] = $model->id;
			$data['transfer_id'] = null;
			$data['invoice_id'] = null;
			$data['stock_id'] = null;
			$data['refund_id'] = null;
		} elseif ($transaction_type == 'stock') {
			$data['transfer_id'] = null;
			$data['invoice_id'] = null;
			$data['import_id'] = null;
			$data['refund_id'] = null;
			$data['stock_id'] = $model->id;
		} elseif ($transaction_type == 'purchase') {
			$data['transfer_id'] = null;
			$data['invoice_id'] = null;
			$data['import_id'] = null;
			$data['refund_id'] = null;
			$data['stock_id'] = $model->id;
		} elseif ($transaction_type == 'refund') {
			$data['transfer_id'] = null;
			$data['invoice_id'] = null;
			$data['import_id'] = null;
			$data['refund_id'] = $model->id;
			$data['stock_id'] = null;
		} else {
			$data['transfer_id'] = null;
			$data['invoice_id'] = null;
			$data['import_id'] = null;
			$data['stock_id'] = null;
			$data['refund_id'] = null;
		}
		return $data;
	}

	public function getFinalProductBeforeSave($product, $transfer, $data = null)
	{

		$data = $data ? $data :  [];

		$data['product_key'] = $product->product_key;
		$data['transfer_id'] = $transfer->id;
		$data['final_account_id'] = $product->account_id;
		$data['final_quantity_before'] = $product->qty;
		$data['final_price'] = $product->price;
		$data['final_cost'] = $product->cost;

		return $data;
	}

	public function getOriginalProductAfterSave($product, $data)
	{
		if (isset($data['final_account_id']) == false) {
			$data['final_account_id'] = $product->account_id;
		}
		$data['original_quantity_after'] = $product->qty;
		$data['original_price'] = $product->price;
		$data['original_cost'] = $product->cost;

		$data['product_id'] = isset($data['product_id']) ? $data['product_id'] : $product->id;
		$data['product_key'] = isset($data['product_key']) ? $data['product_key'] : $product->product_key;
		$data['notes'] = isset($data['notes']) ? $data['notes'] :  $product->notes;

		if ($data['invoice_id']) {
			if (intval($data['original_quantity_after']) - intval($data['original_quantity_before']) > 0) {
				$data['reason'] = 'Cantidad rebajada en factura';
			} else {
				$data['reason'] = 'Cantidad sumada en factura';
			}
		}
		if ($data['stock_id']) {
			if (intval($data['original_quantity_after']) - intval($data['original_quantity_before']) > 0) {
				$data['reason'] = 'Cantidad sumada en almacen';
			} else {
				$data['reason'] = 'Cantidad rebajada en almacen';
			}
		}
		if ($data['transfer_id']) {
			$data['reason'] = 'Transferencia enviada';
		}
		if ($data['refund_id']) {
			$data['reason'] = 'Devolucion de producto';
		}
		return $data;
	}

	public function getFinalProductAfterSave($product, $data)
	{
		$data['final_account_id'] = $product->account_id;
		$data['final_quantity_after'] = $product->qty;
		$data['final_price'] = $product->price;
		$data['final_cost'] = $product->cost;
		if ($data['transfer_id']) {
			$data['reason'] = 'Transferencia recibida';
		}
		return $data;
	}


	public function saveProduct($data, $accept_transfer = false)
	{
		try {
			//RECEIVE PRODUCT
			if ($accept_transfer) {
				$this->final_account_id = $data['final_account_id'];
				$this->final_quantity_before = isset($data['final_quantity_before']) ? $data['final_quantity_before'] : 0;
				$this->final_quantity_after = isset($data['final_quantity_after']) ? $data['final_quantity_after'] : 0;
				$this->final_cost = isset($data['final_cost']) ? $data['final_cost'] : 0;
				$this->reason = isset($data['reason']) ? $data['reason'] : '';
				$this->save();
				return;
			}
			//SEND PRODUCT	
			if (isset($data['product_id']) || isset($data['product_key'])) {
				$real_userid = Session::has('real_userid') ? Session::get('real_userid') : $data['user_id'];
				$user = User::find($real_userid);
				$user_id = null;
				$user_name = null;
				if ($user) {
					$user_id = $user->id;
					$user_name = $user->first_name . " " . $user->last_name;
				}
				$this->product_id = $data['product_id'];
				$this->user_id = $data['user_id'];
				$this->original_account_id = $data['original_account_id'];
				$this->final_account_id = $data['final_account_id'];
				$this->product_key = $data['product_key'];
				$this->notes = $data['notes'];
				$this->original_quantity_before = isset($data['original_quantity_before']) ? $data['original_quantity_before'] : 0;
				$this->original_quantity_after = isset($data['original_quantity_after']) ? $data['original_quantity_after'] : 0;
				$this->final_quantity_before = isset($data['final_quantity_before']) ? $data['final_quantity_before'] : 0;
				$this->final_quantity_after = isset($data['final_quantity_after']) ? $data['final_quantity_after'] : 0;
				$this->original_price = $data['original_price'];
				$this->final_price = isset($data['final_price']) ? $data['final_price'] : 0;
				$this->original_cost = isset($data['original_cost']) ? $data['original_cost'] : 0;
				$this->final_cost = isset($data['final_cost']) ? $data['final_cost'] : 0;
				$this->transaction_type = $data['transaction_type'];
				$this->transfer_id = $data['transfer_id'];
				$this->invoice_id = $data['invoice_id'];
				$this->import_id = $data['import_id'];
				$this->stock_id = $data['stock_id'];
				$this->refund_id = $data['refund_id'];
				$this->item_id = isset($data['item_id']) ? $data['item_id'] : null;
				$this->reason = isset($data['reason']) ? $data['reason'] : '';
				$this->remote_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
				$this->real_user_id = $user_id;
				$this->real_user_name = $user_name;
				$this->created_at = date('Y-m-d H:i:s');
				$this->save();
			}
		} catch (Exception $e) {
			dd($e->getMessage());
		}
	}
}
