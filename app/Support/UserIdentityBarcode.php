<?php

namespace App\Support;

use App\Models\User;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use InvalidArgumentException;

class UserIdentityBarcode
{
    public function canRender(?string $identityNumber): bool
    {
        return filled(trim((string) $identityNumber));
    }

    public function svg(string $identityNumber, int $size = 180, int $margin = 12): string
    {
        $value = trim($identityNumber);

        if ($value === '') {
            throw new InvalidArgumentException('Nomor identitas wajib diisi untuk membuat barcode.');
        }

        $result = (new Builder(
            writer: new SvgWriter(),
            writerOptions: [
                SvgWriter::WRITER_OPTION_EXCLUDE_XML_DECLARATION => true,
            ],
            validateResult: false,
            data: $value,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: $size,
            margin: $margin,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        ))->build();

        $svg = $result->getString();
        $metadata = '<title>QR Code ' . e($value) . '</title><desc>' . e($value) . '</desc>';

        return preg_replace('/<svg\b([^>]*)>/', '<svg$1>' . $metadata, $svg, 1) ?? $svg;
    }

    public function downloadResponse(User $user): Response
    {
        abort_unless($this->canRender($user->identity_number), 404);

        $filename = 'qr-code-' . Str::slug($user->identity_number) . '.svg';

        return response($this->svg($user->identity_number, 320, 16), 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
