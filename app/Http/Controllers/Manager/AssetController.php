<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function index()
    {
        $departmentId = Auth::user()->department_id;
        $assets = Asset::where('department_id', $departmentId)
            ->with(['category', 'latestLocation'])
            ->get();
            
        return view('admin.assets.index', compact('assets'));
    }

    public function show(Asset $asset)
    {
        $this->authorizeManager($asset);
        return view('admin.assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $this->authorizeManager($asset);
        $categories = AssetCategory::all();
        return view('admin.assets.edit', compact('asset', 'categories'));
    }

    public function update(Request $request, Asset $asset)
    {
        $this->authorizeManager($asset);
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'asset_category_id' => 'required|exists:asset_categories,id',
            'status' => 'required|string',
        ]);

        $asset->update($data);
        return redirect()->route('manager.assets.index')->with('success', 'Asset updated successfully.');
    }

    protected function authorizeManager(Asset $asset)
    {
        if ($asset->department_id !== Auth::user()->department_id) {
            abort(403, 'Unauthorized access to asset.');
        }
    }
}