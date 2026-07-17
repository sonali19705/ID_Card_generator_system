<?php
/**
 * MiniPDF - a tiny, dependency-free PDF writer used to generate the
 * professional ID card PDF for this project. Only the small subset of the
 * PDF spec needed for text, filled rectangles and JPEG photos is implemented,
 * which keeps the whole project "Core PHP only" with no Composer libraries.
 */
class MiniPDF
{
    private $width;
    private $height;
    private $pages = [];      // array of content streams (strings)
    private $pageImages = []; // array of image object refs used per page

    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function addPage()
    {
        $this->pages[] = "";
        $this->pageImages[] = [];
        return count($this->pages) - 1;
    }

    private function esc($text)
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    public function setFillColor($pageIndex, $r, $g, $b)
    {
        $this->pages[$pageIndex] .= sprintf("%.3F %.3F %.3F rg\n", $r / 255, $g / 255, $b / 255);
    }

    public function rect($pageIndex, $x, $y, $w, $h, $fill = true)
    {
        // PDF y-axis is bottom-up, our x/y here is top-left based like CSS
        $y = $this->height - $y - $h;
        $this->pages[$pageIndex] .= sprintf("%.2F %.2F %.2F %.2F re %s\n", $x, $y, $w, $h, $fill ? 'f' : 'S');
    }

    public function text($pageIndex, $x, $y, $text, $size = 10, $bold = false, $r = 0, $g = 0, $b = 0)
    {
        $font = $bold ? 'F2' : 'F1';
        $y = $this->height - $y;
        $this->pages[$pageIndex] .= sprintf(
            "BT %.3F %.3F %.3F rg /%s %.2F Tf %.2F %.2F Td (%s) Tj ET\n",
            $r / 255, $g / 255, $b / 255, $font, $size, $x, $y, $this->esc($text)
        );
    }

    /**
     * Draw a JPEG image (raw jpeg binary data) into the given box.
     * Returns nothing; registers the image against the page automatically.
     */
    public function image($pageIndex, $jpegData, $x, $y, $w, $h)
    {
        $info = @getimagesizefromstring($jpegData);
        if (!$info) return;
        $imgIndex = count($this->pageImages[$pageIndex]);
        $name = "Im" . $pageIndex . "_" . $imgIndex;
        $this->pageImages[$pageIndex][$name] = [
            'data' => $jpegData,
            'width' => $info[0],
            'height' => $info[1],
        ];
        $y = $this->height - $y - $h;
        $this->pages[$pageIndex] .= sprintf("q %.2F 0 0 %.2F %.2F %.2F cm /%s Do Q\n", $w, $h, $x, $y, $name);
    }

    /** Output the finished PDF as a binary string. */
    public function output()
    {
        $objects = [];
        $objNum = 1;

        // 1: Catalog, 2: Pages (filled later)
        $catalogNum = $objNum++;
        $pagesNum = $objNum++;

        $fontRegularNum = $objNum++;
        $fontBoldNum = $objNum++;

        $objects[$fontRegularNum] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
        $objects[$fontBoldNum] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>";

        $pageNums = [];
        $contentNums = [];
        $imageObjNums = []; // pageIndex => [name => objNum]

        foreach ($this->pages as $i => $content) {
            $pageNums[$i] = $objNum++;
        }

        foreach ($this->pages as $i => $content) {
            // Register image objects for this page
            $imgResourceEntries = "";
            $imageObjNums[$i] = [];
            foreach ($this->pageImages[$i] as $name => $img) {
                $imgNum = $objNum++;
                $imageObjNums[$i][$name] = $imgNum;
                $objects[$imgNum] = [
                    'stream' => $img['data'],
                    'dict' => sprintf(
                        "<< /Type /XObject /Subtype /Image /Width %d /Height %d /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length %d >>",
                        $img['width'], $img['height'], strlen($img['data'])
                    ),
                ];
                $imgResourceEntries .= "/$name $imgNum 0 R ";
            }

            $contentNum = $objNum++;
            $contentNums[$i] = $contentNum;
            $objects[$contentNum] = [
                'stream' => $content,
                'dict' => "<< /Length " . strlen($content) . " >>",
            ];

            $resources = "<< /Font << /F1 $fontRegularNum 0 R /F2 $fontBoldNum 0 R >>";
            if ($imgResourceEntries !== "") {
                $resources .= " /XObject << $imgResourceEntries >>";
            }
            $resources .= " >>";

            $objects[$pageNums[$i]] = sprintf(
                "<< /Type /Page /Parent %d 0 R /MediaBox [0 0 %.2F %.2F] /Resources %s /Contents %d 0 R >>",
                $pagesNum, $this->width, $this->height, $resources, $contentNum
            );
        }

        $kids = implode(' ', array_map(function ($n) {
            return "$n 0 R";
        }, $pageNums));
        $objects[$pagesNum] = "<< /Type /Pages /Kids [$kids] /Count " . count($pageNums) . " >>";
        $objects[$catalogNum] = "<< /Type /Catalog /Pages $pagesNum 0 R >>";

        // Build the final PDF byte stream with a valid xref table
        ksort($objects);
        $pdf = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as $num => $obj) {
            $offsets[$num] = strlen($pdf);
            if (is_array($obj)) {
                $pdf .= "$num 0 obj\n" . $obj['dict'] . "\nstream\n" . $obj['stream'] . "\nendstream\nendobj\n";
            } else {
                $pdf .= "$num 0 obj\n" . $obj . "\nendobj\n";
            }
        }

        $xrefStart = strlen($pdf);
        $maxNum = max(array_keys($objects));
        $pdf .= "xref\n0 " . ($maxNum + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($n = 1; $n <= $maxNum; $n++) {
            if (isset($offsets[$n])) {
                $pdf .= sprintf("%010d 00000 n \n", $offsets[$n]);
            } else {
                $pdf .= "0000000000 65535 f \n";
            }
        }

        $pdf .= "trailer\n<< /Size " . ($maxNum + 1) . " /Root $catalogNum 0 R >>\n";
        $pdf .= "startxref\n$xrefStart\n%%EOF";

        return $pdf;
    }
}

/**
 * Loads an image file from disk (jpg/png/gif) and returns raw JPEG binary
 * data suitable for embedding in the PDF, resized/cropped to a square.
 * Returns null if the file cannot be read.
 */
function preparePhotoForPdf($path, $squareSize = 300)
{
    if (empty($path) || !file_exists($path)) return null;

    $info = @getimagesize($path);
    if (!$info) return null;

    switch ($info['mime']) {
        case 'image/jpeg':
            $src = @imagecreatefromjpeg($path);
            break;
        case 'image/png':
            $src = @imagecreatefrompng($path);
            break;
        case 'image/gif':
            $src = @imagecreatefromgif($path);
            break;
        default:
            return null;
    }
    if (!$src) return null;

    $srcW = imagesx($src);
    $srcH = imagesy($src);
    $side = min($srcW, $srcH);
    $srcX = intval(($srcW - $side) / 2);
    $srcY = intval(($srcH - $side) / 2);

    $dst = imagecreatetruecolor($squareSize, $squareSize);
    $white = imagecolorallocate($dst, 255, 255, 255);
    imagefill($dst, 0, 0, $white);
    imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $squareSize, $squareSize, $side, $side);

    ob_start();
    imagejpeg($dst, null, 90);
    $data = ob_get_clean();

    imagedestroy($src);
    imagedestroy($dst);

    return $data;
}

/**
 * Builds the two-sided professional ID card PDF for a request and saves it
 * to $outputPath. $data keys: name, designation, roll_no, department, dob,
 * blood_group, email, mobile, photo_path (absolute path or null), request_id.
 */
function generateIdCardPdf($data, $outputPath)
{
    // CR80 card size (85.6mm x 54mm) in PDF points, landscape.
    $w = 243.0;
    $h = 153.0;

    $pdf = new MiniPDF($w, $h);
    $brand = [0, 75, 110];   // #004B6E
    $brandDark = [0, 40, 59]; // #00283b

    // ---------------- FRONT SIDE ----------------
    $front = $pdf->addPage();
    $pdf->setFillColor($front, 255, 255, 255);
    $pdf->rect($front, 0, 0, $w, $h);

    // Header band
    $pdf->setFillColor($front, $brand[0], $brand[1], $brand[2]);
    $pdf->rect($front, 0, 0, $w, 34);
    $pdf->text($front, 12, 22, "COLLEGE ID CARD", 12, true, 255, 255, 255);

    // Photo box
    $photoX = 14;
    $photoY = 44;
    $photoSize = 66;
    if (!empty($data['photo_data'])) {
        $pdf->image($front, $data['photo_data'], $photoX, $photoY, $photoSize, $photoSize);
    } else {
        $pdf->setFillColor($front, 230, 230, 230);
        $pdf->rect($front, $photoX, $photoY, $photoSize, $photoSize);
        $pdf->text($front, $photoX + 10, $photoY + 36, "No Photo", 8, false, 120, 120, 120);
    }
    $pdf->setFillColor($front, 0, 0, 0);
    $pdf->rect($front, $photoX, $photoY, $photoSize, $photoSize, false);

    // Details next to photo
    $tx = $photoX + $photoSize + 12;
    $ty = 58;
    $pdf->text($front, $tx, $ty, wordwrapShort($data['name'], 22), 11, true, 20, 20, 20);
    $pdf->text($front, $tx, $ty + 16, $data['designation'], 9, false, 90, 90, 90);
    $pdf->text($front, $tx, $ty + 32, "ID: " . $data['roll_no'], 9, false, 20, 20, 20);
    $pdf->text($front, $tx, $ty + 46, $data['department'], 9, false, 20, 20, 20);

    // Footer band
    $pdf->setFillColor($front, $brandDark[0], $brandDark[1], $brandDark[2]);
    $pdf->rect($front, 0, $h - 16, $w, 16);
    $pdf->text($front, 12, $h - 5, "Request #" . $data['request_id'], 7, false, 255, 255, 255);

    // ---------------- BACK SIDE ----------------
    $back = $pdf->addPage();
    $pdf->setFillColor($back, 245, 245, 245);
    $pdf->rect($back, 0, 0, $w, $h);

    $pdf->setFillColor($back, $brand[0], $brand[1], $brand[2]);
    $pdf->rect($back, 0, 0, $w, 20);
    $pdf->text($back, 12, 14, "ID CARD DETAILS", 10, true, 255, 255, 255);

    $lines = [
        "Date of Birth: " . ($data['dob'] ?: '-'),
        "Blood Group: " . ($data['blood_group'] ?: '-'),
        "Email: " . ($data['email'] ?: '-'),
        "Mobile: " . ($data['mobile'] ?: '-'),
    ];
    $ly = 40;
    foreach ($lines as $line) {
        $pdf->text($back, 14, $ly, $line, 9, false, 40, 40, 40);
        $ly += 16;
    }

    // Barcode-like stripes
    $barY = $h - 40;
    $stripeX = 14;
    $stripeW = $w - 28;
    $pdf->setFillColor($back, 0, 0, 0);
    for ($i = 0; $i < 40; $i++) {
        $sw = ($i % 3 == 0) ? 2.4 : 1.2;
        $pdf->rect($back, $stripeX, $barY, $sw, 24);
        $stripeX += $sw + 1.4;
        if ($stripeX > $stripeW + 14) break;
    }

    $pdf->text($back, 14, $h - 8, "This card is property of the college. If found, please return.", 6, false, 90, 90, 90);

    file_put_contents($outputPath, $pdf->output());
    return file_exists($outputPath);
}

function wordwrapShort($text, $max)
{
    if (strlen($text) <= $max) return $text;
    return substr($text, 0, $max - 1) . '.';
}
