@extends('admin.layouts.app')

@section('title', 'Create New Agent')
@section('page-title', 'Create New Agent')

@section('content')
    <a href="{{ route('admin.agents.index') }}" style="display: inline-block; margin-bottom: 1.5rem; color: #007bff; text-decoration: none;">&larr; Back to Agents List</a>
    @include('admin.agents._form', ['action' => route('admin.agents.store'), 'method' => 'POST', 'agent' => new \App\Models\Agent()])
@endsection