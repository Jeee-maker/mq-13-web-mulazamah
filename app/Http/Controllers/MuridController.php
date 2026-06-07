<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\PaymentTransaction;

class MuridController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        return view('murid.dashboard', compact('user'));
    }

    public function mulazamah()
    {
        $user = Auth::user();
        $student = Student::with(['yearly', 'monthly'])->where('user_id', $user->id)->firstOrFail();
        
        // Group months by year
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

        // Sort by year
        usort($yearsData, function($a, $b) {
            return $a['year'] <=> $b['year'];
        });

        $transactions = PaymentTransaction::where('student_id', $student->id)->orderBy('created_at', 'desc')->get();

        return view('murid.mulazamah', compact('user', 'student', 'yearsData', 'transactions'));
    }

    public function uploadPayment(Request $request)
    {
        $request->validate([
            'dest_bank_info' => 'required|string',
            'user_bank' => 'required|string',
            'user_account_no' => 'required|string',
            'user_account_name' => 'required|string',
            'amount' => 'required|numeric|min:1000',
            'receipt' => 'required|image|max:2048', // max 2MB
        ]);

        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->firstOrFail();

        // Handle upload to local storage (acting as Cloudinary placeholder)
        $path = $request->file('receipt')->store('receipts', 'public');

        PaymentTransaction::create([
            'student_id' => $student->id,
            'dest_bank_info' => $request->dest_bank_info,
            'user_bank' => $request->user_bank,
            'user_account_no' => $request->user_account_no,
            'user_account_name' => $request->user_account_name,
            'amount' => $request->amount,
            'notes' => $request->notes,
            'receipt_path' => $path,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Pembayaran sedang diproses.');
    }
}
