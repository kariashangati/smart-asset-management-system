@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
        <h2 class="text-3xl font-extrabold text-gray-900 text-center mb-6">Reset Password</h2>
        
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                <p class="text-sm text-red-600 font-semibold">{{ $errors->first() }}</p>
            </div>
        @endif

        @if (session('status'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
                <p class="text-sm text-green-600 font-semibold">{{ session('status') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    required 
                    autofocus
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                />
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button 
                type="submit" 
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md transition duration-200"
            >
                Send Password Reset Link
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Remember your password? 
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Login</a>
            </p>
        </div>
    </div>
</div>
@endsection
