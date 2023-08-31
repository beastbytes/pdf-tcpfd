<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PDF\TCPDF\Tests;

use BeastBytes\PDF\TCPDF\Document;
use BeastBytes\PDF\TCPDF\Tests\Support\TestCase;
use TCPDF;

class DocumentTest extends TestCase
{
    private Document $document;

    protected function setUp(): void
    {
        parent::setUp();
        $this->document = new Document();
    }

    public function testAuthor(): void
    {
        $author = 'Test author';
        $document = $this->document->withAuthor($author);

        $this->assertNotSame($document, $this->document);
        $this->assertSame($author, $document->getAuthor());
    }

    public function testDefaultCreator(): void
    {
        $this->assertSame(TCPDF::class, $this->document->getCreator());
    }

    public function testCreator(): void
    {
        $creator = 'Test creator';
        $document = $this->document->withCreator($creator);

        $this->assertNotSame($document, $this->document);
        $this->assertSame($creator, $document->getCreator());
    }

    public function testKeywords(): void
    {
        $keywords = 'test keywords';
        $document = $this->document->withKeywords($keywords);

        $this->assertNotSame($document, $this->document);
        $this->assertSame($keywords, $document->getKeywords());
    }

    public function testName(): void
    {
        $name = 'Test name';
        $document = $this->document->withName($name);

        $this->assertNotSame($document, $this->document);
        $this->assertSame($name, $document->getName());
    }

    public function testSubject(): void
    {
        $subject = 'Test subject';
        $document = $this->document->withSubject($subject);

        $this->assertNotSame($document, $this->document);
        $this->assertSame($subject, $document->getSubject());
    }

    public function testTitle(): void
    {
        $title = 'Test title';
        $document = $this->document->withTitle($title);

        $this->assertNotSame($document, $this->document);
        $this->assertSame($title, $document->getTitle());
    }
}
