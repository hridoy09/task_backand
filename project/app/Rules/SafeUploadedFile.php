<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;
use finfo;

class SafeUploadedFile implements Rule
{
    protected array $allowedExtensions;
    protected array $allowedMimes;
    protected int $maxBytes;
    protected array $errors = [];

    /**
     * @param array $allowedExtensions e.g. ['jpg','png','pdf']
     * @param array $allowedMimes e.g. ['image/jpeg','image/png','application/pdf']
     * @param int $maxBytes e.g. 5 * 1024 * 1024
     */
    public function __construct(array $allowedExtensions = [], array $allowedMimes = [], $maxBytes = null)
    {
        if(!$maxBytes) {
            $uploadMax = $this->toBytes(ini_get('upload_max_filesize'));
            $postMax   = $this->toBytes(ini_get('post_max_size'));

            $maxBytes = min($uploadMax, $postMax);
        }
        
        $this->allowedExtensions = array_map('strtolower', $allowedExtensions);
        $this->allowedMimes = $allowedMimes;
        $this->maxBytes = $maxBytes;
    }

    public function passes($attribute, $value)
    {
        // must be an UploadedFile
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            $this->errors[] = 'Invalid upload.';
            return false;
        }

        // size check
        if ($value->getSize() > $this->maxBytes) {
            $this->errors[] = 'File too large.';
            return false;
        }

        $path = $value->getRealPath();
        if (! $path || ! is_readable($path)) {
            $this->errors[] = 'Upload unreadable.';
            return false;
        }

        // 1) extension check
        $ext = strtolower($value->getClientOriginalExtension() ?: pathinfo($value->getClientOriginalName(), PATHINFO_EXTENSION));
        if ($this->allowedExtensions && ! in_array($ext, $this->allowedExtensions, true)) {
            $this->errors[] = "Extension '{$ext}' not allowed.";
            return false;
        }

        // 2) server-detected MIME using finfo
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $detectedMime = $finfo->file($path);
        if ($this->allowedMimes && ! in_array($detectedMime, $this->allowedMimes, true)) {
            $this->errors[] = "MIME type mismatch: {$detectedMime}.";
            return false;
        }

        // 3) magic-number (file signature) check for common types
        if (! $this->checkMagicNumber($path, $ext)) {
            $this->errors[] = 'File signature does not match declared type.';
            return false;
        }

        // 4) content sniffing for PHP / embedded scripts in text-like uploads
        if ($this->isPlainText($detectedMime, $ext) && $this->containsExecutablePayload($path)) {
            $this->errors[] = 'Executable code detected inside the uploaded file.';
            return false;
        }

        // 5) double extension or .php in filename
        $lowerName = strtolower($value->getClientOriginalName());
        if (preg_match('/\.(php|phtml|phar)(\.|$)/', $lowerName)) {
            $this->errors[] = 'Filename contains disallowed executable extension.';
            return false;
        }

        // all checks passed
        return true;
    }

    public function message()
    {
        return implode(' ', $this->errors) ?: 'Invalid file.';
    }

    protected function isPlainText(?string $mime, string $ext): bool
    {
        $textMimes = ['text/plain', 'application/xml', 'application/json'];
        $textExts = ['txt', 'csv', 'json', 'xml', 'html', 'htm'];
        return in_array($mime, $textMimes, true) || in_array($ext, $textExts, true);
    }

    protected function containsExecutablePayload(string $path): bool
    {
        // read first N bytes + some tail to check for <?php, <script>, eval( etc
        $contents = file_get_contents($path, false, null, 0, 5120); // 5KB
        if ($contents === false) {
            return true; // treat unreadable as suspicious
        }

        $suspicious = ['<?php', '<?=','<script', 'eval(', 'base64_decode(', 'shell_exec(', 'passthru('];
        foreach ($suspicious as $needle) {
            if (stripos($contents, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    protected function checkMagicNumber(string $path, string $ext): bool
    {
        // Map of extension => allowed signature prefixes (hex)
        $signatures = [
            'jpg'  => ['ffd8ff'],
            'jpeg' => ['ffd8ff'],
            'png'  => ['89504e47'],
            'gif'  => ['47494638'],
            'pdf'  => ['25504446'],
            'zip'  => ['504b0304'],
            'docx' => ['504b0304'], // docx is actually zipped xml
            'xlsx' => ['504b0304'],
            'pptx' => ['504b0304'],
            // add more as needed
        ];

        // If extension has known signatures, check them
        if (isset($signatures[$ext])) {
            $fh = fopen($path, 'rb');
            if (! $fh) return false;
            $bytes = strtolower(bin2hex(fread($fh, 8)));
            fclose($fh);

            foreach ($signatures[$ext] as $sig) {
                if (strpos($bytes, strtolower($sig)) === 0) {
                    return true;
                }
            }
            return false;
        }

        // If no signature rule for this extension, be conservative: allow if finfo detected something
        return true;
    }

    private function toBytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        $val = (int)$val;

        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }

        return $val;
    }
}
