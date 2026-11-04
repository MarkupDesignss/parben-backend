<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\RouterLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Dompdf\Dompdf;
use Dompdf\Options;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalLogs = RouterLog::count();
        $totalContacts = Contact::count();
        $totalUniqueRouters = RouterLog::distinct('router_id')->count('router_id');
        $totalUniqueCases = RouterLog::whereNotNull('case_id')->distinct('case_id')->count('case_id');
        
        $recentLogs = RouterLog::with('user')
            ->latest('log_generated_at')
            ->take(5)
            ->get();
        
        $logsPerDay = RouterLog::select(DB::raw('DATE(log_generated_at) as date'), DB::raw('count(*) as total'))
            ->where('log_generated_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
        
        return view('admin.dashboard', compact(
            'totalLogs', 
            'totalContacts', 
            'totalUniqueRouters', 
            'totalUniqueCases',
            'recentLogs', 
            'logsPerDay'
        ));
    }

    public function cases(Request $request)
    {
        $logs = RouterLog::query()
            ->leftJoin('users', 'router_logs.user_id', '=', 'users.id')
            ->select('router_logs.*', 'users.name as investigator_name', 'users.id as investigator_id');

        if ($request->filled('search')) {
            $logs->where(function ($q) use ($request) {
                $q->where('router_logs.case_id', 'like', '%' . $request->search . '%')
                  ->orWhere('router_logs.router_id', 'like', '%' . $request->search . '%')
                  ->orWhere('router_logs.log_content', 'like', '%' . $request->search . '%')
                  ->orWhere('users.name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('from_date')) {
            $logs->whereDate('router_logs.log_generated_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $logs->whereDate('router_logs.log_generated_at', '<=', $request->to_date);
        }

        if ($request->filled('router_model')) {
            $logs->where('router_logs.router_model', $request->router_model);
        }

        if ($request->filled('investigator')) {
            $logs->where('router_logs.user_id', $request->investigator);
        }

        $logs = $logs->orderByDesc('router_logs.log_generated_at')
                     ->paginate(10)
                     ->withQueryString();

        $uniqueRouterModels = RouterLog::distinct()->pluck('router_model')->filter();
        $uniqueInvestigators = User::whereHas('routerLogs')->select('id', 'name')->get();

        return view('admin.cases', compact('logs', 'uniqueRouterModels', 'uniqueInvestigators'));
    }

    // public function exportSingleLog($id)
    // {
    //     try {
    //         $log = RouterLog::with('user')->findOrFail($id);
    //         $originalContent = $log->log_content ?? 'No log content available.';
    //         $contentHash = hash('sha256', $originalContent);
    //         $log->hash = $contentHash;
    //         $log->save();
            
    //         $verificationCode = substr($contentHash, 0, 8) . '...' . substr($contentHash, -8);
            
    //         $data = [
    //             'log' => $log,
    //             'log_content' => $originalContent,
    //             'file_hash' => $contentHash,
    //             'verification_code' => $verificationCode,
    //             'generated_at' => now()->format('d/m/Y H:i:s')
    //         ];
            
    //         $options = new Options();
    //         $options->set('defaultFont', 'DejaVu Sans');
    //         $options->set('isHtml5ParserEnabled', true);
    //         $options->set('isRemoteEnabled', false);
            
    //         $dompdf = new Dompdf($options);
    //         $html = view('admin.exports.single_log_pdf', $data)->render();
    //         $dompdf->loadHtml($html);
    //         $dompdf->setPaper('A4', 'portrait');
    //         $dompdf->render();
            
    //         $routerId = preg_replace('/[^A-Za-z0-9]/', '_', $log->router_id);
    //         $filename = 'log_' . $routerId . '_' . $log->id . '_' . now()->format('Ymd_His') . '.pdf';
            
    //         return $dompdf->stream($filename, ['Attachment' => true]);
            
    //     } catch (\Exception $e) {
    //         Log::error('Export Single Log Error: ' . $e->getMessage());
    //         return back()->with('error', 'Error exporting log: ' . $e->getMessage());
    //     }
    // }
    
    public function exportSingleLog($id)
    {
        try {
            $log = RouterLog::with('user')->findOrFail($id);
            $originalContent = $log->log_content ?? 'No log content available.';
            $contentHash = hash('sha256', $originalContent);
            $log->hash = $contentHash;
            $log->save();
    
            $verificationCode = substr($contentHash, 0, 8) . '...' . substr($contentHash, -8);
    
            $data = [
                'log' => $log,
                'log_content' => $originalContent,
                'file_hash' => $contentHash,
                'verification_code' => $verificationCode,
                'generated_at' => now()->format('d/m/Y H:i:s')
            ];
    
            $options = new Options();
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', false);
    
            $dompdf = new Dompdf($options);
            $dompdf->setPaper('A4', 'portrait');
    
            $html = view('admin.exports.single_log_pdf', $data)->render();
            $dompdf->loadHtml($html);
    
            // AES ENCRYPTION
            $dompdf->getCanvas()->get_cpdf()->setEncryption(
                '',
                config('app.pdf_owner_password', 'Owner@2026'),
                ['print']
            );
    
            $dompdf->render();
    
            $routerId = preg_replace('/[^A-Za-z0-9]/', '_', $log->router_id);
            $filename = 'log_' . $routerId . '_' . $log->id . '_' . now()->format('Ymd_His') . '.pdf';
    
            return $dompdf->stream($filename, ['Attachment' => true]);
    
        } catch (\Exception $e) {
            Log::error('Export Single Log Error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting log: ' . $e->getMessage());
        }
    }

    public function exportBulkLogs(Request $request)
    {
        try {
            $logIds = json_decode($request->input('log_ids', '[]'), true);
            if (empty($logIds)) {
                return back()->with('error', 'Please select at least one log to export.');
            }
            $logs = RouterLog::with('user')->whereIn('id', $logIds)->get();
            $bulkData = '';
            foreach ($logs as $log) {
                $content = $log->log_content ?? 'No log content available.';
                $bulkData .= $content . $log->id;
            }
            $bulkHash = hash('sha256', $bulkData . now()->timestamp);
            foreach ($logs as $log) {
                $log->hash = $bulkHash;
                $log->save();
            }
            $data = [
                'logs' => $logs,
                'bulk_hash' => $bulkHash,
                'total_logs' => $logs->count(),
                'generated_at' => now()->format('d/m/Y H:i:s')
            ];
            $options = new Options();
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', false);
            $dompdf = new Dompdf($options);
            $html = view('admin.exports.bulk_logs_pdf', $data)->render();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            $filename = 'bulk_logs_export_' . now()->format('Ymd_His') . '.pdf';
            return $dompdf->stream($filename, ['Attachment' => true]);
        } catch (\Exception $e) {
            Log::error('Export Bulk Logs Error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting logs: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        try {
            if ($request->get('format') === 'pdf') {
                return $this->exportFilteredPdf($request);
            }
            $logs = $this->getFilteredLogs($request);
            $filename = 'router_logs_export_' . now()->format('Ymd_His') . '.csv';
            return response()->stream(function () use ($logs) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Case ID', 'Router ID', 'Router Model', 'Investigator', 'Log Time', 'File Path', 'File Size (bytes)']);
                foreach ($logs as $log) {
                    fputcsv($file, [
                        $log->case_id ?? 'N/A',
                        $log->router_id,
                        $log->router_model ?? 'N/A',
                        $log->investigator_name ?? 'N/A',
                        $log->log_generated_at ? Carbon::parse($log->log_generated_at)->format('d/m/Y H:i:s') : 'N/A',
                        $log->file_path ? asset('storage/' . $log->file_path) : 'N/A',
                        $log->file_size ?? 0,
                    ]);
                }
                fclose($file);
            }, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=$filename",
            ]);
        } catch (\Exception $e) {
            Log::error('Export Error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting: ' . $e->getMessage());
        }
    }

    private function exportFilteredPdf(Request $request)
    {
        try {
            $logs = $this->getFilteredLogs($request);
            $hashData = '';
            foreach ($logs as $log) {
                $content = $log->log_content ?? 'No log content available.';
                $hashData .= $content . $log->id;
            }
            $exportHash = hash('sha256', $hashData . now()->timestamp);
            foreach ($logs as $log) {
                $log->hash = $exportHash;
                $log->save();
            }
            $data = [
                'logs' => $logs,
                'export_hash' => $exportHash,
                'generated_at' => now()->format('d/m/Y H:i:s'),
                'total_records' => $logs->count(),
                'filters' => $request->only(['search', 'from_date', 'to_date', 'router_model', 'investigator'])
            ];
            $options = new Options();
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', false);
            $dompdf = new Dompdf($options);
            $html = view('admin.exports.router_logs_pdf', $data)->render();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            $filename = 'router_logs_export_' . now()->format('Ymd_His') . '.pdf';
            return $dompdf->stream($filename, ['Attachment' => true]);
        } catch (\Exception $e) {
            Log::error('Export Filtered PDF Error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting PDF: ' . $e->getMessage());
        }
    }

    // ============================================================
    // SIMPLE VERIFICATION – JUST COMPARE HASHES
    // ============================================================

    public function verifyHash(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:pdf|max:10240',
                'hash' => 'required|string|size:64'
            ]);

            $providedHash = trim($request->hash);

            // Check if hash exists in database
            $dbLog = RouterLog::where('hash', $providedHash)->first();
            
            if (!$dbLog) {
                return response()->json([
                    'valid' => false,
                    'message' => '❌ Hash not found in database!'
                ]);
            }

            // Extract hash from PDF
            $uploadedFile = $request->file('file');
            $pdfText = $this->extractTextFromPDF($uploadedFile->getPathname());
            
            $hashFromPDF = null;
            if (preg_match('/[a-f0-9]{64}/', $pdfText, $matches)) {
                $hashFromPDF = $matches[0];
            }

            Log::info('=== SIMPLE VERIFICATION ===');
            Log::info('Provided Hash : ' . $providedHash);
            Log::info('Stored Hash   : ' . $dbLog->hash);
            Log::info('Hash from PDF : ' . $hashFromPDF);

            // COMPARE
            if ($hashFromPDF === $providedHash) {
                return response()->json([
                    'valid' => true,
                    'log_id' => $dbLog->id,
                    'case_id' => $dbLog->case_id,
                    'router_id' => $dbLog->router_id,
                    'router_model' => $dbLog->router_model,
                    'investigator' => $dbLog->user ? $dbLog->user->name : 'N/A',
                    'generated_at' => $dbLog->log_generated_at ? $dbLog->log_generated_at->format('d/m/Y H:i:s') : 'N/A',
                    'stored_hash' => $dbLog->hash,
                    'provided_hash' => $providedHash,
                    'hash_from_pdf' => $hashFromPDF,
                    'message' => '✅ PDF VERIFIED! Hash matches database!'
                ]);
            }

            return response()->json([
                'valid' => false,
                'provided_hash' => $providedHash,
                'stored_hash' => $dbLog->hash,
                'hash_from_pdf' => $hashFromPDF,
                'message' => '❌ PDF TAMPERED! Hash mismatch!'
            ]);

        } catch (\Exception $e) {
            Log::error('Verify Error: ' . $e->getMessage());
            return response()->json([
                'valid' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function extractTextFromPDF($filePath)
    {
        if (class_exists('\PrinsFrank\PdfParser\PdfParser')) {
            try {
                $parser = new \PrinsFrank\PdfParser\PdfParser();
                $document = $parser->parseFile($filePath);
                $text = $document->getText();
                $text = trim($text);
                Log::info('✅ PrinsFrank extracted: ' . strlen($text) . ' chars');
                if (!empty($text) && strlen($text) > 100) {
                    return $text;
                }
            } catch (\Exception $e) {
                Log::warning('PrinsFrank failed: ' . $e->getMessage());
            }
        }

        if (class_exists('\Smalot\PdfParser\Parser')) {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($filePath);
                $text = $pdf->getText();
                $text = trim($text);
                Log::info('✅ Smalot extracted: ' . strlen($text) . ' chars');
                if (!empty($text) && strlen($text) > 100) {
                    return $text;
                }
            } catch (\Exception $e) {
                Log::warning('Smalot failed: ' . $e->getMessage());
            }
        }

        Log::warning('Using raw extraction as last resort');
        $content = file_get_contents($filePath);
        $content = preg_replace('/[\x00-\x1F\x80-\xFF]/', ' ', $content);
        preg_match_all('/[A-Za-z0-9\s\-\=\+\*\/\(\)\,\;\:\"\'\?\<\>\.\,\!\@\#\$\%\^\&\*\_\|\~\`\[\]\{\}]+/', $content, $matches);
        $text = implode(' ', $matches[0] ?? []);
        $text = trim($text);
        Log::info('⚠️ Raw extraction: ' . strlen($text) . ' chars');
        return $text;
    }

    private function getFilteredLogs(Request $request)
    {
        $query = RouterLog::query()
            ->leftJoin('users', 'router_logs.user_id', '=', 'users.id')
            ->select('router_logs.*', 'users.name as investigator_name');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('router_logs.case_id', 'like', '%' . $request->search . '%')
                  ->orWhere('router_logs.router_id', 'like', '%' . $request->search . '%')
                  ->orWhere('users.name', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('from_date')) {
            $query->whereDate('router_logs.log_generated_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('router_logs.log_generated_at', '<=', $request->to_date);
        }
        return $query->get();
    }
    
    /**
     * Delete single router log.
     */
    public function destroy($id)
    {
        try {
            $log = RouterLog::findOrFail($id);
            $log->delete();
    
            return back()->with('success', 'Log deleted successfully.');
    
        } catch (\Exception $e) {
            Log::error('Delete Log Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete log: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete router logs.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:router_logs,id',
        ]);
    
        try {
            $count = count($request->ids);
            RouterLog::whereIn('id', $request->ids)->delete();
    
            // AJAX ke liye JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $count . ' logs deleted successfully.',
                ]);
            }
    
            return back()->with('success', $count . ' logs deleted successfully.');
    
        } catch (\Exception $e) {
            Log::error('Bulk Delete Logs Error: ' . $e->getMessage());
    
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete logs.',
                ], 500);
            }
    
            return back()->with('error', 'Failed to delete logs: ' . $e->getMessage());
        }
    }
}