@extends('admin.layouts.app')

@section('title', 'Edit Agent')
@section('page-title', 'Edit Agent: ' . $agent->name)

@section('content')
    <a href="{{ route('admin.agents.index') }}" style="display: inline-block; margin-bottom: 1.5rem; color: #007bff; text-decoration: none;">&larr; Back to Agents List</a>
    @include('admin.agents._form', ['action' => route('admin.agents.update', $agent), 'method' => 'PUT'])
@endsection