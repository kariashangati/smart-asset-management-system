@extends('layouts.admin')

@section('title', 'Create Asset Category')
@section('portal_label', 'Admin Portal')
@section('page_title', 'Create Asset Category')
@section('dashboard_url', route('admin.dashboard'))

@section('content')
    <section class="content-card">
        <div class="section-header">
            <div>
                <h2>New Asset Category</h2>
                <p>Add a classification that will later be assigned to institutional assets.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="form-alert">
                <strong>Please correct the following:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.asset-categories.store') }}" class="form-stack">
            @csrf

            <div class="form-group">
                <label for="name">Category Name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <input
                    id="description"
                    name="description"
                    type="text"
                    value="{{ old('description') }}"
                >
            </div>

            <div class="button-row">
                <button type="submit" class="btn btn-primary">
                    Save Asset Category
                </button>

                <a href="{{ route('admin.asset-categories.index') }}" class="btn btn-outline">
                    Cancel
                </a>
            </div>
        </form>
    </section>
@endsection