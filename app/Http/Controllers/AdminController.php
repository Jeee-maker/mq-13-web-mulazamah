<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\User;
use App\Models\PaymentTransaction;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        return view('admin.dashboard', compact('user'));
    }

    public function cekMulazamah(Request $request)
    {
        $query = Student::with('user');
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        $students = $query->paginate(20);
        return view('admin.cek-mulazamah', compact('students'));
    }

    public function detailMulazamah($id)
    {
        $student = Student::with(['user', 'yearly', 'monthly'])->findOrFail($id);
        
        $yearsData = [];
        foreach ($student->yearly as $yearRecord) {
            $months = $student->monthly->where('hijri_year', $yearRecord->hijri_year)->values();
            $yearsData[] = [
                'year' => $yearRecord->hijri_year,
                'paid' => $yearRecord->total_paid,
                'debt' => $yearRecord->total_debt,
                'expected' => $yearRecord->total_expected,
                'months' => $months
            ];
        }

        usort($yearsData, function($a, $b) {
            return $a['year'] <=> $b['year'];
        });

        $transactions = PaymentTransaction::where('student_id', $student->id)->orderBy('created_at', 'desc')->get();

        return view('admin.detail-mulazamah', compact('student', 'yearsData', 'transactions'));
    }

    public function konfirmasiPembayaran()
    {
        $pending = PaymentTransaction::with('student.user')->where('status', 'pending')->orderBy('created_at', 'desc')->get();
        $approved = PaymentTransaction::with('student.user')->where('status', 'approved')->orderBy('updated_at', 'desc')->get();
        $rejected = PaymentTransaction::with('student.user')->where('status', 'rejected')->orderBy('updated_at', 'desc')->get();

        return view('admin.konfirmasi-pembayaran', compact('pending', 'approved', 'rejected'));
    }

    public function prosesPembayaran(Request $request, $id)
    {
        $transaction = PaymentTransaction::findOrFail($id);
        $action = $request->action; // 'approve' or 'reject'
        
        if ($action === 'approve') {
            $transaction->status = 'approved';
            
            // Tambahkan ke total_paid di student
            $student = $transaction->student;
            $student->total_paid += $transaction->amount;
            if ($student->total_debt > 0) {
                $student->total_debt = max(0, $student->total_debt - $transaction->amount);
            }
            $student->save();
        } else {
            $transaction->status = 'rejected';
        }
        
        $transaction->actioned_by = Auth::id();
        $transaction->save();

        return redirect()->back()->with('success', 'Pembayaran berhasil di proses.');
    }
}
