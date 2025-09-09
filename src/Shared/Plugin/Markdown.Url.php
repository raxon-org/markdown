<?php
namespace Plugin;

use Exception;

trait Markdown_Url {

    /**
     * @throws Exception
     */
    public function markdown_url(array $options): string
    {
        $object = $this->object();
        $page = [];
        $module = 'Snippet';
        if(!array_key_exists('language', $options)){
            $options['language'] = 'en';
        }
        if(
            array_key_exists('module', $options) &&
            !empty($options['module'])
        ){
            $module = ucfirst($options['module']);
//            $page[] = $module;
        }
        if(
            array_key_exists('submodule', $options) &&
            !empty($options['submodule'])
        ){
            $page[] = ucfirst($options['submodule']);
        }
        if(
            array_key_exists('command', $options) &&
            !empty($options['command'])
        ){
            $page[] = ucfirst($options['command']);
        }
        if(
            array_key_exists('subcommand', $options) &&
            !empty($options['subcommand'])
        ){
            $page[] = ucfirst($options['subcommand']);
        }
        if(
            array_key_exists('action', $options) &&
            !empty($options['action'])
        ){
            $page[] = ucfirst($options['action']);
        }
        if(
            array_key_exists('subaction', $options) &&
            !empty($options['subaction'])
        ){
            $page[] = ucfirst($options['subaction']);
        }
        if(array_key_exists(0, $page)){
            $page = $module . $object->config('ds') . implode('.', $page);
            $url = $object->config('controller.dir.data') .
                'Markdown' .
                $object->config('ds') .
                ucfirst($options['language']) .
                $object->config('ds') .
                $page .
                $object->config('extension.md');
        }
        elseif(array_key_exists('page', $options)){
            $url = $object->config('controller.dir.data') .
                'Markdown' .
                $object->config('ds') .
                ucfirst($options['language']) .
                $object->config('ds') .
                $options['page'] .
                $object->config('extension.md');
        } else {
            throw new Exception('Please read the documentation');
        }        
        return $url;

    }
}