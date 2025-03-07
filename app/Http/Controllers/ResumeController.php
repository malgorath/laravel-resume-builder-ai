<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Resume;
use App\Services\OllamaService;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;

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
            'resume' => 'required|mimes:pdf,doc,docx|max:2048',
        ]);

        $file = $request->file('resume');
        $resumeText = $this->extractText($file);

        $resume = new Resume([
            'user_id' => Auth::id(),
            'filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_data' => file_get_contents($file),
        ]);
        $resume->save();

       // Analyze the resume with Ollama
        $analysis = $this->aiService->analyzeResume($resumeText, $resume);

        return back()->with('success', 'Resume uploaded! AI Analysis: ' . substr($analysis, 0, 200) . '...');
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

    public function download($id)
    {
        $resume = Resume::findOrFail($id);

        return response($resume->file_data)
            ->header('Content-Type', $resume->mime_type)
            ->header('Content-Disposition', 'attachment; filename="' . $resume->filename . '"');
    }
}
