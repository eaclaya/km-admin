<?php

namespace App\Http\Controllers;

use App\Models\SetupMenu;
use Illuminate\Http\Request;

class SetupMenuController extends Controller
{
    public function index(Request $request): \Illuminate\Contracts\View\View
    {
        $items = SetupMenu::whereNull('supra_menu_id')->orWhere('supra_menu_id',0)->with('subItems')->paginate(5);
        return view('setup_menu.list', ['items' => $items]);
    }

    public function create(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = array_filter($request->all(), function ($item) {
            return !empty($item);
        });
        unset($data['_token']);
        if(isset($data['url']) && trim($data['url']) != ''){
            $data['url'] = (!str_starts_with($data['url'], '/')) ? '/' . $data['url'] : $data['url'];
        }else{
            $data['url'] = '#';
        }
        SetupMenu::create($data);
        return redirect()->route('setup_menu.index')->with('success', 'SetupMenu created successfully');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $items = $request->get('item');

        $itemsWithUrl = SetupMenu::whereNotNull('url')->whereNot('url','#')->whereNot('url','/#')->get()->pluck('supra_menu_id','id')->toArray();
        $lastItem = SetupMenu::latest()->first();
        $lastId = $lastItem->id;
        foreach ($items as $key => $value) {
            $data = $value;
            if(isset($data['url']) && trim($data['url']) != '' && trim($data['url']) != '#' && trim($data['url']) != '/#'){
                $data['url'] = (!str_starts_with($data['url'], '/')) ? '/' . $data['url'] : $data['url'];
            }else{
                $data['url'] = '#';
            }
            $id = $data['id'] ?? null;
            unset($data['id']);
            if(isset($data['supra_menu_id'])){
                if($data['supra_menu_id'] == 0 || trim($data['supra_menu_id']) == ''){
                    $data['supra_menu_id'] = null;
                }elseif(array_key_exists($data['supra_menu_id'],$itemsWithUrl)){
                    $data['supra_menu_id'] = isset($itemsWithUrl[$data['supra_menu_id']]) ? $itemsWithUrl[$data['supra_menu_id']] : null;
                }elseif ($data['supra_menu_id'] == $id){
                    $data['supra_menu_id'] = null;
                }elseif ($data['supra_menu_id'] > $lastId){
                    $data['supra_menu_id'] = null;
                }
            }else{
                $data['supra_menu_id'] = null;
            }
            $setupMenu = SetupMenu::findOrFail($id);
            $setupMenu->update($data);
        }

        return redirect()->route('setup_menu.index')->with('success', 'SetupMenu created successfully');
    }

    public function destroy($id)
    {
        $setupMenu = SetupMenu::findOrFail($id);
        $setupMenu->delete();

        return redirect()->route('setup_menu.index')->with('success', 'SetupMenu deleted successfully');
    }
}
