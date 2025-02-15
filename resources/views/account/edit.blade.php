@extends('layout.layout')

@section('content')
<div class="container-fluid mt-3">
    <div class="card mb-3 border-0 shadow-sm" style="background-color:#f2f2f2;">
        <div class="card-body" style="background-color: #37B7C3; border-radius: 8px">
            <h2 class="m-0" style="color: #EBF4F6">Edit Account</h2>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('account.update', $account->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Name Input -->
        <div class="form-group mb-3">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $account->name) }}" required>
        </div>

        <!-- Email Input -->
        <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="text" name="email" id="email" class="form-control" value="{{ old('email', $account->email) }}" required>
        </div>

        <!-- Username Input -->
        <div class="form-group mb-3">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $account->username) }}" required>
        </div>

        <!-- Password Input -->
        <div class="form-group mb-3">
            <label for="password">New Password</label>
            <input type="text" name="password" id="password" class="form-control" placeholder="Masukkan kata sandi baru" required>
        </div>

        <!-- Role Selection Dropdown -->
        <div class="form-group mb-3">
            <label for="role">Role</label>
            <select name="role" id="role" class="form-control" required>
                @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ $account->hasRole($role->name) ? 'selected' : '' }}>
                    {{ $role->name }}
                </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('account.index') }}" class="btn btn-secondary">Back to List</a>
    </form>
</div>
@endsection