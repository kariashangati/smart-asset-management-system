<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Department;
use App\Services\AssetService;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    protected $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    public function index()
    {
        $assets = Asset::with(['category', 'department', 'activeAssignment.trackerDevice'])->get();
        return view('admin.assets.index', compact('assets'));
    }

    public function create()
    {
        $categories = AssetCategory::all();
        $departments = Department::all();
        return view('admin.assets.create', compact('categories', 'departments'));
    }

    public function store(StoreAssetRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $asset = $this->assetService->createAsset($data);

        return redirect()->route('admin.assets.index')
            ->with('success', 'Asset created successfully.');
    }

    public function show(Asset $asset)
    {
        $asset->load(['category', 'department', 'assignments.trackerDevice', 'activeAssignment.trackerDevice', 'geofences']);
        return view('admin.assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $categories = AssetCategory::all();
        $departments = Department::all();
        return view('admin.assets.edit', compact('asset', 'categories', 'departments'));
    }

    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        $this->assetService->updateAsset($asset, $data);

        return redirect()->route('admin.assets.index')
            ->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        $this->assetService->deleteAsset($asset);
        return redirect()->route('admin.assets.index')
            ->with('success', 'Asset deleted successfully.');
    }
}