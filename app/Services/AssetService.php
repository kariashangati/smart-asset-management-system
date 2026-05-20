<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Support\Facades\Storage;

class AssetService
{
    public function createAsset(array $data): Asset
    {
        if (isset($data['image'])) {
            $data['image'] = $data['image']->store('assets', 'public');
        }

        return Asset::create($data);
    }

    public function updateAsset(Asset $asset, array $data): Asset
    {
        if (isset($data['image'])) {
            if ($asset->image) {
                Storage::disk('public')->delete($asset->image);
            }
            $data['image'] = $data['image']->store('assets', 'public');
        }

        $asset->update($data);

        return $asset;
    }

    public function deleteAsset(Asset $asset): void
    {
        if ($asset->image) {
            Storage::disk('public')->delete($asset->image);
        }

        $asset->delete();
    }
}