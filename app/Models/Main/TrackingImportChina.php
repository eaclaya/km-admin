<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Session;
use App\Models\Main\User;
use App\Models\Main\OrderItem;
use App\Models\Main\Order;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackingImportChina extends ModelDBMain
{

    protected $connection = 'main';
	protected $data_transaction_order_type = [
		0 => 'ORDEN CREADA',
		1 => 'ORDEN CAMBIADA DE ESTATUS',
		2 => 'ORDEN COPIADA',
		3 => 'ORDEN MODIFICADA',
		4 => 'ORDEN ELIMINADA',
		5 => 'ORDEN REORGANIZADA',
		6 => 'ORDEN DUPLICADA',
	];

	protected $data_transaction_item_type = [
		0 => 'PRODUCTO CREADO',
		1 => 'PRODUCTO CAMBIADO A OTRA ORDEN',
		2 => 'PRODUCTO COPIADO A OTRA ORDEN',
		3 => 'PRODUCTO MODIFICADO',
		4 => 'PRODUCTO ELIMINADO',
	];
	
	protected $primaryKey = 'id';
	protected $table = 'tracking_import_china';
	public $timestamps = true;

	public function typeTransactionOrder(){
		if (isset($this->transaction_order_type)) {
			return $this->data_transaction_order_type[$this->transaction_order_type];
		}
	}

	public function typeTransactionItem(){
		if (isset($this->transaction_item_type)) {
			return $this->data_transaction_item_type[$this->transaction_item_type];
		}
	}

	public function getOriginalBeforeSave($order, $item, $transaction_order_type_id, $transaction_item_type_id)
	{
		$data = [];

		/* ----- Datos Basicos ----- */
		
		$data['transaction_order_type'] = $transaction_order_type_id;
		$data['transaction_item_type'] = $transaction_item_type_id;

		$user = User::find(Session::get('real_userid'));
		$real_user_id = null;
		$user_name = null;
		if ($user) {
			$real_user_id = $user->id;
			$user_name = $user->first_name . " " . $user->last_name;
		}
		$data['user_id'] = Auth::user()->id;
		$data['real_user_id'] = $real_user_id;
		$data['real_user_name'] = $user_name;
		
		$data['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		
		/* ---------- */
		$data['order_id_before']  = isset($order->id) ? $order->id : null;

		$originalOrder = null;
		
		if ( isset($item) ) {
			$orderItem = OrderItem::withTrashed()->with(['order'])->where('product_id','=',$item->product_id)->first();
			$originalOrder = $orderItem->order;
			if (!isset($originalOrder)) {
				$originalOrder = Order::withTrashed()->where('order_date', '=', $order->order_date)->first();
			}
			
			$data['product_id'] = $item->product_id;

			$data['item_id_original']  = isset($orderItem->id) ? $orderItem->id : null;
			$data['item_id_before']  = isset($item->id) ? $item->id : null;
			
			$data['product_key_original']  = isset($orderItem->product_key) ? $orderItem->product_key : null;
			$data['product_key_before']  = isset($item->product_key) ? $item->product_key : null;
			
			$data['item_description_original']  = isset($orderItem->description) ? $orderItem->description : null;
			$data['item_description_before']  = isset($item->description) ? $item->description : null;
			
			$data['item_qty_original']  = isset($orderItem->qty) ? $orderItem->qty : null;
			$data['item_qty_before']  = isset($item->qty) ? $item->qty : null;
			
			$data['item_cost_original']  = isset($orderItem->cost) ? $orderItem->cost : null;
			$data['item_cost_before']  = isset($item->cost) ? $item->cost : null;
			
			$data['item_picture_original']  = isset($orderItem->picture) ? $orderItem->picture : null;
			$data['item_picture_before']  = isset($item->picture) ? $item->picture : null;
			
			$data['item_notes_original']  = isset($orderItem->notes) ? $orderItem->notes : null;
			$data['item_notes_before']  = isset($item->notes) ? $item->notes : null;
			
			$data['item_comments_original']  = isset($orderItem->comments) ? $orderItem->comments : null;
			$data['item_comments_before']  = isset($item->comments) ? $item->comments : null;
			
			$data['item_original_cost_original']  = isset($orderItem->original_cost) ? $orderItem->original_cost : null;
			$data['item_original_cost_before']  = isset($item->original_cost) ? $item->original_cost : null;
		} else {
			if (isset($order)) {
				$originalOrder = Order::withTrashed()->where('order_date', '=', $order->order_date)->first();
			}else{
				$originalOrder = null;
			}
		}

		$data['order_id_original']  = isset($originalOrder->id) ? $originalOrder->id : null;
		$data['order_number_original']  = isset($originalOrder->order_number) ? $originalOrder->order_number : null;
		
		$data['order_number_before']  = isset($order->order_number) ? $order->order_number : null;
		
		$data['original_order_qty_before']  = isset($order->quantity) ? $order->quantity : null;
		$data['original_order_amount_before']  = isset($order->amount) ? $order->amount : null;
		
		$data['status_id_before']  = isset($order->status_id) ? $order->status_id : null;
		
		return $data;
	}

	public function getFinalBeforeSave($order, $data = null)
	{
		$data = $data ? $data :  [];
		$data['final_order_qty_before'] = $order->quantity;
		$data['final_order_amount_before'] = $order->amount;
		return $data;
	}

	public function getOriginalAfterSave($order, $item, $modifyField, $data)
	{
		if ( isset($order) ) {
			$data['original_order_qty_after'] = $order->quantity;
			$data['original_order_amount_after'] = $order->amount;
			$data['status_id_after'] = $order->status_id;			
		}
		$data['item_modify_field'] = $modifyField;
		if ( isset($item) ) {
			$data['item_id_after'] = $item->id;
			$data['product_key_after'] = $item->product_key;
			$data['item_description_after'] = $item->description;
			$data['item_qty_after'] = $item->qty;
			$data['item_cost_after'] = $item->cost;
			$data['item_picture_after'] = $item->picture;
			$data['item_notes_after'] = $item->notes;
			$data['item_comments_after'] = $item->comments;
			$data['item_original_cost_after'] = $item->original_cost;
		}
		return $data;
	}

	public function getFinalAfterSave($order, $data = null)
	{
		$data = $data ? $data :  [];
		$data['order_id_after'] = $order->id;
		$data['order_number_after'] = $order->order_number;
		$data['final_order_qty_after'] = $order->quantity;
		$data['final_order_amount_after'] = $order->amount;
		return $data;
	}

	public function saveData($data)
	{
		try {
			//SEND PRODUCT	
			if (isset($data)) {
				foreach ($data as $key => $value) {
					$this->$key = $value;
				}
				$this->save();
			}
		} catch (Exception $e) {
			dd($e->getMessage());
		}
	}
}
