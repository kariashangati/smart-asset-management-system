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
        $query = Asset::with(['category', 'department', 'activeAssignment.trackerDevice']);

        // Filter assets by department for asset managers
        if (auth()->user()->isDepartmentManager()) {
            $query->where('department_id', auth()->user()->department_id);
        }

        $assets = $query->get();
        return view('admin.assets.index', compact('assets'));
    }

    public function create()
    {
        $categories = AssetCategory::all();
        
        // Asset managers only see their own department
        if (auth()->user()->isDepartmentManager()) {
            $departments = auth()->user()->department()->get();
        } else {
            $departments = Department::all();
        }
        
        return view('admin.assets.create', compact('categories', 'departments'));
    }

    public function store(StoreAssetRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        // For asset managers, set department to their own
        if (auth()->user()->isDepartmentManager()) {
            $data['department_id'] = auth()->user()->department_id;
        }

        $asset = $this->assetService->createAsset($data);

        return redirect()->route('admin.assets.index')
            ->with('success', 'Asset created successfully.');
    }

    public function show(Asset $asset)
    {
        // Check if user can view this asset
        $this->authorize('view', $asset);

        $asset->load(['category', 'department', 'assignments.trackerDevice', 'activeAssignment.trackerDevice', 'geofences']);
        return view('admin.assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        // Check if user can edit this asset
        $this->authorize('update', $asset);

        $categories = AssetCategory::all();
        
        // Asset managers only see their own department
        if (auth()->user()->isDepartmentManager()) {
            $departments = auth()->user()->department()->get();
        } else {
            $departments = Department::all();
        }
        
        return view('admin.assets.edit', compact('asset', 'categories', 'departments'));
    }

    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        // Check if user can update this asset
        $this->authorize('update', $asset);

        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        // For asset managers, ensure department doesn't change
        if (auth()->user()->isDepartmentManager()) {
            $data['department_id'] = auth()->user()->department_id;
        }

        $this->assetService->updateAsset($asset, $data);

        return redirect()->route('admin.assets.index')
            ->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        // Check if user can delete this asset
        $this->authorize('delete', $asset);

        $this->assetService->deleteAsset($asset);
        return redirect()->route('admin.assets.index')
            ->with('success', 'Asset deleted successfully.');
    }
}
