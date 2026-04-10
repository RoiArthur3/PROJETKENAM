@extends('layouts.app')

@section('title', 'Test Page')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Test Page</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Session Information</h2>
        <div class="space-y-2">
            <p><strong>User ID:</strong> {{ Auth::id() }}</p>
            <p><strong>User Email:</strong> {{ Auth::user()?->email }}</p>
            <p><strong>User Role:</strong> {{ Auth::user()?->role }}</p>
            <p><strong>Is Admin:</strong> {{ Auth::user()?->isAdmin() ? 'Yes' : 'No' }}</p>
            <p><strong>CSRF Token:</strong> {{ csrf_token() }}</p>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('login') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Login</a>
        @if(Auth::user()?->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="bg-green-500 text-white px-4 py-2 rounded ml-2">Admin Dashboard</a>
        @endif
    </div>
</div>
@endsection
