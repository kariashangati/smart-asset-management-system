<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\LocationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    /**
     * Display a listing of department assets.
     */
    public function index()
    {
        $departmentId = Auth::user()->department_id;
        $assets = Asset::where('department_id', $departmentId)
            ->with(['category', 'latestLocation'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $categories = AssetCategory::all();

        return view('manager.assets.index', compact('assets', 'categories'));
    }

    /**
     * Show the form for creating a new asset.
     */
    public function create()
    {
        $categories = AssetCategory::all();
        return view('manager.assets.create', compact('categories'));
    }

    /**
     * Store a newly created asset in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_code' => 'nullable|string|unique:assets,asset_code',
            'name' => 'required|string|max:255',
            'asset_category_id' => 'required|exists:asset_categories,id',
            'serial_number' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,missing,maintenance',
            'purchase_date' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Auto-generate asset_code if not provided
        if (empty($validated['asset_code'])) {
            $validated['asset_code'] = $this->generateAssetCode();
        }

        $validated['department_id'] = Auth::user()->department_id;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('assets', 'public');
        }

        Asset::create($validated);

        return redirect()->route('manager.assets.index')
            ->with('success', 'Asset created successfully.');
    }

    /**
     * Display the specified asset with location history.
     */
    public function show(Asset $asset)
    {
        $this->authorizeManager($asset);

        $locationHistory = LocationLog::where('asset_id', $asset->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('manager.assets.show', compact('asset', 'locationHistory'));
    }

    /**
     * Show the form for editing the specified asset.
     */
    public function edit(Asset $asset)
    {
        $this->authorizeManager($asset);
        $categories = AssetCategory::all();
        return view('manager.assets.edit', compact('asset', 'categories'));
    }

    /**
     * Update the specified asset in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        $this->authorizeManager($asset);

        $validated = $request->validate([
            'asset_code' => 'required|string|unique:assets,asset_code,' . $asset->id,
            'name' => 'required|string|max:255',
            'asset_category_id' => 'required|exists:asset_categories,id',
            'serial_number' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,missing,maintenance',
            'purchase_date' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($asset->image) {
                Storage::disk('public')->delete($asset->image);
            }
            $validated['image'] = $request->file('image')->store('assets', 'public');
        }

        $asset->update($validated);

        return redirect()->route('manager.assets.index')
            ->with('success', 'Asset updated successfully.');
    }

    /**
     * Remove the specified asset from storage.
     */
    public function destroy(Asset $asset)
    {
        $this->authorizeManager($asset);

        if ($asset->image) {
            Storage::disk('public')->delete($asset->image);
        }

        $asset->delete();

        return redirect()->route('manager.assets.index')
            ->with('success', 'Asset deleted successfully.');
    }

    /**
     * Get asset details via AJAX.
     */
    public function details(Asset $asset)
    {
        $this->authorizeManager($asset);

        $locationHistory = LocationLog::where('asset_id', $asset->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'asset' => $asset->load('category'),
            'latest_location' => $asset->latestLocation,
            'location_history' => $locationHistory,
        ]);
    }

    /**
     * Generate a unique asset code.
     */
    protected function generateAssetCode()
    {
        $prefix = 'AST';
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(substr(uniqid(), -6));
        
        return $prefix . '-' . $timestamp . '-' . $random;
    }

    /**
     * Authorize that the manager can access this asset.
     */
    protected function authorizeManager(Asset $asset)
    {
        if ($asset->department_id !== Auth::user()->department_id) {
            abort(403, 'Unauthorized access to asset.');
        }
    }
}
