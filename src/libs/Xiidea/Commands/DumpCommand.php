<?php

namespace Xiidea\Commands;

use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Extension\Twig\TwigFormulaLoader;
use Assetic\Extension\Twig\TwigResource;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\LazyAssetManager;
use Assetic\Util\VarUtils;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xiidea\Helper\Filesystem;
use Xiidea\Twig\Loader;

class DumpCommand extends ConfigAwareCommand
{
    private $basePath;
    private $verbose;
    /**
     * @var LazyAssetManager
     */
    private $am;
    private $_twig;

    protected function configure()
    {
        $this
            ->setName('assetic:dump')
            ->setDescription('Dumps all assets to the filesystem')
            ->addArgument('write_to', InputArgument::OPTIONAL, 'Override the configured asset root')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->basePath = $input->getArgument('write_to') ?: $this->getConfig()->getWebBasePath();
        $this->verbose = $input->getOption('verbose');

        $assetFactory = new AssetFactory($this->getConfig()->getAssetsBasePath(), $this->getConfig()->isDebug());

        $loader = new TwigFormulaLoader($this->twig());
        $this->am = new LazyAssetManager($assetFactory, array('twig'=>$loader));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln(sprintf('Dumping all <comment>%s</comment> assets.', $input->getOption('env')));
        $output->writeln(sprintf('Debug mode is <comment>%s</comment>.', $this->am->isDebug() ? 'on' : 'off'));
        $output->writeln('');

        $templates = Filesystem::scanForFiles($this->getConfig()->getTwigBasePath(), '', 'twig');

        $loader   = $this->twig()->getLoader();


        foreach ($templates as $template) {
            $resource = new TwigResource($loader, $template);
            $this->am->addResource($resource, 'twig');
        }

        foreach ($this->am->getNames() as $name) {
            $this->dumpAsset($name, $output);
        }
    }


    /**
     * Writes an asset.
     *
     * If the application or asset is in debug mode, each leaf asset will be
     * dumped as well.
     *
     * @param string          $name   An asset name
     * @param OutputInterface $output The command output
     */
    private function dumpAsset($name, OutputInterface $output)
    {
        $asset = $this->am->get($name);
        $formula = $this->am->getFormula($name);

        // start by dumping the main asset
        $this->doDump($asset, $output);

        // dump each leaf if debug
        if (isset($formula[2]['debug']) ? $formula[2]['debug'] : $this->am->isDebug()) {
            foreach ($asset as $leaf) {
                $this->doDump($leaf, $output);
            }
        }
    }

    /**
     * Performs the asset dump.
     *
     * @param AssetInterface  $asset  An asset
     * @param OutputInterface $output The command output
     *
     * @throws RuntimeException If there is a problem writing the asset
     */
    private function doDump(AssetInterface $asset, OutputInterface $output)
    {
        foreach ($this->getAssetVarCombinations($asset) as $combination) {
            $asset->setValues($combination);

            // resolve the target path
            $target = rtrim($this->basePath, '/').'/'.$asset->getTargetPath();
            $target = str_replace('_controller/', '', $target);
            $target = VarUtils::resolve($target, $asset->getVars(), $asset->getValues());

            if (!is_dir($dir = dirname($target))) {
                $output->writeln(sprintf(
                    '<comment>%s</comment> <info>[dir+]</info> %s',
                    date('H:i:s'),
                    $dir
                ));

                if (false === @mkdir($dir, 0777, true)) {
                    throw new \RuntimeException('Unable to create directory '.$dir);
                }
            }

            $output->writeln(sprintf(
                '<comment>%s</comment> <info>[file+]</info> %s',
                date('H:i:s'),
                $target
            ));

            if ($this->verbose) {
                if ($asset instanceof AssetCollectionInterface) {
                    foreach ($asset as $leaf) {
                        $root = $leaf->getSourceRoot();
                        $path = $leaf->getSourcePath();
                        $output->writeln(sprintf('        <comment>%s/%s</comment>', $root ?: '[unknown root]', $path ?: '[unknown path]'));
                    }
                } else {
                    $root = $asset->getSourceRoot();
                    $path = $asset->getSourcePath();
                    $output->writeln(sprintf('        <comment>%s/%s</comment>', $root ?: '[unknown root]', $path ?: '[unknown path]'));
                }
            }

            if (false === @file_put_contents($target, $asset->dump())) {
                throw new \RuntimeException('Unable to write file '.$target);
            }
        }
    }

    private function getAssetVarCombinations(AssetInterface $asset)
    {
        return VarUtils::getCombinations(
            $asset->getVars(),
            array()
        );
    }

    /**
     * @return \Twig_Environment
     */
    public function twig()
    {
        if ($this->_twig == null) {
            $this->_twigInit();
        }

        return $this->_twig;
    }

    private function _twigInit()
    {
        if (class_exists('\Twig_Loader_Filesystem')) {
            $loader = new \Twig_Loader_Filesystem($this->getConfig()->getTwigBasePath());
            $this->_twig = new Loader($loader, array(
                'debug' => $this->getConfig()->getEnvironment() != 'production',
                'cache' => $this->getConfig()->getApplicationBasePath() . 'cache'
            ), new ProxyController, $this->getConfig()->getAppConfigValue('twig_extensions'));
        } else {
            show_error('Twig is not installed. Install Twig first by run the command "composer update twig/twig"');
        }
    }
}
