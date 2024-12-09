<?php

namespace App\Http\Controllers;

use App\Enums\OcrDocumentType;
use App\Models\OcrDocument;
use Illuminate\Support\Facades\Storage;

class OcrDocumentController extends Controller
{
    public function index()
    {
        $documents = OcrDocument::orderBy('id', 'desc')->paginate(50);

        return view('documents.index', compact('documents'));
    }

    public function show(OcrDocument $document)
    {
        $data = [
            'document' => $document,
            'tables' => $document->getTables(),
            'forms' => $document->texts()->where('type', OcrDocumentType::FORMS)->get(),
        ];


        return view('documents.show', $data);
    }

    public function pdf(OcrDocument $document, bool $inline = false)
    {
        if (Storage::exists($document->file)) {
            if ($inline) {
                return response()->download(
                    Storage::path($document->file),
                    basename($document->file),
                    ['Content-Type' => 'application/pdf'],
                    'inline'
                );
            } else {
                return Storage::download($document->file);
            }
        }
    }
}
