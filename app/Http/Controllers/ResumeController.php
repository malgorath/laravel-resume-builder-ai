<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Resume;
use App\Services\OllamaService;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;
use App\Models\Skill;
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
            'resume.*' => 'required|mimes:pdf,doc,docx|max:2048', // Validate file type and size
        ]);

        if ($request->hasFile('resume')) {
            foreach ($request->file('resume') as $file) {
                // Save file data and metadata to the database
                Resume::create([
                    'user_id' => Auth::id(), // Associate the resume with the authenticated user
                    'filename' => $file->getClientOriginalName(), // Original file name
                    'mime_type' => $file->getClientMimeType(), // MIME type of the file
                    'file_data' => file_get_contents($file->getRealPath()), // Binary file data
                ]);
            }

            return back()->with('success', 'Files uploaded and stored in the database successfully!');
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
