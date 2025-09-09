<?php
namespace Package\Raxon\Markdown\Module;

use Raxon\App;
use Raxon\Exception\FileNotExistException;
use Raxon\Exception\FileWriteException;
use Raxon\Exception\ObjectException;
use Raxon\Module\Core;
use Raxon\Module\Dir;
use Raxon\Module\Data;
use Raxon\Module\File;
use Raxon\Parse\Module\Parse;

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

    public static function read(App $object, Parse $parse, Data $data, string $url=null, bool $code=false, array $config=[]): string
    {        
        if(!File::exist($url)){
            throw new FileNotExistException('File not found: ' . $url);
        }
        $mtime = File::mtime($url);        
        $require_disabled = $object->config('require.disabled');
        if($require_disabled){
            //nothing
        } else {
            $require_url = $object->config('require.url');
            $require_mtime = $object->config('require.mtime');
            if(empty($require_url)){
                $require_url = [];
                $require_mtime = [];
            }
            if(
                !in_array(
                    $url,
                    $require_url,
                    true
                )
            ){
                $require_url[] = $url;
                $require_mtime[] = $mtime;
                $object->config('require.url', $require_url);
                $object->config('require.mtime', $require_mtime);
            }
        }
        $markdown_dir = $object->config('markdown.dir');
        if($markdown_dir === null){
            $markdown_dir = Dir::name($url);
            if(substr($markdown_dir, 0, strlen($object->config('controller.dir.data'))) === $object->config('controller.dir.data')){
                $object->config('markdown.dir', $markdown_dir);
            }
        }    
        $read = File::read($url);
        $read = Markdown::parse($object, $read, $config);        
        if($code){
            $read = $parse->compile($read, $data)
        }
        return $read;

    }

}