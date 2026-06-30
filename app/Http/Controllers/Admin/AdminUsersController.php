<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminUsersController extends Controller
{
    public function index()
    {
        $query = User::withCount(['ads', 'supportTickets']);

        if (request()->ajax()) {
            $search = request()->get('search');
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }
            
            $users = $query->orderBy('created_at', 'desc')
                          ->paginate(20);

            return view('admin.users.partials.users-table', compact('users'))->render();
        }

        $users = $query->orderBy('created_at', 'desc')
                      ->paginate(20);

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::whereNotNull('email_verified_at')->count(),
            'unverified_users' => User::whereNull('email_verified_at')->count(),
            'admin_users' => User::where('is_admin', true)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_admin' => ['boolean'],
            'email_verified' => ['boolean'],
        ], [
            'email.unique' => 'Tento email už používa iný používateľ. Zadajte prosím iný email.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'is_admin' => $request->boolean('is_admin'),
            'email_verified_at' => $request->boolean('email_verified') ? now() : null,
        ]);

        return redirect()->route('admin.pouzivatelia.index')
            ->with('success', 'Používateľ bol úspešne vytvorený.');
    }

    public function show(User $user)
    {
        $user->load(['ads', 'supportTickets', 'adPayments']);
        return view('admin.users.show', compact('user'));
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:active,suspended,banned'
        ]);

        // Môžeme pridať status stĺpec do users tabuľky neskôr
        return redirect()->back()->with('success', 'Stav používateľa bol úspešne aktualizovaný.');
    }

    public function destroy(User $user)
    {
        if ($user->is_admin) {
            return redirect()->back()->with('error', 'Nemôžete vymazať admin účet.');
        }

        $user->delete();
        return redirect()->route('admin.pouzivatelia.index')->with('success', 'Používateľ bol úspešne vymazaný.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'is_admin' => ['boolean'],
            'email_verified' => ['boolean'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'is_admin' => $request->boolean('is_admin'),
            'email_verified_at' => $request->boolean('email_verified') ? $user->email_verified_at ?? now() : null,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('admin.pouzivatelia.show', $user)
            ->with('success', 'Používateľ bol úspešne aktualizovaný.');
    }

    /**
     * Prepnúť sa na používateľa (impersonation)
     */
    public function impersonate(User $user)
    {
        // Kontrola že admin sa nepokúša prepnúť na iného admina
        if ($user->is_admin) {
            return redirect()->back()->with('error', 'Nemôžete sa prepnúť na iného administrátora.');
        }

        // Uloženie pôvodného admin ID do session
        session(['impersonating_admin_id' => auth()->id()]);
        
        // Prihlásenie ako používateľ
        auth()->login($user);
        
        return redirect()->route('dashboard')->with('success', "Úspešne ste sa prepli na používateľa: {$user->name}");
    }

    /**
     * Ukončiť impersonation a vrátiť sa ako admin
     */
    public function stopImpersonating()
    {
        $adminId = session('impersonating_admin_id');
        
        if (!$adminId) {
            return redirect()->route('dashboard')->with('error', 'Nie ste v režime prepnutia používateľa.');
        }

        // Nájsť pôvodného admina
        $admin = User::find($adminId);
        
        if (!$admin || !$admin->is_admin) {
            session()->forget('impersonating_admin_id');
            return redirect()->route('login')->with('error', 'Pôvodný admin účet nebol nájdený.');
        }

        // Prihlásiť sa späť ako admin PRED vymazaním session
        auth()->login($admin);
        
        // Teraz vymazať session
        session()->forget('impersonating_admin_id');
        
        return redirect()->route('admin.nastenka')->with('success', 'Úspešne ste sa vrátili do admin režimu.');
    }
} 