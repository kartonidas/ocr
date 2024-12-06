<?php

namespace App\Services;

use App\Enums\OcrDocumentStatus;
use App\Jobs\OcrResult;
use App\Models\OcrDocument;
use Aws\S3\S3Client;
use Aws\Textract\TextractClient;
use Exception;
use Illuminate\Support\Facades\Storage;

class Ocr
{
    private const OCR_RESULT_DELAY = 15;

    private static function getTextractClient()
    {
        return new TextractClient([
            'region' => config('services.aws.region'),
            'version' => 'latest',
            'credentials' => [
                'key'    => config('services.aws.key'),
                'secret' => config('services.aws.secret')
            ]
        ]);
    }

    private static function getS3Client()
    {
        return new S3Client([
            'region' => config('services.aws.region'),
            'version' => 'latest',
        ]);
    }

    public static function process(OcrDocument $document)
    {
        if ($document->status !== OcrDocumentStatus::NEW) {
            throw new Exception(__('Nieprawidłowy status.'));
        }

        if (! Storage::exists($document->file)) {
            $document->status = OcrDocumentStatus::ERROR;
            $document->save();

            throw new Exception(__('Plik: :file nie istnieje.', ['file' => $document->file]));
        }

        $document->status = OcrDocumentStatus::PENDING;
        $document->save();

        self::uploadToS3($document->file);

        $ocrJobId = self::runAnalyzeDocument($document->file);

        if ($ocrJobId)
        {
            $document->aws_job_id = $ocrJobId;
            $document->save();

            OcrResult::dispatch($document)->delay(now()->addSeconds(self::OCR_RESULT_DELAY));
        }
    }

    public static function processResult(OcrDocument $document)
    {
        if ($document->status !== OcrDocumentStatus::PENDING) {
            throw new Exception(__('Nieprawidłowy status.'));
        }

        if (empty($document->aws_job_id)) {
            throw new Exception(__('Brak identyfikatora zlecenia.'));
        }

        $response = self::getAnalyzeDocumentResult($document->aws_job_id);

        if ($response['JobStatus'] === 'IN_PROGRESS') {
            OcrResult::dispatch($document)->delay(now()->addSeconds(self::OCR_RESULT_DELAY));
        } else {
            $tables = OcrParser::getTables($response);
            $forms = OcrParser::getForms($response);

            // Eksport do IdoSell

            // Usuwanie pliku z S3 (?)

            $document->status = OcrDocumentStatus::COMPLETED;
            $document->save();
        }
    }

    private static function uploadToS3($file)
    {
        $client = self::getS3Client();

        $client->putObject([
            'Bucket' => config('services.aws.bucket'),
            'Key' => $file,
            'Body' => Storage::get($file),
        ]);
    }

    private static function runAnalyzeDocument($file)
    {
        $client = self::getTextractClient();

        $response = $client->startDocumentAnalysis([
            'DocumentLocation' => [
                'S3Object' => [
                    'Bucket' => config('services.aws.bucket'),
                    'Name' => $file,
                ],
            ],
            'FeatureTypes' => ['TABLES', 'FORMS'],
        ]);

        return $response['JobId'];
    }

    private static function getAnalyzeDocumentResult($jobId)
    {
        $client = self::getTextractClient();

        return $client->getDocumentAnalysis(['JobId' => $jobId]);
    }
}
