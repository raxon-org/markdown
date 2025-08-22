<?php
namespace Package\Raxon\Markdown\Module;

use Raxon\App;

use Raxon\Module\Core;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

use League\CommonMark\Exception\CommonMarkException;

class Markdown {

    /**
     * @throws CommonMarkException
     */
    public static function parse(App $object, string $string='', array $config = []): string
    {
        //options: App::options($object)
        //flags: App::flags($object)

        if(!array_key_exists('html_input', $config)){
            $config['html_input'] = 'strip';
        } else {
            unset($config['html_input']); //allow ?
        }
        if(array_key_exists('html', $config)){
            if($config['html'] === true){
                unset($config['html_input']); //allow ?
            } else{
                $config['html_input'] = 'strip';
            }
            unset($config['html']);
        }
        if(!array_key_exists('allow_unsafe_links', $config)){
            $config['allow_unsafe_links'] = false;
        }        
        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new AttributesExtension());
        $converter = new MarkdownConverter($environment);
        $comment_start = Core::uuid();
        $comment_end = Core::uuid();
        $string = str_replace(['<!--', '-->'], [$comment_start, $comment_end], $string);
        $string = $converter->convert($string);
        $string =  str_replace([$comment_start, $comment_end], ['<!--', '-->'], $string);
        return str_replace(['<p><!--', '--></p>'], ['<!--', '-->'], $string);
    }

}