<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LibraryController extends Controller
{
    public function home(Request $request)
    {
        $query = Book::query()->where('available_stock', '>', 0);
        if ($search = $request->string('q')->toString()) {
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('author', 'like', "%{$search}%")->orWhere('category', 'like', "%{$search}%"));
        }
        return view('library.home', ['books' => $query->orderBy('title')->paginate(12)->withQueryString()]);
    }

    public function showLogin() { return view('library.login'); }
    public function showRegister() { return view('library.register'); }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:30', 'unique:members,nis'],
            'name' => ['required', 'string', 'max:255'],
            'class_name' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'unique:users,email', 'unique:members,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);
        $member = Member::create(['nis' => $data['nis'], 'name' => $data['name'], 'class_name' => $data['class_name'], 'email' => $data['email'], 'status' => 'aktif']);
        $user = User::create(['name' => $data['name'], 'email' => $data['email'], 'password' => Hash::make($data['password']), 'role' => 'siswa', 'member_id' => $member->id]);
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route('my-loans')->with('success', 'Akun berhasil dibuat. Selamat datang!');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);
        if (! Auth::attempt($credentials)) throw ValidationException::withMessages(['email' => 'Email atau kata sandi tidak cocok.']);
        $request->session()->regenerate();
        if (Auth::user()->role !== 'siswa') {
            Auth::logout();
            return back()->withErrors(['email' => 'Akun ini tidak memiliki akses siswa.']);
        }
        return redirect()->intended(route('my-loans'));
    }

    public function logout(Request $request)
    {
        Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    public function myLoans()
    {
        $member = Auth::user()->member;
        return view('library.loans', ['loans' => $member->loans()->with('book')->latest()->get()]);
    }

    public function borrow(Book $book)
    {
        $member = Auth::user()->member;
        if (! $member || $member->status !== 'aktif') return back()->withErrors(['book' => 'Akun anggota tidak aktif.']);
        if ($member->loans()->where('status', 'dipinjam')->count() >= 3) return back()->withErrors(['book' => 'Maksimal 3 buku dapat dipinjam pada saat yang sama.']);
        Loan::create(['member_id' => $member->id, 'book_id' => $book->id, 'processed_by' => Auth::id(), 'loan_date' => today(), 'due_date' => today()->addDays(7), 'status' => 'dipinjam']);
        return redirect()->route('my-loans')->with('success', 'Peminjaman berhasil. Kembalikan sebelum tanggal jatuh tempo.');
    }

    public function returnBook(Loan $loan)
    {
        abort_unless($loan->member_id === Auth::user()->member_id, 403);
        if ($loan->status !== 'dipinjam') return back()->withErrors(['loan' => 'Transaksi ini sudah ditutup.']);
        $loan->update(['status' => 'dikembalikan', 'returned_at' => today()]);
        return back()->with('success', 'Pengembalian berhasil dicatat.');
    }
}
