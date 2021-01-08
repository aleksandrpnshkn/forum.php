<?php
declare(strict_types=1);

namespace Src\Core;

use JetBrains\PhpStorm\ArrayShape;

class Pagination
{
    private array $baseUrl;
    private int $currentPage;
    private int $perPage;
    private int $length;
    private int $itemsCount;
    private int $pagesCount;

    public function __construct(string $baseUrl, int $itemsCount, int $currentPage, int $perPage = 10, int $length = 3)
    {
        $baseUrl = parse_url($baseUrl);

        if (! is_array($baseUrl)) {
            throw new \Exception('Cannot parse url');
        }

        $this->baseUrl = $baseUrl;
        $this->itemsCount = $itemsCount;
        $this->currentPage = $currentPage;
        $this->perPage = $perPage;
        $this->pagesCount = (int)ceil($this->itemsCount / $this->perPage);
        $this->length = $length;
    }

    private function getPathForPage(int $page) : string
    {
        $query = $this->baseUrl['query'] ?? '';
        $query = array_filter(explode('&', $query), function (string $queryPart) {
            return $queryPart && ! str_starts_with($queryPart, 'page=');
        });
        $query[] = 'page=' . $page;

        return ($this->baseUrl['path'] ?? '')
            . '?'
            . implode('&', $query);
    }

    public function buildNumericPart() : array
    {
        $res = [];

        $start = $this->currentPage - (int)(($this->length - 1) / 2);

        if ($start < 1 + (int)(($this->length - 1) / 2)) {
            $start = 1;
        }
        elseif ($start > $this->pagesCount + 1 - $this->length) {
            $start = $this->pagesCount + 1 - $this->length;
        }

        $finish = $start + $this->length - 1;

        if ($finish > $this->pagesCount) {
            $finish = $this->pagesCount;
        }

        $res[1] = $this->getPathForPage(1);

        for ($i = $start; $i <= $finish; $i++) {
            $res[$i] = $i === $this->currentPage
                ? null
                : $this->getPathForPage($i);
        }

        if (array_key_last($res) !== $this->pagesCount) {
            $res[$this->pagesCount] = $this->getPathForPage($this->pagesCount);
        }

        return $res;
    }

    #[ArrayShape([
        'prev' => 'null|string',
        'next' => 'null|string',
        'first' => 'null|string',
        'last' => 'null|string',
    ])]
    private function buildKeysPart() : array
    {
        return [
            'prev' => $this->currentPage > 1 ? $this->getPathForPage($this->currentPage - 1) : null,
            'next' => $this->currentPage < $this->pagesCount ? $this->getPathForPage($this->currentPage + 1) : null,
            // First and last should be null if only one page
            'first' => $this->pagesCount > 1 ? $this->getPathForPage(1) : null,
            'last' => $this->pagesCount > 1 ? $this->getPathForPage($this->pagesCount) : null,
        ];
    }

    public function build() : array
    {
        return $this->buildNumericPart() + $this->buildKeysPart();
    }

    public function buildHtml() : string
    {
        $pagination = $this->build();

        $html = '<nav class="pagination">';

        $html .= $pagination['first']
            ? '<a href="' . $pagination['first'] . '">' . htmlspecialchars('«') . '</a>'
            : '';
        $html .= '<a href="' . $pagination['prev'] . '">' . htmlspecialchars('<') . '</a>';

        $prev = null;

        foreach ($pagination as $key => $path) {
            if (is_int($key)) {
                if ($prev && $key - $prev > 1) {
                    $html .= '...';
                }
                else {
                    $html .= '<a href="' . $path . '">' . $key . '</a>';
                }
            }
        }

        $html .= '<a href="' . $pagination['next'] . '">' . htmlspecialchars('>') . '</a>';
        $html .= '<a href="' . $pagination['prev'] . '">' . htmlspecialchars('»') . '</a>';

        return $html;
    }
}
