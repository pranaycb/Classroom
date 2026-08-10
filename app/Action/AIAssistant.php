<?php

namespace App\Action;

use Gemini\Data\Blob;
use Gemini\Enums\MimeType;
use Gemini\Laravel\Facades\Gemini;

class AIAssistant
{
    /**
     * Send a simple text + code/math prompt
     */
    public function chat(string $message): string
    {
        // Add instructions for code/math
        $instruction = "
You are an AI tutor.
Wrap all text and code in proper markdown.
For math, always output MathML inside <math>...</math> tags.
Never output LaTeX.
Explain step by step.
";

        $prompt = $instruction . "\n\n" . $message;

        // Use a generative model; pick the model name you need
        $result = Gemini::generativeModel(model: 'gemini-2.0-flash')
            ->generateContent($prompt);

        return $result->text();
    }

    /**
     * Send a prompt + image/file to Gemini (vision)
     * Accepts UploadedFile instances from Laravel
     */
    public function chatWithFiles(string|null $message, array $files): string
    {
        $parts = [];

        // Instruction + user message
        $instruction = "
You are an AI tutor.
Wrap all text and code in proper markdown.
For math, ALWAYS output MathML inside <math>...</math> tags.
Never output LaTeX.
Explain step by step.
";
        $parts[] = $instruction;
        $parts[] = $message ?? 'analyze';

        foreach ($files as $file) {
            $data = file_get_contents($file->getRealPath());
            $base64 = base64_encode($data);
            $mime = $file->getMimeType();

            $parts[] = new Blob(
                mimeType: MimeType::from($mime),
                data: $base64
            );
        }

        $result = Gemini::generativeModel(model: 'gemini-2.0-flash')
            ->generateContent($parts);

        return $result->text();
    }

    /**
     * Format Gemini response to HTML (to render in front-end)
     * - Code blocks (```) → <pre><code>
     * - Keeps LaTeX for MathLive to render
     */
    public function formatHtml(string $message): string
    {
        return $message;
    }
}
