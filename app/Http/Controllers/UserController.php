<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Satker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $users = User::with('satker')->latest()->paginate(10);
        $satkers = Satker::all();
        
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'user_users' => User::where('role', 'user')->count(),
        ];
        
        return view('admin.users', compact('user', 'users', 'satkers', 'stats'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nrp' => 'required|unique:users,nrp',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,user',
            'satker_id' => 'required|exists:satkers,id',
            'status' => 'required|in:active,inactive',
        ]);
        
        User::create([
            'nrp' => $request->nrp,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'satker_id' => $request->satker_id,
            'status' => $request->status,
        ]);
        
        return redirect()->route('admin.users')
            ->with('success', 'User berhasil ditambahkan.');
    }
    
    public function edit(User $user)
    {
        return response()->json([
            'user' => $user,
            'satkers' => Satker::all()
        ]);
    }
    
    public function update(Request $request, User $user)
    {
        $request->validate([
            'nrp' => 'required|unique:users,nrp,' . $user->id,
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,user',
            'satker_id' => 'required|exists:satkers,id',
            'status' => 'required|in:active,inactive',
        ]);
        
        $user->update($request->all());
        
        return redirect()->route('admin.users')
            ->with('success', 'User berhasil diperbarui.');
    }
    
    public function destroy(User $user)
    {
        if ($user->id == auth()->id()) {
            return redirect()->route('admin.users')
                ->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users')
            ->with('success', 'User berhasil dihapus.');
    }
    
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|min:6',
        ]);
        
        $user->update([
            'password' => Hash::make('password123'),
            'password_changed_at' => null,
        ]);
        
        return redirect()->route('admin.users')
            ->with('success', 'Password berhasil direset.');
    }
}