@extends('layouts.admin')

@section('title', 'Edit Department')
@section('portal_label', 'Admin Portal')
@section('page_title', 'Edit Department')
@section('dashboard_url', route('admin.dashboard'))

@section('content')
    <section class="content-card">
        <div class="section-header">
            <div>
                <h2>Edit Department</h2>
                <p>Update the selected department information.</p>
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

        <form
            method="POST"
            action="{{ route('admin.departments.update', $department) }}"
            class="form-stack"
        >
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Department Name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name', $department->name) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="code">Department Code</label>
                <input
                    id="code"
                    name="code"
                    type="text"
                    value="{{ old('code', $department->code) }}"
                >
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <input
                    id="description"
                    name="description"
                    type="text"
                    value="{{ old('description', $department->description) }}"
                >
            </div>

            <div class="button-row">
                <button type="submit" class="btn btn-primary">
                    Update Department
                </button>

                <a href="{{ route('admin.departments.index') }}" class="btn btn-outline">
                    Cancel
                </a>
            </div>
        </form>
    </section>
@endsection