<?php

namespace Mavu\GlobalBundle\Core;

use Psr\Log\NullLogger;

use PHPCR\SessionInterface;
use Psr\Log\LoggerInterface;
use Tarsana\Functional as F;
use Mavu\GlobalBundle\Entity\Dekor;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Filesystem\Filesystem;
use Sulu\Bundle\PageBundle\Document\BasePageDocument;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;


class TwClassesCore
{

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var SessionInterface
     */
    private $liveSession;

    /**
     * @var LoggerInterface
     */
    private $mavuDebugLogger;

    private string $projectDir;

    private array $bundleConfig;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var CacheInterface
     */
    private $cache;

    // /**
    // * @var ManagerRegistry
    // */
    // private $registry;


    private $dekorRepository;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        ContainerInterface $container,
        CacheInterface $cache,
        Filesystem $filesystem,
        LoggerInterface $mavuDebugLogger,
        ManagerRegistry $registry,
        array $bundleConfig,
        string $projectDir,
        \PHPCR\SessionInterface $session2
    ) {



        $this->filesystem = $filesystem;
        $this->container = $container;
        $this->cache = $cache;
        $this->logger = $mavuDebugLogger ?: new NullLogger();
        // $this->registry = $registry;
        $this->projectDir = $projectDir;
        $this->bundleConfig = $bundleConfig;
        $this->session = $session2;
        $this->liveSession = $session2;
        $this->dekorRepository = $registry->getRepository(Dekor::class);
    }


    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @return string[]
     **/

    public function collectClassesFromDocument(BasePageDocument $document)
    {
        $classes = [];
        if ($document->getStructure()->hasProperty('blocks')) {
            $blocks = $document->getStructure()->getProperty('blocks')->getValue();
            if ($blocks) {
                return $this->collectClassesFromBlocks($blocks);
            }
        }

        return [];
    }

    public function collectClassesFromBlocks(array $blocks)
    {


        $blocks = $this->flatten_blocks($blocks);
        return F\Stream::of($blocks)
            ->map(function ($block) {
                if (array_key_exists('classes', $block)) {
                    return $this->cleanUpClassString($block['classes'], $block);
                } else {
                    return null;
                }
            })
            ->filter(function ($string) {
                return ($string !== null);
            })
            ->chain(F\split(" "))
            ->map(function ($string) {
                //remove c?: container-prefixes
                return preg_replace("/(^|\s)c.\:/mis", " ", trim($string));
            })
            ->unique()
            ->result();
    }


    public function cleanUpClassString($classString, array $block): string
    {
        $classString = $classString ?? "";
        $classString = $this->removeComments($classString);

        $classString = $this->enrichWithStylePresetsForBlock($classString, $block);
        $classString = $this->enrichWithDefaultClassesForBlock($classString, $block);
        $classString = $this->customizeBreakpointPrefixes($classString, $block);
        return $classString;
    }

    public function removeComments($classString)
    {
        return F\Stream::of($classString)
            ->split("\n")
            ->filter(function ($string) {
                if (
                    str_starts_with(trim($string), "//")
                    || str_starts_with(trim($string), "#")

                ) {
                    return false;
                } else {
                    return true;
                }
            })
            ->join(" ")
            ->result();
    }

    public function enrichWithDefaultClassesForBlock($classString, array $settings): string
    {
        return "{$classString} " . $this->getDefaultClassesForBlock($settings["type"]);
    }

    public function enrichWithStylePresetsForBlock($classString, array $settings): string
    {

        if (array_key_exists('style_presets', $settings) && $settings["style_presets"]) {
            return "{$classString} " . $this->getClassesForDekor($settings["style_presets"]);
        }
        return $classString;
    }

    public function getClassesForDekor($style_presets)
    {

        if (is_array($style_presets)) {


            $strings = array_map(function ($dekor) {
                if (is_integer($dekor)) {
                    $dekor = $this->dekorRepository->find($dekor);
                }
                if ($dekor) {
                    return  "preset_{$dekor->getId()}";
                    // return $dekor->getClasses()." preset_{$dekor->getId()}";
                } else {
                    return "";
                }
            }, $style_presets);
            return implode(" ", $strings);
        }
        return "";
    }


    public function getDefaultClassesForBlock(string $type): string
    {
        // copied from html-templates, otherwise tailwind  won't know about them

        switch ($type) {

            case 'expander':
                return "pb-8 pt-4 pl-8";


            case 'twocol':
                return "sm:flex sm:space-x-6 ce-inner xs:space-y-6 sm:w-1/2";

            case 'threecol':
                return "sm:flex sm:space-x-6 ce-inner xs:space-y-6 sm:w-1/3";

            case 'image':
            case 'video':
                return  'inline-block text-xs opacity-50 mt-3';

            default:
                return "";
        }
    }

    /**
     *
     * @param mixed $blocks
     * @return mixed[]
     */
    public function flatten_blocks($blocks)
    {
        if (is_array($blocks)) {
            return F\Stream::of($blocks)
                ->chain(function ($block) {
                    if ($this->isAssoc($block)) {
                        $ret = F\reduce(function ($acc, $pair) {
                            [$key, $val] = $pair;

                            if (is_array($val) and $this->isAssoc($val) == false && $key != "style_presets") {
                                foreach ($this->flatten_blocks($val) as $item) {
                                    $acc[] = $item;
                                }
                                return $acc;
                            } elseif (is_array($val) and $this->isAssoc($val) == true && $key != "style_presets") {
                                foreach ($this->flatten_blocks([$val]) as $item) {
                                    $acc[] = $item;
                                }
                                return $acc;
                            } else {
                                $acc[0][$key] = $val;
                                return $acc;
                            }
                        }, [["init" => "88"]], F\toPairs($block));

                        // $this->logger->info(" ctcyan flatten_blocks", ["ret"=>$ret]);

                        return $ret;
                    } else {
                        return [];
                    }
                })
                ->result();
        } else {
            return [];
        }
    }

    /**
     * is associative array?
     *
     * @param mixed $arr
     * @return boolean
     */
    public function isAssoc($arr)
    {
        if (!is_array($arr)) {
            return false;
        }
        if (array() === $arr) {
            return false;
        }
        return array_keys($arr) !== range(0, count($arr) - 1);
    }


    /**
     * Undocumented function
     *
     * @param string $addonClassString
     * @return string
     */
    public function writeClassFiles(string $addonClassString = "")
    {
        $manifestFilename = $this->projectDir . '/public/fe_assets2/manifest.json';

        $filename = $this->projectDir . '/public/fe_assets2/tw_classes.html';
        $this->logger->info("writeClassFiles $filename in workspace " . $this->liveSession->getWorkspace()->getName());

        $classes = $this->getClassesForSession($this->session);

        $liveClasses = $this->getClassesForSession($this->liveSession);

        $hash = "not set";
        $oldHash = $this->cache->get('md5_tw_classes', function () use ($hash) {
            return $hash;
        });

        $content = implode("\n", [$addonClassString, ...$classes, ...$liveClasses]);
        $hash = md5($content);

        $customCssFilename = dirname($manifestFilename) . "/custom.{$hash}.css";

        if ($hash !== $oldHash) {
            $this->filesystem->dumpFile($filename, $content);
            // write current hash to cache:
            $this->cache->delete('md5_tw_classes');
            $this->cache->get('md5_tw_classes', function () use ($hash) {
                return $hash;
            });
            $this->rebuildCustomTailwindCssFile($filename, $customCssFilename);
            $this->rebuildManifest();
        } else {
            $this->logger->info("hash did not change");
        }

        return $customCssFilename;
    }

    public function writeDekorStylesheet($content)
    {
        $filename = $this->projectDir . '/public/fe_assets2/tw_dekors.css';

        $manifestFilename = $this->projectDir . '/public/fe_assets2/manifest.json';

        $hash = "not set";
        $oldHash = $this->cache->get('md5_tw_dekors', function () use ($hash) {
            return $hash;
        });

        $hash = md5($content);

        $customCssFilename = dirname($manifestFilename) . "/presets.{$hash}.css";

        if ($hash !== $oldHash) {
            $this->filesystem->dumpFile($filename, $content);
            $this->rebuildPresetsCssFile($customCssFilename);
            $this->cache->delete('md5_tw_dekors');
            $this->cache->get('md5_tw_dekors', function () use ($hash) {
                return $hash;
            });
            $this->rebuildManifest();
        } else {
            $this->logger->info("hash did not change");
        }
    }

    public function rebuildPresetsCssFile(string $targetFileName)
    {
        $basedir = $this->projectDir . '/fe_assets2';

        $npmBinary = $this->bundleConfig["tw_classes"]['npm_binary'];

        $cmd = [$npmBinary, "exec", "--", "postcss", "./presets.pcss", "-o", $targetFileName];

        $process = new Process($cmd, $basedir, ['PATH' => '/Users/manfred/.asdf/shims:/opt/homebrew/opt/asdf/libexec/bin:/Users/manfred/bin:/usr/local/bin:/opt/homebrew/bin:/opt/homebrew/sbin:/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin:/opt/plesk/node/17/bin', 'NODE_ENV' => 'production']);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {

            throw new \Exception($this->generateFriendlyErrorMessage($process->getErrorOutput()));
            // throw new ProcessFailedException($process);
        }

        $res = $process->getOutput();
        $this->logger->info(implode(" ", $cmd), ["res" => print_r($res, 1)]);
        $this->removeOtherCssFiles("presets", $targetFileName);
        return ["ok"];
    }

    public function generateFriendlyErrorMessage(string $output)
    {
        $cleanedOutput = str_replace($this->projectDir, '', $output);
        return "presets.css was not generated: ➜ {$cleanedOutput}";
    }


    public function rebuildCustomTailwindCssFile(string $htmlFilename, string $targetFileName)
    {
        $basedir = $this->projectDir . '/fe_assets2';

        $npmBinary = $this->bundleConfig["tw_classes"]['npm_binary'];

        $cmd = [$npmBinary, "exec", "--", "tailwindcss", "-o", $targetFileName, "--minify"];


        $this->logger->info("bundleconf", ["bc" => print_r($this->bundleConfig, 1)]);
        $this->logger->info("rebuildCustomTailwindCssFile: " . implode(" ", $cmd));

        $process = new Process($cmd, $basedir, ['PATH' => '/Users/manfred/.asdf/shims:/opt/homebrew/opt/asdf/libexec/bin:/Users/manfred/bin:/usr/local/bin:/opt/homebrew/bin:/opt/homebrew/sbin:/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin:/opt/plesk/node/17/bin']);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $res = $process->getOutput();
        $this->logger->info("run returns", ["res" => print_r($res, 1)]);
        $this->removeOtherCssFiles('custom', $targetFileName);
        return ["ok"];
    }


    public function removeOtherCssFiles(string $basename,  string $filename)
    {
        $finder = new Finder();

        $finder->files()->name($basename . '*.css')
            // ->date("before - 1 hour")
            ->in(dirname($filename))
            ->notName(basename($filename));
        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                unlink($file->getRealPath());
            }
        }
    }


    function rebuildManifest($subdir = '/public/fe_assets2')
    {

        $manifestDir = $this->projectDir . $subdir;
        $finder = new Finder();

        $finder->files()->in($manifestDir)->name('/^[^.]+\.[^.]+\.(css|js)$/')->sortByModifiedTime();
        if ($finder->hasResults()) {
            foreach ($finder as $file) {

                $filename = $file->getFilename();
                [$name, $hash, $extension] = explode('.', $filename);

                $files["$name.$extension"] = $filename;
            }
        }

        $manifestFilename = $manifestDir . "/manifest.json";
        $content = json_encode($files, JSON_PRETTY_PRINT);
        $this->filesystem->dumpFile($manifestFilename, $content);
        return $content;
    }



    public function getDefaultBreakpoints()
    {

        return [
            "bp_sm" => 640,
            "bp_md" => 768,
            "bp_lg" => 1024,
        ];
    }


    /**
     * Undocumented function
     *
     * @param SessionInterface $session
     * @return string[]
     */
    public function getClassesForSession($session)
    {
        $queryManager = $session->getWorkspace()->getQueryManager();

        $query = 'SELECT i18n:de-tw_classes-classnames,i18n:en-tw_classes-classnames  FROM [nt:unstructured] where [i18n:de-tw_classes-classnames] is not null or [i18n:en-tw_classes-classnames] is not null';
        $rows = $queryManager->createQuery($query, 'JCR-SQL2')->execute();

        $classes = [];
        foreach ($rows as $row) {
            $classes[] = trim(implode(" ", array_values($row->getValues())));
        }
        return $classes;
    }


    public function customizeBreakpointPrefixes(string $classString, array $settings)
    {
        return  array_reduce($this->getCustomBreakPoints($settings), [$this, "customizeBreakpointPrefix"], $classString);
    }

    public function customizeBreakpointPrefix($classString, $item): string
    {
        [$breakpointName, $customBreakpointName] = $item;
        return str_replace($breakpointName . ":", $customBreakpointName . ":", $classString);
    }

    public function  getCustomBreakPoints(array $settings)
    {
        $ret = [];
        foreach (["md", "sm", "lg"] as $key) {
            if (array_key_exists("bp_$key", $settings) && $settings["bp_$key"]) {
                $ret[] = [$key, "b" . $settings["bp_$key"]];

                if ($key == "sm") {
                    $ret[] = ["xs", "bl" . $settings["bp_$key"]];
                }
            }
        }
        return $ret;
    }
}
