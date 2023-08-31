<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF\TCPDF;

use BeastBytes\PDF\Document as BaseDocument;
use BeastBytes\PDF\DocumentInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use TCPDF;
use Yiisoft\Http\ContentDispositionHeader;
use Yiisoft\ResponseDownload\DownloadResponseFactory;

use const DIRECTORY_SEPARATOR;

final class Document extends BaseDocument
{
    // Output destinations
    public const DESTINATION_DOWNLOAD = 'D';
    public const DESTINATION_FILE = 'F';
    public const DESTINATION_INLINE = 'I';
    public const DESTINATION_STRING = 'S';
    // --end--
    public const DIRECTORY_NOT_CREATED_EXCEPTION = 'Directory `%s` was not created';
    public const INVALID_OUTPUT_DESTINATION_EXCEPTION = 'Invalid output destination';
    public const NAME_NOT_SET_EXCEPTION = 'Filename not set';
    // Page orientation
    public const ORIENTATION_LANDSCAPE = 'L';
    public const ORIENTATION_PORTRAIT = 'P';
    // --end--
    // Page sizes
    public const PAGE_SIZE_A3 = 'A3';
    public const PAGE_SIZE_A4 = 'A4';
    public const PAGE_SIZE_A5 = 'A5';
    public const PAGE_SIZE_LEGAL = 'Legal';
    public const PAGE_SIZE_LETTER = 'Letter';
    // --end--
    public const UNICODE = true;
    // Page measurement units
    public const UNITS_POINTS = 'pt';
    public const UNITS_MILLIMETERS = 'mm';
    public const UNITS_CENTIMETERS = 'cm';
    public const UNITS_INCHES = 'in';
    // --end--
    public const UTF8 = 'UTF8';

    private string $author = '';
    private string $creator = '';
    private string $keywords = '';
    private string $subject = '';
    private string $title = '';
    private string $name = '';
    private string $path = '';
    private bool $utf8 = false;

    public function __construct(
        string $class = TCPDF::class,
        string $orientation = self::ORIENTATION_PORTRAIT,
        string $unit = self::UNITS_MILLIMETERS,
        string $format = self::PAGE_SIZE_A4,
        bool $unicode = self::UNICODE,
        string $encoding = self::UTF8,
        bool $diskcache = false,
        bool $pdfa = false
    )
    {
        $this->pdf = new ($class)($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        $this->creator = $class;
        $this
            ->pdf
            ->setCreator($class)
        ;
    }

    public function __toString(): string
    {
        return $this
            ->pdf
            ->Output( '', self::DESTINATION_STRING)
        ;
    }

    /**
     * @return string Name of the entity (person, organisation, ...) that created the document
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @return string Name of the package used to create the document
     */
    public function getCreator(): string
    {
        return $this->creator;
    }

    /**
     * @return string Keywords for the document
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    /**
     * @return string Name of the document when displayed in the browser, downloaded, or saved.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string Path of directory where document is saved.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string Subject of the document
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string Document title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    public function output(
        string $destination,
        DownloadResponseFactory $downloadResponseFactory
    ): bool|string|ResponseInterface
    {
        $return = false;

        if (str_contains($destination, self::DESTINATION_FILE)) {
            if ($this->getName() === '') {
                throw new RuntimeException(self::NAME_NOT_SET_EXCEPTION);
            }

            $destination = str_replace(self::DESTINATION_FILE, '', $destination);
            $path = $this->getPath();

            if (
                !is_dir($path)
                && !mkdir($path, 0766, true)
                && !is_dir($path)
            ) {
                throw new RuntimeException(sprintf(self::DIRECTORY_NOT_CREATED_EXCEPTION, $path));
            }

            $return = file_put_contents(
                $path . DIRECTORY_SEPARATOR . $this->getName(),
                (string)$this
            ) !== false;
        }

        if ((bool)$destination) {
            switch ($destination) {
                case self::DESTINATION_DOWNLOAD:
                    if ($this->getName() === '') {
                        throw new RuntimeException(self::NAME_NOT_SET_EXCEPTION);
                    }

                    return $downloadResponseFactory->sendContentAsFile(
                        (string)$this,
                        $this->getName(),
                        ContentDispositionHeader::ATTACHMENT,
                        self::MIME_TYPE
                    );
                    break;
                case self::DESTINATION_INLINE:
                    if ($this->getName() === '') {
                        throw new RuntimeException(self::NAME_NOT_SET_EXCEPTION);
                    }

                    return $downloadResponseFactory->sendContentAsFile(
                        (string)$this,
                        $this->getName(),
                        ContentDispositionHeader::INLINE,
                        self::MIME_TYPE
                    );
                    break;
                case self::DESTINATION_STRING:
                    return (string)$this;
                    break;
                default:
                    throw new InvalidArgumentException(self::INVALID_OUTPUT_DESTINATION_EXCEPTION);
            }
        }

        return $return;
    }

    public function withAuthor(string $author): DocumentInterface
    {
        $new = clone $this;
        $new->author = $author;
        $new
            ->pdf
            ->setAuthor($author)
        ;
        return $new;
    }

    public function withCreator(string $creator): DocumentInterface
    {
        $new = clone $this;
        $new->creator = $creator;
        $new
            ->pdf
            ->setCreator($creator)
        ;
        return $new;
    }

    public function withKeywords(string ...$keywords): DocumentInterface
    {
        $new = clone $this;
        $new->keywords = implode(', ', $keywords);
        $new
            ->pdf
            ->setKeywords(implode(', ', $keywords))
        ;
        return $new;
    }

    public function withName(string $name): DocumentInterface
    {
        $new = clone $this;
        $new->name = $name;
        return $new;
    }

    public function withPath(string $path): DocumentInterface
    {
        $new = clone $this;
        $new->path = $path;
        return $new;
    }

    public function withSubject(string $subject): DocumentInterface
    {
        $new = clone $this;
        $new->subject = $subject;
        $new
            ->pdf
            ->setSubject($subject)
        ;
        return $new;
    }

    public function withTitle(string $title): DocumentInterface
    {
        $new = clone $this;
        $new->title = $title;
        $new
            ->pdf
            ->setTitle($title)
        ;
        return $new;
    }
}
