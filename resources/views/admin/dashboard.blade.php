@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')
<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    .card {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        text-align: center;
        border: 1px solid #dee2e6;
    }
    .card h3 { margin-top: 0; }
    .card a { text-decoration: none; color: #007bff; font-weight: bold; }
</style>
<div class="dashboard-grid">
    <div class="card"><h3>Manage Properties</h3><p><a href="{{ route('admin.properties.list') }}">View all properties</a></p><p><a href="{{ route('admin.properties.create') }}">Create new property</a></p></div>
    <div class="card"><h3>Manage Agents</h3><p><a href="{{ route('admin.agents.index') }}">View all agents</a></p><p><a href="{{ route('admin.agents.create') }}">Create new agent</a></p></div>
</div>
@endsection