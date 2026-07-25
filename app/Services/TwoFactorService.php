<?php

namespace App\Services;

use BaconQrCode\Renderer\Color\Alpha;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Str;

class TwoFactorService
{
    /**
     * Generate a random 16-character Base32 secret for 2FA.
     */
    public function generateSecret(): string
    {
        $validChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < 16; $i++) {
            $secret .= $validChars[rand(0, 31)];
        }
        return $secret;
    }

    /**
     * Generate 8 random recovery codes.
     */
    public function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::random(10) . '-' . Str::random(10);
        }
        return $codes;
    }

    /**
     * Generate OTPAuth QR Code SVG string.
     */
    public function getQrCodeSvg(string $company, string $email, string $secret): string
    {
        $otpUrl = sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s',
            rawurlencode($company),
            rawurlencode($email),
            $secret,
            rawurlencode($company)
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        return $writer->writeString($otpUrl);
    }

    /**
     * Verify a 6-digit TOTP code against secret.
     */
    public function verifyCode(string $secret, string $code): bool
    {
        $code = trim($code);
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return false;
        }

        // Calculate HMAC-SHA1 TOTP for current timestamp +- 1 window
        $time = floor(time() / 30);
        for ($i = -1; $i <= 1; $i++) {
            $calculatedCode = $this->calculateTotp($secret, $time + $i);
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    private function calculateTotp(string $secret, int $timeSlice): string
    {
        $secretKey = $this->base32Decode($secret);
        $pack = pack('N*', 0) . pack('N*', $timeSlice);
        $hash = hash_hmac('sha1', $pack, $secretKey, true);
        $offset = ord($hash[19]) & 0xf;
        $otp = (
            ((ord($hash[$offset + 0]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % 1000000;

        return str_pad((string)$otp, 6, '0', STR_PAD_LEFT);
    }

    private function base32Decode(string $secret): string
    {
        $secret = strtoupper($secret);
        $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $base32lookup = array_flip(str_split($base32chars));

        $binaryString = '';
        for ($i = 0; $i < strlen($secret); $i++) {
            if (!isset($base32lookup[$secret[$i]])) {
                continue;
            }
            $binaryString .= sprintf('%05b', $base32lookup[$secret[$i]]);
        }

        $bytes = [];
        for ($i = 0; $i < strlen($binaryString); $i += 8) {
            if ($i + 8 <= strlen($binaryString)) {
                $bytes[] = chr(bindec(substr($binaryString, $i, 8)));
            }
        }

        return implode('', $bytes);
    }
}
