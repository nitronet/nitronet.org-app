<?php
namespace Nitronet\runtime;

use Nitronet\models\Page;

class BuilderService
{
    const PAGES_DIRECTORY       = "pages";
    const ARTICLES_DIRECTORY    = "articles";

    const SPACE_SEPARATOR       = "+";
    const EXCLUDE_PREFIX        = "_";

    const PAGE_FILE_REGEX       = "/^([a-zA-Z0-9\-\s]+)_(.[^\.]+)\.(md|markdown)$/i";

    protected $pages;

    protected $articles;

    /**
     * @var GitService
     */
    protected $gitService;

    /**
     *
     * @param string $repositoryDir
     *
     * @return void
     */
    public function __construct(GitService $gitService)
    {
        $this->gitService   = $gitService;
    }

    /**
     *
     */
    public function getPages()
    {
        if (!isset($this->pages)) {
            $dir = $this->gitService->getRepositoryDir() .
                    DIRECTORY_SEPARATOR .
                    self::PAGES_DIRECTORY;

            if (!is_dir($dir)) {
                $this->pages = array();
                return;
            }

            $this->pages = $this->getPagesFromDirectory($dir);
        }

        return $this->pages;
    }


    protected function getPagesFromDirectory($directory, $slugPrefix = null)
    {
        $it     = new \DirectoryIterator($directory);
        $final  = array();

        foreach ($it as $item) {
            $fileName = $item->getFilename();
            $fullPath = $item->getPathname();
            $relativePath = self::PAGES_DIRECTORY . DIRECTORY_SEPARATOR . $slugPrefix . $fileName;

            if ($item->isDir() && strpos($fileName, ".") !== 0) {
                $final += $this->getPagesFromDirectory($fullPath, $fileName . DIRECTORY_SEPARATOR);
                continue;
            }

            // exclude prefix
            elseif ($item->isDir()
                    || strpos($fileName, self::EXCLUDE_PREFIX) === 0) {
                continue;
            }

            // skipping non-markdown files
            elseif (!preg_match_all(self::PAGE_FILE_REGEX, $fileName, $matches)) {
                continue;
            }

            $slug   = str_replace(self::SPACE_SEPARATOR, "-", $matches[1][0]);
            $title  = str_replace(self::SPACE_SEPARATOR, " ", $matches[2][0]);

            $page = new Page();
            $page->setSlug($slugPrefix . $slug);
            $page->setTitle($title);
            $page->setContents(file_get_contents($fullPath));

            $commits = $this->gitService->getFileCommits($relativePath);
            $first = array_pop($commits);
            $page->setCreated_at($first->dateCreated);
            $page->setAuthor($first->author);

            if(count($commits)) {
                $last   = array_shift($commits);
                $page->setUpdated_at($last->dateCreated);
            }

            $final[$slugPrefix . $slug] = $page;
        }

        return $final;
    }
}