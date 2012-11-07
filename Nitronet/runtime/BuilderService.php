<?php
namespace Nitronet\runtime;

use Nitronet\models\Publication;

class BuilderService
{
    const PAGES_DIRECTORY       = "pages";
    const ARTICLES_DIRECTORY    = "articles";

    const SPACE_SEPARATOR       = "+";
    const EXCLUDE_PREFIX        = "_";

    const PAGE_FILE_REGEX       = "/^([a-zA-Z0-9\-\s]+)_(.[^\.]+)\.(md|markdown)$/i";
    const ARTICLE_FILE_REGEX    = "/^(.[^\.]+)\.(md|markdown)$/i";

    /**
     * @var array
     */
    protected $pages;

    /**
     * @var array
     */
    protected $articles;

    /**
     * @var GitService
     */
    protected $gitService;

    /**
     * Constructor
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
     * Returns all pages from the repository dir
     *
     * @return array
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

    /**
     *
     * @param string $directory
     * @param string $slugPrefix
     *
     * @return array
     */
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

            $page = new Publication();
            $page->setSlug($slugPrefix . $slug);
            $page->setTitle($title);
            $page->setContents(file_get_contents($fullPath));

            $commits = $this->gitService->getFileCommits($relativePath);
            $first = array_pop($commits);
            $page->setCreated_at($first->dateCreated);
            $page->setAuthor($first->author);
            $page->setRevision($first->commit);
            $page->setDateRelativeCreated($first->dateCreatedRelative);

            if(count($commits)) {
                $last   = array_shift($commits);
                $page->setUpdated_at($last->dateCreated);
                $page->setDateRelativeUpdated($last->dateCreatedRelative);
            }

            $final[$slugPrefix . $slug] = $page;
        }

        return $final;
    }

    /**
     * Returns all pages from the repository dir
     *
     * @return array
     */
    public function getArticles()
    {
        if (!isset($this->articles)) {
            $dir = $this->gitService->getRepositoryDir() .
                    DIRECTORY_SEPARATOR .
                    self::ARTICLES_DIRECTORY;

            if (!is_dir($dir)) {
                $this->articles = array();
                return;
            }

            $this->articles = $this->getArticlesFromDirectory($dir);
        }

        return $this->articles;
    }

    /**
     *
     * @param string $directory
     * @param string $slugPrefix
     *
     * @return array
     */
    protected function getArticlesFromDirectory($directory, $slugPrefix = null)
    {
        $it     = new \DirectoryIterator($directory);
        $final  = array();

        foreach ($it as $item) {
            $fileName = $item->getFilename();
            $fullPath = $item->getPathname();
            $relativePath = self::ARTICLES_DIRECTORY . DIRECTORY_SEPARATOR . $slugPrefix . $fileName;

            if ($item->isDir() && strpos($fileName, ".") !== 0) {
                $final += $this->getArticlesFromDirectory($fullPath, $fileName . DIRECTORY_SEPARATOR);
                continue;
            }

            // exclude prefix
            elseif ($item->isDir()
                    || strpos($fileName, self::EXCLUDE_PREFIX) === 0) {
                continue;
            }

            // skipping non-markdown files
            elseif (!preg_match_all(self::ARTICLE_FILE_REGEX, $fileName, $matches)) {
                continue;
            }


            $title = str_replace(self::SPACE_SEPARATOR, " ", $matches[1][0]);
            $slug  = str_replace(
                array(
                    " ",
                    "é", "è", "ê", "ë",
                    "à", "ä",
                    "ï", "î",
                    "ô", "ö",
                    "ü", "ù",
                    "'", '"', "\\", "/", ":", ".", "!", "?"
                ),
                array(
                    "-",
                    "e", "e", "e", "e",
                    "a", "a",
                    "i", "i",
                    "o", "o",
                    "u", "u",
                    "", "", "", "", "", "", "", "", ""
                ),
                strtolower($title)
            );

            $page = new Publication();
            $page->setSlug($slugPrefix . $slug);
            $page->setTitle($title);
            $page->setContents(file_get_contents($fullPath));

            $commits = $this->gitService->getFileCommits($relativePath);
            $first = array_pop($commits);
            $page->setCreated_at($first->dateCreated);
            $page->setAuthor($first->author);
            $page->setRevision($first->commit);
            $page->setDateRelativeCreated($first->dateCreatedRelative);

            if(count($commits)) {
                $last   = array_shift($commits);
                $page->setUpdated_at($last->dateCreated);
                $page->setDateRelativeUpdated($last->dateCreatedRelative);
            }

            $final[$slugPrefix . $slug] = $page;
        }

        return $final;
    }
}