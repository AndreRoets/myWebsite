@extends('admin.layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User: ' . $user->name)

@section('content')
    <a href="{{ route('admin.users.index') }}" style="display: inline-block; margin-bottom: 1.5rem; color: #007bff; text-decoration: none;">&larr; Back to Users List</a>
    @include('admin.users._form', ['action' => route('admin.users.update', $user), 'method' => 'PUT'])
@endsection