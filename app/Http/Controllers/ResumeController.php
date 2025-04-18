<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Resume;
use App\Services\OllamaService;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;
use App\Models\Skill;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ResumeController extends Controller
{
    protected $aiService;

    public function __construct(OllamaService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function showUploadForm()
    {
        return view('resumes.upload');
    }

    public function upload(Request $request)
    {

        $request->validate([
            'resume.*' => 'required|mimes:pdf,doc,docx|max:10240', // Validate file type and size
        ]);

        // --- Add DB Listener for Debugging ---
        DB::listen(function ($query) {
            // Log the SQL query and bindings
            Log::debug("Executing SQL: " . $query->sql);
            // Log bindings carefully - file_data will be huge/binary
            Log::debug("Bindings: ", array_map(function($binding) {
                // Avoid logging huge binary data directly if it causes issues
                return is_string($binding) && strlen($binding) > 1000 ? '[Binary Data Snippet: ' . substr(bin2hex($binding), 0, 50) . '...]' : $binding;
            }, $query->bindings));
        });
        // --- End DB Listener ---

        if ($request->hasFile('resume')) {
            $successCount = 0; // <-- Add this line
            $errorCount = 0;   // <-- Add this line

            foreach ($request->file('resume') as $file) {
                try {
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $nameWithoutExtension = pathinfo($originalName, PATHINFO_FILENAME);
                    $slug = Str::slug($nameWithoutExtension);
                    $safeFilename = $slug . '_' . time() . '.' . $extension;

                    // Read file content
                    $fileData = file_get_contents($file->getRealPath());

                    // --- Convert binary data to hex string ---
                    $hexData = bin2hex($fileData);
                    // --- End conversion ---
                    $userId = Auth::id(); // Get user ID
                    $mimeType = $file->getClientMimeType(); // Get mime type
                    $now = now(); // Get current timestamp

                    $sql = 'insert into "resumes" ("user_id", "filename", "mime_type", "file_data", "created_at", "updated_at") values (:user_id, :filename, :mime_type, decode(:filedata, \'hex\'), :created_at, :updated_at)'; // Note: escaped 'hex'

                    $bindings = [
                        'user_id' => $userId,
                        'filename' => $safeFilename,
                        'mime_type' => $mimeType,
                        'filedata' => $hexData,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                
                    DB::insert($sql, $bindings);
                    // --- End Replacement ---

                    $successCount++; // Assuming you have this from previous steps

                } catch (\Exception $e) {
                    $errorCount++; // Assuming you have this
                    Log::error("Error processing file '{$originalName}': " . $e->getMessage());
                    Log::error($e->getTraceAsString());
                    // Handle error reporting as needed
                }

            }

            $message = '';
            if ($successCount > 0) {
                $message .= "{$successCount} file(s) uploaded successfully. ";
            }
            if ($errorCount > 0) {
                $message .= "{$errorCount} file(s) failed to upload. Check logs for details.";
                // Return with ERRORS if any file failed
                return back()->withErrors(['resume' => $message])->withInput();
            }
            // Only return SUCCESS if errorCount is 0
            return back()->with('success', $message);
            // --- End Corrected Logic ---
        }

        return back()->withErrors(['resume' => 'Please upload at least one file.']);
    }

    private function extractText($file)
    {
        $text = '';

        if ($file->getClientOriginalExtension() === 'pdf') {
            $parser = new Parser();
            $pdf = $parser->parseContent(file_get_contents($file));
            $text = $pdf->getText();
        } elseif (in_array($file->getClientOriginalExtension(), ['doc', 'docx'])) {
            $phpWord = IOFactory::load($file->getPathname());
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    }
                }
            }
        } else {
            $text = 'Unsupported file type.';
        }

        // Process the extracted text using OllamaService
        $processedText = $this->aiService->processText($text);

        return $processedText;
    }

    public function download($id)
    {
        $resume = Resume::findOrFail($id);

        return response($resume->file_data)
            ->header('Content-Type', $resume->mime_type)
            ->header('Content-Disposition', 'attachment; filename="' . $resume->filename . '"');
    }
}
