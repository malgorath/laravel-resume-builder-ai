<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Illuminate\Support\Facades\Auth;
use App\Services\OllamaService;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Exception;

class ResumesController extends Controller
{
    protected $aiService;

    public function __construct(OllamaService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Show upload form
     */
    public function showUploadForm()
    {
        return view('resumes.upload');
    }

    /**
     * Upload one or more resumes
     */
    public function upload(Request $request)
    {
        $request->validate([
            'resume.*' => 'required|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($request->hasFile('resume')) {
            $successCount = 0;
            $errorCount = 0;

            foreach ($request->file('resume') as $file) {
                try {
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $nameWithoutExtension = pathinfo($originalName, PATHINFO_FILENAME);
                    $slug = Str::slug($nameWithoutExtension);
                    $safeFilename = $slug . '_' . time() . '.' . $extension;

                    // Read file content as binary
                    $fileData = file_get_contents($file->getRealPath());

                    // Use Eloquent model to handle binary data across all databases
                    Resume::create([
                        'user_id' => Auth::id(),
                        'filename' => $safeFilename,
                        'mime_type' => $file->getClientMimeType(),
                        'file_data' => $fileData,
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error("Error processing file '{$originalName}': " . $e->getMessage());
                    Log::error($e->getTraceAsString());
                }
            }

            $message = '';
            if ($successCount > 0) {
                $message .= "{$successCount} file(s) uploaded successfully. ";
            }
            if ($errorCount > 0) {
                $message .= "{$errorCount} file(s) failed to upload. Check logs for details.";
                return back()->withErrors(['resume' => $message])->withInput();
            }
            return back()->with('success', $message);
        }

        return back()->withErrors(['resume' => 'Please upload at least one file.']);
    }

    /**
     * Show list of all resumes for the authenticated user
     */
    public function index()
    {
        $resumes = Resume::where('user_id', Auth::id())->get();
        return view('resumes.index', compact('resumes'));
    }

    /**
     * Show a specific resume, perform analysis if not available
     */
    public function show($id)
    {
        $resume = Resume::with('user')->findOrFail($id);
        $this->authorize('view', $resume);

        // If the resume does not have AI analysis, try to perform it
        if (empty($resume->ai_analysis)) {
            $resumeText = null;
            $analysis = null;

            try {
                // Check mime type to decide how to parse
                if ($resume->mime_type === 'application/pdf') {
                    $parser = new Parser();
                    $pdfContent = null;

                    if (is_resource($resume->file_data)) {
                        rewind($resume->file_data);
                        $pdfContent = stream_get_contents($resume->file_data);
                    } elseif (is_string($resume->file_data)) {
                        $pdfContent = $resume->file_data;
                    }
                
                    if ($pdfContent !== null) {
                        $pdf = $parser->parseContent($pdfContent);
                        $resumeText = $pdf->getText();
                    } else {
                        Log::error("Could not read PDF content for resume ID {$resume->id}. file_data type: " . gettype($resume->file_data));
                    }
                } elseif (in_array($resume->mime_type, [
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ])) {
                    $tempFilePath = tempnam(sys_get_temp_dir(), 'resume_') . '.docx';
                    file_put_contents($tempFilePath, $resume->file_data);

                    if (file_exists($tempFilePath)) {
                        $phpWord = IOFactory::load($tempFilePath);
                        $text = '';
                        foreach ($phpWord->getSections() as $section) {
                            foreach ($section->getElements() as $element) {
                                if (method_exists($element, 'getText')) {
                                    $text .= $element->getText();
                                } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                                    foreach ($element->getElements() as $textElement) {
                                        if (method_exists($textElement, 'getText')) {
                                            $text .= $textElement->getText();
                                        }
                                    }
                                }
                                $text .= " ";
                            }
                            $text .= "\n";
                        }
                        $resumeText = trim($text);
                        unlink($tempFilePath);
                    } else {
                        Log::error("Failed to create temp file for resume ID {$resume->id}");
                    }
                } else {
                    Log::warning("Unsupported mime type '{$resume->mime_type}' for resume ID {$resume->id}. Skipping analysis.");
                }

                // If text was successfully extracted, perform AI analysis
                if (!is_null($resumeText) && !empty(trim($resumeText))) {
                    $analysis = $this->aiService->analyzeResume($resumeText, $resume);
                    $resume->ai_analysis = $analysis;
                    $resume->save();
                } elseif (!is_null($resumeText) && empty(trim($resumeText))) {
                    Log::warning("Extracted text was empty for resume ID {$resume->id}. Skipping analysis.");
                }
            } catch (Exception $e) {
                Log::error("Error parsing resume ID {$resume->id} (Mime: {$resume->mime_type}): " . $e->getMessage());
            }
        }

        return view('resumes.show', compact('resume'));
    }

    /**
     * Download a specific resume
     */
    public function download($id)
    {
        $resume = Resume::findOrFail($id);
        $this->authorize('download', $resume);

        return response($resume->file_data)
            ->header('Content-Type', $resume->mime_type)
            ->header('Content-Disposition', 'attachment; filename="' . $resume->filename . '"');
    }

    /**
     * Reset AI analysis for a resume
     */
    public function resetAnalysis($id)
    {
        $resume = Resume::findOrFail($id);
        $this->authorize('update', $resume);
        $resume->ai_analysis = null;
        $resume->save();
        return redirect()->back()->with('success', 'AI analysis reset successfully.');
    }

    /**
     * Match resume to a job
     */
    public function matchJob(Request $request, $id)
    {
        $resume = Resume::where('user_id', Auth::id())->findOrFail($id);
        $this->authorize('view', $resume);

        $request->validate([
            'job_description' => 'required|string',
        ]);

        // Extract text from resume
        $resumeText = $this->extractResumeText($resume);
        
        if (empty($resumeText)) {
            return back()->withErrors(['error' => 'Could not extract text from resume.']);
        }

        $matchResult = $this->aiService->matchJob($resumeText, $request->job_description);

        return back()->with('match_result', $matchResult);
    }

    /**
     * Generate cover letter
     */
    public function generateCoverLetter(Request $request, $id)
    {
        $resume = Resume::where('user_id', Auth::id())->findOrFail($id);
        $this->authorize('view', $resume);

        $request->validate([
            'job_description' => 'required|string',
        ]);

        // Extract text from resume
        $resumeText = $this->extractResumeText($resume);
        
        if (empty($resumeText)) {
            return back()->withErrors(['error' => 'Could not extract text from resume.']);
        }

        $userName = Auth::user()->name;
        $coverLetter = $this->aiService->generateCoverLetter($resumeText, $request->job_description, $userName);

        return back()->with('cover_letter', $coverLetter);
    }

    /**
     * Extract text from resume for AI processing
     */
    private function extractResumeText($resume)
    {
        try {
            if ($resume->mime_type === 'application/pdf') {
                $parser = new Parser();
                $pdfContent = is_string($resume->file_data) ? $resume->file_data : null;
                if ($pdfContent) {
                    $pdf = $parser->parseContent($pdfContent);
                    return $pdf->getText();
                }
            } elseif (in_array($resume->mime_type, [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ])) {
                $tempFilePath = tempnam(sys_get_temp_dir(), 'resume_') . '.docx';
                file_put_contents($tempFilePath, $resume->file_data);
                if (file_exists($tempFilePath)) {
                    $phpWord = IOFactory::load($tempFilePath);
                    $text = '';
                    foreach ($phpWord->getSections() as $section) {
                        foreach ($section->getElements() as $element) {
                            if (method_exists($element, 'getText')) {
                                $text .= $element->getText();
                            } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                                foreach ($element->getElements() as $textElement) {
                                    if (method_exists($textElement, 'getText')) {
                                        $text .= $textElement->getText();
                                    }
                                }
                            }
                            $text .= " ";
                        }
                        $text .= "\n";
                    }
                    unlink($tempFilePath);
                    return trim($text);
                }
            }
        } catch (Exception $e) {
            Log::error("Error extracting text from resume: " . $e->getMessage());
        }
        return null;
    }
}
