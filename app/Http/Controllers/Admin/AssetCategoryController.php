<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssetCategoryRequest;
use App\Http\Requests\UpdateAssetCategoryRequest;
use App\Models\AssetCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssetCategoryController extends Controller
{
    public function index(): View
    {
        $assetCategories = AssetCategory::query()
            ->latest()
            ->get();

        return view('admin.asset-categories.index', compact('assetCategories'));
    }

    public function create(): View
    {
        return view('admin.asset-categories.create');
    }

    public function store(StoreAssetCategoryRequest $request): RedirectResponse
    {
        AssetCategory::create($request->validated());

        return redirect()
            ->route('admin.asset-categories.index')
            ->with('success', 'Asset category created successfully.');
    }

    public function edit(AssetCategory $assetCategory): View
    {
        return view('admin.asset-categories.edit', compact('assetCategory'));
    }

    public function update(
        UpdateAssetCategoryRequest $request,
        AssetCategory $assetCategory
    ): RedirectResponse {
        $assetCategory->update($request->validated());

        return redirect()
            ->route('admin.asset-categories.index')
            ->with('success', 'Asset category updated successfully.');
    }

    public function destroy(AssetCategory $assetCategory): RedirectResponse
    {
        $assetCategory->delete();

        return redirect()
            ->route('admin.asset-categories.index')
            ->with('success', 'Asset category deleted successfully.');
    }
}