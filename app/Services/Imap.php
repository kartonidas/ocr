<?php

namespace App\Services;

use App\Jobs\OcrJob;
use App\Models\OcrDocument;
use Illuminate\Support\Facades\Storage;
use PhpImap\IncomingMailAttachment;
use PhpImap\Mailbox;

class Imap
{
    private $imap;
    private array $processEmailsFrom = [];

    public function __construct()
    {
        $this->processEmailsFrom = [
            'arturpatura@gmail.com'
        ];

        $this->imap = new Mailbox(
            config('services.imap.path'),
            config('services.imap.email'),
            config('services.imap.password')
        );
    }

    public function processEmails()
    {
        $this->imap->switchMailbox(config('services.imap.path') . 'INBOX');

        $mailIds = $this->imap->searchMailbox('ALL');

        foreach ($mailIds as $mailId) {
            $mail = $this->imap->getMail($mailId);

            if (in_array($mail->fromAddress, $this->processEmailsFrom)) {
                if ($mail->hasAttachments()) {
                    $attachments = $mail->getAttachments();

                    foreach ($attachments as $attachment) {
                        if ($attachment->mimeType == 'application/pdf') {

                            $file = $this->storeInvoice($mailId, $attachment);

//                            $this->imap->moveMail($mailId, 'ocr');

                            $ocrDocument = OcrDocument::create([
                                'mail_id' => $mailId,
                                'mail_subject' => $mail->subject,
                                'file' => $file
                            ]);

                            OcrJob::dispatch($ocrDocument);

                            continue;
                        }
                    }
                }
            }
        }
    }

    private function storeInvoice(int $mailId, IncomingMailAttachment $attachment): string
    {
        $path = 'invoices/' . date('Y-m');
        if (! Storage::exists($path)) {
            Storage::makeDirectory($path);
        }

        $invoiceFileName = $path . '/' . $mailId . '-' . time() . '.pdf';
        Storage::put($invoiceFileName, $attachment->getContents());

        return $invoiceFileName;
    }
}
