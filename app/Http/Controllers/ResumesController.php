<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use Illuminate\Support\Facades\Auth;
use App\Services\OllamaService;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Str;
use Exception; 

class ResumesController extends Controller
{
    protected $aiService;

    public function __construct(OllamaService $aiService)
    {
        $this->aiService = $aiService;
    }

    // Show list of all resumes for the authenticated user
    public function index()
    {
        $resumes = Resume::where('user_id', Auth::id())->get();
        return view('resumes.index', compact('resumes'));
    }

    // Show a specific resume, perform analysis if not available
    public function show($id)
    {
        $resume = Resume::where('user_id', Auth::id())->with('user')->findOrFail($id);

        // If the resume does not have AI analysis, try to perform it
        if (empty($resume->ai_analysis)) {
            $resumeText = null;
            $analysis = null;

            try {
                // Check mime type to decide how to parse
                if ($resume->mime_type === 'application/pdf') {
                    $parser = new Parser();
                    // Attempt to parse PDF content from binary data
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
                    'application/msword', // .doc
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' // .docx
                ])) {
                    // For PhpWord, we need to load from a file path.
                    // Save binary data to a temporary file first.
                    $tempFilePath = tempnam(sys_get_temp_dir(), 'resume_') . '.docx'; // Add extension hint
                    file_put_contents($tempFilePath, $resume->file_data);

                    if (file_exists($tempFilePath)) {
                        $phpWord = IOFactory::load($tempFilePath);
                        $text = '';
                        foreach ($phpWord->getSections() as $section) {
                            foreach ($section->getElements() as $element) {
                                // Check for text extraction capability more robustly
                                if (method_exists($element, 'getText')) {
                                    $text .= $element->getText();
                                } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                                     // Handle TextRun elements which contain Text elements
                                    foreach ($element->getElements() as $textElement) {
                                        if (method_exists($textElement, 'getText')) {
                                            $text .= $textElement->getText();
                                        }
                                    }
                                }
                                // Add a space or newline between elements if desired
                                $text .= " ";
                            }
                            $text .= "\n"; // Add newline between sections
                        }
                        $resumeText = trim($text);
                        unlink($tempFilePath); // Clean up the temporary file
                    } else {
                         Log::error("Failed to create temp file for resume ID {$resume->id}");
                    }
                } else {
                    // Handle unsupported mime types if necessary
                    Log::warning("Unsupported mime type '{$resume->mime_type}' for resume ID {$resume->id}. Skipping analysis.");
                }

                // If text was successfully extracted, perform AI analysis
                if (!is_null($resumeText) && !empty(trim($resumeText))) {
                    $analysis = $this->aiService->analyzeResume($resumeText, $resume);
                    $resume->ai_analysis = $analysis;
                    $resume->save();
                } elseif (!is_null($resumeText) && empty(trim($resumeText))) {
                     Log::warning("Extracted text was empty for resume ID {$resume->id}. Skipping analysis.");
                     // Optionally set a specific status in ai_analysis
                     // $resume->ai_analysis = 'Error: Could not extract text content.';
                     // $resume->save();
                }

            } catch (Exception $e) {
                // Catch ANY exception during parsing (covers invalid headers, corrupted files, etc.)
                Log::error("Error parsing resume ID {$resume->id} (Mime: {$resume->mime_type}): " . $e->getMessage());
                // Optionally set ai_analysis to indicate an error
                // $resume->ai_analysis = 'Error during processing: ' . $e->getMessage();
                // $resume->save();
            }
        }

        return view('resumes.show', compact('resume'));
    }


    public function download($id)
    {
        $resume = Resume::where('user_id', Auth::id())->findOrFail($id);

        // Return the file for download (binary data)
        // Ensure headers are correct
        return response($resume->file_data) // Use response() helper for binary data
            ->header('Content-Type', $resume->mime_type)
            ->header('Content-Disposition', 'attachment; filename="' . $resume->filename . '"');

        // streamDownload might be better for very large files, but requires a callback
        // return response()->streamDownload(function () use ($resume) {
        //     echo $resume->file_data;
        // }, $resume->filename, ['Content-Type' => $resume->mime_type]);
    }

    // This private method is likely intended for uploaded files, not DB data.
    // Keep it if you have an upload feature, but it's not used in the 'show' method.
    private function extractText($file)
    {
        // This expects an UploadedFile object, e.g., from a request $request->file('resume_file')
        $extension = strtolower($file->getClientOriginalExtension());

        try {
            if ($extension === 'pdf') {
                $parser = new Parser();
                $pdf = $parser->parseContent(file_get_contents($file->getRealPath()));
                return $pdf->getText();
            } elseif (in_array($extension, ['doc', 'docx'])) {
                $phpWord = IOFactory::load($file->getRealPath());
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
                return trim($text);
            }
        } catch (Exception $e) {
             Log::error("Failed to extract text from uploaded file: " . $e->getMessage());
             return 'Error extracting text from file.';
        }

        return 'Unsupported file type.';
    }
}
