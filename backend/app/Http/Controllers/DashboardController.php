<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoanResource;
use App\Models\LaptopLoan;
use App\Models\Notification;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get summary statistics for dashboard monitoring.
     */
    public function stats(Request $request): JsonResponse
    {
        $date = $request->input('date', now()->toDateString());

        // Daily loans count
        $dailyLoans = LaptopLoan::whereDate('taken_at', $date)->get();

        $totalTaken = $dailyLoans->count();
        $totalReturned = $dailyLoans->where('status', 'RETURNED')->count();
        $totalBorrowed = $dailyLoans->where('status', 'BORROWED')->count();
        $totalOverdue = $dailyLoans->where('status', 'OVERDUE')->count();

        $percentageReturned = $totalTaken > 0
            ? round(($totalReturned / $totalTaken) * 100, 1)
            : 0;

        $totalStudents = Student::where('is_active', true)->count();
        $totalNotifications = Notification::whereDate('created_at', $date)->count();

        // Overdue list
        $overdueLoans = LaptopLoan::with(['student.classRoom', 'student.parent', 'laptop'])
            ->whereIn('status', ['BORROWED', 'OVERDUE'])
            ->whereDate('taken_at', '<=', $date)
            ->get();

        return response()->json([
            'data' => [
                'date' => $date,
                'total_students' => $totalStudents,
                'total_taken' => $totalTaken,
                'total_returned' => $totalReturned,
                'total_borrowed' => $totalBorrowed,
                'total_overdue' => $totalOverdue,
                'percentage_returned' => $percentageReturned,
                'total_notifications_sent' => $totalNotifications,
                'overdue_list' => LoanResource::collection($overdueLoans),
            ],
        ]);
    }
}
