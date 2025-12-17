<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Costume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportController extends Controller
{
    const FORMAT_CURRENCY_IDR_CUSTOM = 'Rp #,##0;[Red]Rp -#,##0';

    public function exportAnalytics(string $format): StreamedResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->hasAnyRole(['admin', 'owner', 'renter'])) {
            abort(403, 'Unauthorized cosmic access.');
        }

        if ($format !== 'excel') {
            abort(400, "Only Excel format is supported.");
        }

        $isAdmin = $user->hasAnyRole(['admin', 'owner']);

        // 1. DATA FETCHING
        $query = User::role('renter')->with([
            'store',
            'costumes' => function ($q) {
                $q->withCount(['orders as total_rentals', 'orders as completed_revenue' => fn($query) => $query->select(DB::raw('SUM(total_price)'))->where('status', 'completed')]);
            }
        ]);

        if (!$isAdmin) {
            $query->where('id', $user->id); // Renter only sees themselves
        }

        $renters = $query->get();
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // Start fresh

        // --- PHASE A: GLOBAL ANALYTICS (ADMIN ONLY) ---
        if ($isAdmin) {
            $globalSheet = $spreadsheet->createSheet();
            $globalSheet->setTitle('Global Analytics');

            $totalPlatformRevenue = Order::where('status', 'completed')->sum('total_price');
            $topCostumes = Costume::where('status', 'approved')->with('renter.store')
                ->withCount(['orders as total_rentals'])
                ->withSum(['orders as rev' => fn($q) => $q->where('status', 'completed')], 'total_price')
                ->orderByDesc('rev')->limit(10)->get();

            $globalSheet->setCellValue('A1', 'STARIUM PLATFORM GLOBAL SALES OVERVIEW');
            $globalSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $globalSheet->setCellValue('A3', 'Total Platform Revenue:');
            $globalSheet->setCellValue('B3', $totalPlatformRevenue);
            $globalSheet->getStyle('B3')->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY_IDR_CUSTOM);

            $globalHeader = ['Rank', 'Costume Name', 'Renter Store', 'Total Rentals', 'Total Revenue (Rp)'];
            $globalSheet->fromArray($globalHeader, null, 'A5');
            $globalSheet->getStyle('A5:E5')->getFont()->setBold(true)->getColor()->setARGB(Color::COLOR_WHITE);
            $globalSheet->getStyle('A5:E5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD97706');

            $row = 6;
            foreach ($topCostumes as $index => $c) {
                $globalSheet->fromArray([$index + 1, $c->name, $c->renter->store->store_name ?? 'N/A', $c->total_rentals, $c->rev], null, 'A' . $row);
                $globalSheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY_IDR_CUSTOM);
                $row++;
            }
            foreach (range('A', 'E') as $col)
                $globalSheet->getColumnDimension($col)->setAutoSize(true);
        }

        // --- PHASE B: RENTER SUMMARY (Dynamic based on Role) ---
        $summarySheet = $spreadsheet->createSheet();
        $summarySheet->setTitle($isAdmin ? 'Renter Summary' : 'My Store Summary');

        $summaryHeader = ['Renter ID', 'Owner Name', 'Store Name', 'Total Listings', 'Total Revenue', 'Status'];
        $summarySheet->fromArray($summaryHeader, null, 'A1');
        $summarySheet->getStyle('A1:F1')->getFont()->setBold(true)->getColor()->setARGB(Color::COLOR_WHITE);
        $summarySheet->getStyle('A1:F1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4F46E5');

        $row = 2;
        foreach ($renters as $renter) {
            $summarySheet->fromArray([
                $renter->id,
                $renter->name,
                $renter->store->store_name ?? 'N/A',
                $renter->costumes->count(),
                $renter->costumes->sum('completed_revenue'),
                $renter->store->is_active ? 'ACTIVE' : 'INACTIVE'
            ], null, 'A' . $row);
            $summarySheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY_IDR_CUSTOM);
            $row++;
        }
        foreach (range('A', 'F') as $col)
            $summarySheet->getColumnDimension($col)->setAutoSize(true);

        // --- PHASE C: DETAILED COSTUME LISTINGS ---
        foreach ($renters as $renter) {
            $sheetName = $isAdmin ? substr($renter->store->store_name ?? 'Renter', 0, 20) . ' Details' : 'My Costume List';
            $detailSheet = $spreadsheet->createSheet();
            $detailSheet->setTitle($sheetName);

            $detailHeader = ['ID', 'Name', 'Series', 'Size', 'Price/Day', 'Disc Value', 'Final Price', 'Stock', 'Status', 'Rentals', 'Total Sales'];
            $detailSheet->fromArray($detailHeader, null, 'A1');
            $detailSheet->getStyle('A1:K1')->getFont()->setBold(true)->getColor()->setARGB(Color::COLOR_WHITE);
            $detailSheet->getStyle('A1:K1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0D0D1F');

            $dRow = 2;
            foreach ($renter->costumes as $costume) {
                $detailSheet->fromArray([
                    $costume->id,
                    $costume->name,
                    $costume->series,
                    $costume->size,
                    $costume->price_per_day,
                    $costume->discount_value ?? 0,
                    $costume->final_price,
                    $costume->stock,
                    strtoupper($costume->status),
                    $costume->total_rentals,
                    $costume->completed_revenue
                ], null, 'A' . $dRow);

                // Formats
                $detailSheet->getStyle('E' . $dRow)->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY_IDR_CUSTOM);
                $detailSheet->getStyle('G' . $dRow)->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY_IDR_CUSTOM);
                $detailSheet->getStyle('K' . $dRow)->getNumberFormat()->setFormatCode(self::FORMAT_CURRENCY_IDR_CUSTOM);
                $dRow++;
            }
            foreach (range('A', 'K') as $col)
                $detailSheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 5. STREAM RESPONSE
        $fileName = ($isAdmin ? 'Admin_Global_Report_' : 'My_Store_Report_') . now()->format('Ymd_His');
        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="' . $fileName . '.xlsx"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}