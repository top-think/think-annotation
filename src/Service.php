<?php

namespace think\annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;

class Service extends \think\Service
{
    use InteractsWithRoute, InteractsWithInject;

    /** @var Reader */
    protected $reader;

    /** @var array */
    protected $annotation = [];

    /** @var string */
    protected $think = [
        'annotation' => 'think\annotation\Route\\',
        'handler' => 'think\annotation\handler\\',
    ];

    /** @var string */
    protected $custom = [
        'annotation' => 'app\annotation\\',
        'handler' => 'app\annotation\handler\\'
    ];

    public function register()
    {
        AnnotationReader::addGlobalIgnoredName('think');

        // TODO: this method is deprecated and will be removed in doctrine/annotations 2.0
        AnnotationRegistry::registerLoader('class_exists');

        $this->reader = new CachedReader(new AnnotationReader(), $this->app);

        $this->setCustomAnnotation();

        //注解路由
        $this->registerAnnotationRoute();

        //自动注入
        $this->autoInject();
    }

    protected function setCustomAnnotation(){
        $this->annotation = config('annotation.custom');
    }

    /**
     * 扫描文件夹下的类
     * @param $dir
     * @return \Generator
     */
    protected function findClasses($dir)
    {
        $files = iterator_to_array(new \RecursiveIteratorIterator(
            new \RecursiveCallbackFilterIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS),
                function (\SplFileInfo $current) {
                    return '.' !== substr($current->getBasename(), 0, 1);
                }
            ),
            \RecursiveIteratorIterator::LEAVES_ONLY
        ));

        usort($files, function (\SplFileInfo $a, \SplFileInfo $b) {
            return (string) $a > (string) $b ? 1 : -1;
        });

        foreach ($files as $file) {
            if (!$file->isFile() || '.php' !== substr($file->getFilename(), -4)) {
                continue;
            }
            if ($class = $this->findClass($file)) {
                yield $class;
            }
        }
    }

    protected function findClass($file)
    {
        $class     = false;
        $namespace = false;
        $tokens    = token_get_all(file_get_contents($file));
        if (1 === \count($tokens) && T_INLINE_HTML === $tokens[0][0]) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not contain PHP code. Did you forgot to add the "<?php" start tag at the beginning of the file?', $file));
        }
        for ($i = 0; isset($tokens[$i]); ++$i) {
            $token = $tokens[$i];
            if (!isset($token[1])) {
                continue;
            }
            if (true === $class && T_STRING === $token[0]) {
                return $namespace . '\\' . $token[1];
            }
            if (true === $namespace && T_STRING === $token[0]) {
                $namespace = $token[1];
                while (isset($tokens[++$i][1]) && \in_array($tokens[$i][0], [T_NS_SEPARATOR, T_STRING])) {
                    $namespace .= $tokens[$i][1];
                }
                $token = $tokens[$i];
            }
            if (T_CLASS === $token[0]) {
                // Skip usage of ::class constant and anonymous classes
                $skipClassToken = false;
                for ($j = $i - 1; $j > 0; --$j) {
                    if (!isset($tokens[$j][1])) {
                        break;
                    }
                    if (T_DOUBLE_COLON === $tokens[$j][0] || T_NEW === $tokens[$j][0]) {
                        $skipClassToken = true;
                        break;
                    } elseif (!\in_array($tokens[$j][0], [T_WHITESPACE, T_DOC_COMMENT, T_COMMENT])) {
                        break;
                    }
                }
                if (!$skipClassToken) {
                    $class = true;
                }
            }
            if (T_NAMESPACE === $token[0]) {
                $namespace = true;
            }
        }
        return false;
    }
}
