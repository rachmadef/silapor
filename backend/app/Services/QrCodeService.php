<?php

namespace App\Services;

use App\Models\QrCode as QrCodeModel;
use App\Models\Student;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Str;
use Throwable;

class QrCodeService
{
    /**
     * Generate or get active QR code for a student.
     */
    public function generateForStudent(Student $student): QrCodeModel
    {
        if ($student->qrCode) {
            return $student->qrCode;
        }

        $token = 'STU-' . strtoupper(Str::random(12));

        return QrCodeModel::create([
            'student_id' => $student->id,
            'token' => $token,
            'is_active' => true,
        ]);
    }

    /**
     * Generate Data URI for a QR code token (PNG with SVG fallback).
     */
    public function getQrCodeDataUri(string $token): string
    {
        $qrCode = new QrCode(
            data: $token,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        );

        try {
            $writer = new PngWriter();
            return $writer->write($qrCode)->getDataUri();
        } catch (Throwable $e) {
            $writer = new SvgWriter();
            return $writer->write($qrCode)->getDataUri();
        }
    }
}
