<?php
namespace Plugin;

use League\CommonMark\Exception\CommonMarkException;
use Package\Raxon\Markdown\Module\Markdown;
use Raxon\Exception\FileNotExistException;
use Raxon\Exception\FileWriteException;
use Raxon\Exception\ObjectException;
use Raxon\Module\Dir;
use Raxon\Module\File;

trait Markdown_Read {

    /**
     * @throws FileWriteException
     * @throws CommonMarkException
     * @throws ObjectException
     * @throws FileNotExistException
     */
    public function markdown_read($url=null, $code=false): string
    {
        $object = $this->object();
        $parse = $this->parse();
        $data = $this->data();
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
        return Markdown::read($object, $parse, $data, $url, $code);
    }
}