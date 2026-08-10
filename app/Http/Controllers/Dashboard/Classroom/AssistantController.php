<?php

namespace App\Http\Controllers\Dashboard\Classroom;

use Inertia\Inertia;
use App\Models\Classroom;
use App\Action\AIAssistant;
use Illuminate\Http\Request;
use App\Http\Controllers\Dashboard\Classroom\ClassroomController;

class AssistantController extends ClassroomController
{
    /**
     * Show ai assistant chat page
     */
    public function index(Classroom $classroom)
    {
        return $this->render('Dashboard/Classroom/Assistant');
    }


    /**
     * Ask question to assistant
     */
    public function ask(Request $request, AIAssistant $aIAssistant, Classroom $classroom)
    {
        $request->validate([
            'message' => 'required_without:files',
            'files.*' => 'nullable|file|max:20480',
        ], [
            'message.required_without' => 'Enter a message to send',
            'files.*.file' => 'Select a file to upload',
            'files.*.max' => 'Max upload file size 20 MB',
        ]);

        $message = $request->input('message', 'Write some html code');
        $files = $request->file('files', []);

        if (count($files) > 0) {
            $raw = $aIAssistant->chatWithFiles($message, $files);
        } else {
            $raw = $aIAssistant->chat($message);
        }

        $html = $aIAssistant->formatHtml($raw);

        return redirect()->back()->with('success', $html);
    }
}
