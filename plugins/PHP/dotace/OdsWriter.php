<?php
namespace Grav\Plugin\Dotace;

use ZipArchive;

class OdsWriter
{
    private const MIME = 'application/vnd.oasis.opendocument.spreadsheet';

    public static function write(string $heading, array $header, array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"'
            . ' xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0"'
            . ' xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" office:version="1.2">'
            . '<office:body><office:spreadsheet><table:table table:name="Seznam">'
            . self::row([$heading]) . self::row($header);
        foreach ($rows as $row) $xml .= self::row($row);
        $xml .= '</table:table></office:spreadsheet></office:body></office:document-content>';

        $manifest = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0" manifest:version="1.2">'
            . '<manifest:file-entry manifest:full-path="/" manifest:version="1.2" manifest:media-type="' . self::MIME . '"/>'
            . '<manifest:file-entry manifest:full-path="content.xml" manifest:media-type="text/xml"/>'
            . '</manifest:manifest>';

        $tmp = tempnam(sys_get_temp_dir(), 'ods');
        try {
            $zip = new ZipArchive();
            if ($zip->open($tmp, ZipArchive::OVERWRITE | ZipArchive::CREATE) !== true) {
                throw new \RuntimeException('Cannot create ODS archive');
            }
            $zip->addFromString('mimetype', self::MIME);
            $zip->setCompressionName('mimetype', ZipArchive::CM_STORE);
            $zip->addFromString('META-INF/manifest.xml', $manifest);
            $zip->addFromString('content.xml', $xml);
            if (!$zip->close()) throw new \RuntimeException('Cannot write ODS archive');
            return file_get_contents($tmp);
        } finally {
            @unlink($tmp);
        }
    }

    private static function row(array $cells): string
    {
        $out = '<table:table-row>';
        foreach ($cells as $c) {
            if ($c === null) {
                $out .= '<table:table-cell/>';
            } elseif (is_int($c)) {
                $out .= '<table:table-cell office:value-type="float" office:value="' . $c . '"><text:p>' . $c . '</text:p></table:table-cell>';
            } else {
                $out .= '<table:table-cell office:value-type="string"><text:p>' . self::esc((string) $c) . '</text:p></table:table-cell>';
            }
        }
        return $out . '</table:table-row>';
    }

    private static function esc(string $s): string
    {
        return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
