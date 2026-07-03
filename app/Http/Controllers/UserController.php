<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index');
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(Request $request): never
    {
        abort(501, 'Not implemented yet.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): never
    {
        abort(501, 'Not implemented yet.');
    }

    public function destroy(User $user): never
    {
        abort(501, 'Not implemented yet.');
    }
}
