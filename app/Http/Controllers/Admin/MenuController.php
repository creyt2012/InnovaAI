<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminMenu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = AdminMenu::ordered()->get();
        return view('admin.menus.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'icon' => 'required|string',
            'route' => 'required|string',
            'parent_id' => 'nullable|exists:admin_menus,id',
            'order' => 'required|integer',
            'roles' => 'required|array'
        ]);

        AdminMenu::create($validated);
        return back()->with('success', 'Menu item added');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:admin_menus,id',
            'items.*.order' => 'required|integer'
        ]);

        foreach ($request->items as $item) {
            AdminMenu::find($item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }
} 