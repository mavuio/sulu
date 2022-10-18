<?php

namespace Mavu\GlobalBundle\Core;

use Monolog\Level;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use Tarsana\Functional as F;
use Mavu\GlobalBundle\Entity\Dekor;
use Symfony\Component\Process\Process;
use Doctrine\Persistence\ManagerRegistry;

use Mavu\GlobalBundle\Core\TwClassesCore;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Filesystem\Filesystem;
use Mavu\GlobalBundle\Content\Select\BlockTypeSelect;
use Mavu\GlobalBundle\Exception\CustomizableException;

class DekorCore
{

    private $dekorRepository;


    private $doctrine;

    /**
     * @var Filesystem
     */
    private $filesystem;


    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * @var CacheInterface
     */
    private $cache;



    /**
     * @var TwClassesCore
     */
    private $classesCoreService;

    /**
     * @var BlockTypeSelect
     */
    private $blockTypeSelect;

    private  $projectDir;
    private  $bundleConfig;


    public function __construct(
        ManagerRegistry $doctrine,
        CacheInterface $cache,
        LoggerInterface $mavuDebugLogger,
        BlockTypeSelect $blockTypeSelect,
        TwClassesCore $classesCoreService,
        Filesystem $filesystem,
        string $projectDir,
        array $bundleConfig,
    ) {

        $this->logger = $mavuDebugLogger ?: new NullLogger();
        $this->cache = $cache;

        $this->doctrine = $doctrine;
        $this->filesystem = $filesystem;
        $this->blockTypeSelect = $blockTypeSelect;
        $this->classesCoreService = $classesCoreService;
        $this->dekorRepository = $doctrine->getRepository(Dekor::class);
        $this->projectDir = $projectDir;
        $this->bundleConfig = $bundleConfig;
    }


    function checkDefaultStyles()
    {
        $contentTypes = $this->blockTypeSelect->getValues("en");

        $updatedStyleCount = 0;
        foreach ($contentTypes as $key => $ctype) {
            $updatedStyleCount += $this->checkDefaultStyleForContentType($ctype);
        }



        return $updatedStyleCount;
    }

    function checkDefaultStyleForContentType($ctype)
    {
        $slug = $this->getSlugForContentTypeName($ctype['name']);

        $dekor = $this->dekorRepository->findOneBy(['slug' => $slug]);
        if ($dekor == null) {

            $this->createDefaultStyleForContentType($ctype);
            $this->logger->log(Level::Info, "style {$slug} not found, creating it now", ["cname" => $ctype['name']]);
            return 1;
        }

        return 0;
    }

    function createDefaultStyleForContentType($ctype)
    {
        $entityManager = $this->doctrine->getManager();

        $cname = $ctype['name'];
        $dekor = new Dekor();
        $dekor->setName("Default Style for '{$cname}'");
        $dekor->setSlug(self::getSlugForContentTypeName($cname));
        $dekor->setBlockType($cname);
        $dekor->setClasses($ctype['defaultClasses']);
        $dekor->setIgnoreDefaults(false);
        $entityManager->persist($dekor);
        $entityManager->flush();
    }


    public function getCssStringForDekor($dekor)
    {

        $useRawCss = $dekor->getUseRawCss();

        if ($useRawCss) {
            $rawCss = $dekor->getRawCss();

            $css = $this->classesCoreService->removeComments($rawCss);
        } else {
            $classString = $dekor->getClasses() ?? '';

            $classString = $this->classesCoreService->removeComments($classString);
            $classString = $this->classesCoreService->customizeBreakpointPrefixes($classString, $this->classesCoreService->getDefaultBreakpoints());

            $classes = $this->getClassesFromString($classString);

            $css = F\Stream::of($classes)
                ->map(function ($class) {
                    return $this->splitClassIntoPrefix($class);
                })
                ->groupBy(function ($class_w_prefix) {
                    [$prefix, $_] = $class_w_prefix;

                    return $prefix;
                })
                ->toPairs()
                ->map(function ($class_w_prefix) {
                    [$prefix, $vals] = $class_w_prefix;

                    $twClassStr = F\Stream::of($vals)
                        ->map(function ($val) {
                            return $val[1];
                        })
                        ->join(' ')
                        ->result();

                    $twClassStr = trim($twClassStr);
                    if ($twClassStr) {
                        if ($prefix) {
                            return ".$prefix { @apply $twClassStr; }";
                        } else {
                            return "@apply $twClassStr;";
                        }
                    }

                    return '';
                })
                ->join("\n")
                ->result();
        }

        if (trim($css)) {
            $selectorParts = [];
            if ($dekor->isDefaultStyle() == false) {
                $selectorParts[] = ".preset_{$dekor->getId()}";
            }
            $slug = $dekor->getSlug();
            if ($slug) {
                $selectorParts[] = ".preset_{$slug}";
            }
            $selectorString = implode(', ', $selectorParts);
            return "
            /*{$dekor->getName()}: */ 
            {$selectorString} {
                {$css}
            }
            ";
        } else {
            return "";
        }
    }


    function updateStylesheet()
    {

        $dekors = $this->dekorRepository->findAll();

        $strings = array_map(function ($dekor) {
            return $this->getCssStringForDekor($dekor);
        }, $dekors);


        $content = implode("\n", $strings);
        // echo $content;

        $this->writeDekorStylesheet($content);
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
            $this->classesCoreService->rebuildManifest();
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
        $this->classesCoreService->removeOtherCssFiles("presets", $targetFileName);
        return ["ok"];
    }



    public function generateFriendlyErrorMessage(string $output)
    {
        $cleanedOutput = str_replace($this->projectDir, '', $output);
        return "presets.css was not generated: ➜ {$cleanedOutput}";
    }


    public function splitClassIntoPrefix($str)
    {
        if (preg_match("/^(c.)\:(.*)$/", $str, $m)) {
            return [$m[1], $m[2]];
        } else {
            return ["", $str];
        }
    }




    public function getClassesFromString(string $str)
    {
        return preg_split("/[\s]+/", $this->classesCoreService->removeComments($str));
    }

    static function getSlugForContentTypeName(string $cname)
    {
        $cname = str_replace('_', '-', $cname);
        return "{$cname}-default";
    }
}
