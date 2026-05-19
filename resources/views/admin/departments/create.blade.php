@extends('layouts.admin')

@section('title', 'Create Department')
@section('portal_label', 'Admin Portal')
@section('page_title', 'Create Department')
@section('dashboard_url', route('admin.dashboard'))

@section('content')
    <section class="content-card">
        <div class="section-header">
            <div>
                <h2>New Department</h2>
                <p>Add a department that will later be assigned to institutional assets.</p>
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

        <form method="POST" action="{{ route('admin.departments.store') }}" class="form-stack">
            @csrf

            <div class="form-group">
                <label for="name">Department Name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="code">Department Code</label>
                <input
                    id="code"
                    name="code"
                    type="text"
                    value="{{ old('code') }}"
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
                    Save Department
                </button>

                <a href="{{ route('admin.departments.index') }}" class="btn btn-outline">
                    Cancel
                </a>
            </div>
        </form>
    </section>
@endsection