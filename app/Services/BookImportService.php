<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\Genre;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookImportService
{
    public function importFromFb2($filePath, $disk = 'public')
    {
        $content = Storage::disk($disk)->get($filePath);
        $xml = simplexml_load_string($content);

        if (!$xml) {
            throw new \Exception('Failed to parse FB2 file.');
        }


        $namespaces = $xml->getNamespaces(true);
        foreach ($namespaces as $prefix => $ns) {
            $xml->registerXPathNamespace($prefix ?: 'fb', $ns);
        }

        $info = $xml->description->{'title-info'};


        $titleNode = $info->{'book-title'} ?? null;
        $title = $titleNode ? trim(dom_import_simplexml($titleNode)->textContent) : '';


        if (empty($title)) {
            $nodes = $xml->xpath('//fb:description/fb:title-info/fb:book-title');
            if (!empty($nodes)) {
                $title = trim(dom_import_simplexml($nodes[0])->textContent);
            }
        }


        if (empty($title)) {
            $nodes = $xml->xpath('//*[local-name()="book-title"]');
            if (!empty($nodes)) {
                $title = trim(dom_import_simplexml($nodes[0])->textContent);
            }
        }



        $title = trim($title) ?: 'Без названия';

        $annotationNode = $info->annotation ?? null;
        if (!$annotationNode) {

            $nodes = $xml->xpath('//*[local-name()="annotation"]');
            if (!empty($nodes))
                $annotationNode = $nodes[0];
        }
        $description = $this->parseAnnotation($annotationNode);


        $authorName = (string) $info->author->{'first-name'} . ' ' . (string) $info->author->{'last-name'};
        $author = Author::firstOrCreate(
            ['name' => trim($authorName)],
            ['slug' => Str::slug($authorName)]
        );


        $genresList = [];
        foreach ($info->genre as $g) {
            $genresList[] = (string) $g;
        }


        $seoTitle = mb_substr($title . ' - ' . $author->name, 0, 255);
        $seoDescription = mb_substr(strip_tags(str_replace(['<p>', '</p>'], ["", " "], $description)), 0, 160);
        $seoKeywords = mb_substr(implode(', ', array_unique(array_merge([$author->name], $genresList))), 0, 255);

        $book = Book::create([
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'description' => $description,
            'status' => 'finished',
            'is_published' => true,
            'seo_title' => $seoTitle,
            'seo_description' => $seoDescription,
            'seo_keywords' => $seoKeywords,
        ]);


        foreach ($genresList as $gName) {
            $genre = Genre::firstOrCreate(
                ['slug' => Str::slug($gName)],
                ['name' => ucfirst($gName)]
            );
            $book->genres()->attach($genre->id);
        }

        $book->authors()->attach($author->id);


        $coverHref = null;
        if (isset($info->coverpage->image)) {
            $attrs = $info->coverpage->image->attributes('http://www.w3.org/1999/xlink');
            $coverHref = ltrim((string) $attrs['href'], '#');
        }

        if ($coverHref) {
            $binary = $xml->xpath("//fb:binary[@id='{$coverHref}']")[0] ?? null;
            if ($binary) {
                $imageData = base64_decode((string) $binary);
                $imageName = 'covers/' . Str::random(40) . '.jpg';
                Storage::disk('public')->put($imageName, $imageData);
                $book->update(['cover_image' => $imageName]);
            }
        }


        $chapterOrder = 1;
        $chaptersData = [];
        foreach ($xml->body->section as $section) {
            $chapterTitle = (string) $section->title->p ?: 'Глава ' . $chapterOrder;
            $chapterContent = $this->parseSection($section);
            $symbolsCount = mb_strlen(strip_tags($chapterContent));

            $chapter = Chapter::create([
                'book_id' => $book->id,
                'title' => $chapterTitle,
                'content' => $chapterContent,
                'order' => $chapterOrder++,
                'symbols_count' => $symbolsCount,
            ]);
            $chaptersData[] = $chapter;
        }


        $fb2FileName = 'books/files/' . $book->slug . '.fb2';
        Storage::disk('public')->put($fb2FileName, $content);
        $book->file_fb2 = $fb2FileName;


        $txtContent = $this->generateTxtContent($book, $chaptersData);
        $txtFileName = 'books/files/' . $book->slug . '.txt';
        Storage::disk('public')->put($txtFileName, $txtContent);
        $book->file_txt = $txtFileName;


        $epubFileName = 'books/files/' . $book->slug . '.epub';

        if ($this->generateEpub($book, $chaptersData, $epubFileName)) {
            $book->file_epub = $epubFileName;
        }

        $book->save();

        return $book;
    }

    protected function parseAnnotation($annotation)
    {
        if (!$annotation)
            return '';
        $text = '';
        foreach ($annotation->children() as $child) {
            $text .= (string) $child . "\n";
        }
        return trim($text);
    }

    protected function parseSection($section)
    {
        $text = '';
        foreach ($section->children() as $name => $child) {
            if ($name === 'title')
                continue;
            if ($name === 'p') {
                $text .= (string) $child . "\n\n";
            } elseif ($name === 'section') {
                $text .= $this->parseSection($child);
            }
        }
        return $text;
    }

    public function generateFb2(Book $book, $chapters, $filename)
    {
        $xml = new \SimpleXMLElement('<FictionBook xmlns="http://www.gribuser.ru/xml/fictionbook/2.0" xmlns:l="http://www.w3.org/1999/xlink"/>');

        $description = $xml->addChild('description');
        $titleInfo = $description->addChild('title-info');


        $genres = $book->genres;
        if ($genres->isEmpty()) {
            $titleInfo->addChild('genre', 'fiction');
        } else {
            foreach ($genres as $genre) {

                $titleInfo->addChild('genre', $genre->slug);
            }
        }


        $authorNode = $titleInfo->addChild('author');

        $authorName = $book->authors->isNotEmpty() ? $book->authors->first()->name : 'Unknown';
        $parts = explode(' ', $authorName, 2);
        $authorNode->addChild('first-name', $parts[0] ?? '');
        $authorNode->addChild('last-name', $parts[1] ?? '');

        $titleInfo->addChild('book-title', $book->title);

        if ($book->description) {
            $annotation = $titleInfo->addChild('annotation');

            foreach (explode("\n", $book->description) as $line) {
                if (trim($line))
                    $annotation->addChild('p', trim($line));
            }
        }
        $titleInfo->addChild('lang', 'ru');


        $body = $xml->addChild('body');
        $body->addChild('title')->addChild('p', $book->title);

        foreach ($chapters as $chapter) {
            $section = $body->addChild('section');
            $section->addChild('title')->addChild('p', $chapter->title);

            foreach (explode("\n", $chapter->content) as $line) {
                if (trim($line))
                    $section->addChild('p', trim($line));
            }
        }

        $content = $xml->asXML();
        Storage::disk('public')->put($filename, $content);
        return true;
    }

    public function generateTxtContent(Book $book, $chapters)
    {
        $content = "{$book->title}\n";
        $authorName = $book->authors->isNotEmpty() ? $book->authors->pluck('name')->join(', ') : 'Unknown';
        $content .= "{$authorName}\n\n";
        $content .= strip_tags(str_replace(['<p>', '</p>'], ["", "\n"], $book->description ?? '')) . "\n\n";
        $content .= str_repeat('=', 20) . "\n\n";

        foreach ($chapters as $chapter) {
            $content .= "{$chapter->title}\n\n";

            $text = strip_tags(str_replace(['<p>', '</p>'], ["", "\n"], $chapter->content));
            $content .= trim($text) . "\n\n";
            $content .= str_repeat('-', 10) . "\n\n";
        }

        return $content;
    }

    public function generateEpub(Book $book, $chapters, $filename)
    {
        $zip = new \ZipArchive();
        $tempFile = tempnam(sys_get_temp_dir(), 'epub');
        if ($zip->open($tempFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return false;
        }


        $zip->addFromString('mimetype', 'application/epub+zip');
        $zip->setCompressionName('mimetype', \ZipArchive::CM_STORE);


        $containerXml = <<<'XML'
<?xml version="1.0"?>
<container version="1.0" xmlns="urn:oasis:names:tc:opendocument:xmlns:container">
    <rootfiles>
        <rootfile full-path="OEBPS/content.opf" media-type="application/oebps-package+xml"/>
    </rootfiles>
</container>
XML;
        $zip->addFromString('META-INF/container.xml', $containerXml);


        $manifest = '';
        $spine = '';
        $navMap = '';




        foreach ($chapters as $index => $chapter) {
            $chapterId = "chapter_{$index}";
            $chapterFilename = "chapter_{$index}.xhtml";

            $chapterContent = <<<HTML
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{$chapter->title}</title>
</head>
<body>
    <h1>{$chapter->title}</h1>
    {$chapter->content}
</body>
</html>
HTML;
            $zip->addFromString("OEBPS/{$chapterFilename}", $chapterContent);

            $manifest .= "<item id=\"{$chapterId}\" href=\"{$chapterFilename}\" media-type=\"application/xhtml+xml\"/>\n";
            $spine .= "<itemref idref=\"{$chapterId}\"/>\n";

            $playOrder = $index + 1;
            $navMap .= <<<XML
        <navPoint id="navPoint-{$playOrder}" playOrder="{$playOrder}">
            <navLabel>
                <text>{$chapter->title}</text>
            </navLabel>
            <content src="{$chapterFilename}"/>
        </navPoint>
XML;
        }


        $authorName = $book->authors->isNotEmpty() ? $book->authors->pluck('name')->join(', ') : 'Unknown';
        $contentOpf = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<package xmlns="http://www.idpf.org/2007/opf" unique-identifier="BookId" version="2.0">
    <metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf">
        <dc:title>{$book->title}</dc:title>
        <dc:creator>{$authorName}</dc:creator>
        <dc:language>ru</dc:language>
        <dc:identifier id="BookId">urn:uuid:{$book->slug}</dc:identifier>
    </metadata>
    <manifest>
        <item id="ncx" href="toc.ncx" media-type="application/x-dtbncx+xml"/>
        {$manifest}
    </manifest>
    <spine toc="ncx">
        {$spine}
    </spine>
</package>
XML;
        $zip->addFromString('OEBPS/content.opf', $contentOpf);


        $tocNcx = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE ncx PUBLIC "-//NISO//DTD ncx 2005-1//EN" "http://www.daisy.org/z3986/2005/ncx-2005-1.dtd">
<ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1">
    <head>
        <meta name="dtb:uid" content="urn:uuid:{$book->slug}"/>
        <meta name="dtb:depth" content="1"/>
        <meta name="dtb:totalPageCount" content="0"/>
        <meta name="dtb:maxPageNumber" content="0"/>
    </head>
    <docTitle>
        <text>{$book->title}</text>
    </docTitle>
    <navMap>
        {$navMap}
    </navMap>
</ncx>
XML;
        $zip->addFromString('OEBPS/toc.ncx', $tocNcx);

        $zip->close();


        Storage::disk('public')->put($filename, file_get_contents($tempFile));
        unlink($tempFile);

        return true;
    }
}
