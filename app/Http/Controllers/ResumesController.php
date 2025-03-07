<?php

namespace App\Http\Controllers;
use App\Models\Resume;
use Illuminate\Support\Facades\Auth;
use App\Services\OllamaService;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;

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

        // If the resume does not have AI analysis, perform it
        if (empty($resume->ai_analysis)) {
            $parser = new Parser();
            $pdf = $parser->parseContent($resume->file_data);
            $resumeText = $pdf->getText();
            $analysis = $this->aiService->analyzeResume($resumeText, $resume);
            $resume->ai_analysis = $analysis;
            $resume->save();
        }

        return view('resumes.show', compact('resume'));
    }

    public function download($id)
    {
        $resume = Resume::where('user_id', Auth::id())->findOrFail($id);

        // Return the file for download (binary data)
        return response()->stream(function () use ($resume) {
            echo $resume->file_data;
        }, 200, [
            'Content-Type' => $resume->mime_type,
            'Content-Disposition' => 'attachment; filename="' . $resume->filename . '"',
        ]);
    }

    private function extractText($file)
    {
        if ($file->getClientOriginalExtension() === 'pdf') {
            $parser = new Parser();
            $pdf = $parser->parseContent(file_get_contents($file));
            return $pdf->getText();
        } elseif (in_array($file->getClientOriginalExtension(), ['doc', 'docx'])) {
            $phpWord = IOFactory::load($file->getPathname());
            $text = '';
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    }
                }
            }
            return $text;
        }
        return 'Unsupported file type.';
    }

}
