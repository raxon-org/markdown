<?php
namespace Package\Raxon\Markdown\Trait;

use Raxon\Module\Core;

use Exception;
trait Main {

    /**
     * @throws Exception
     */
    public function markdown_install(object $flags, object $options): void
    {
        Core::interactive();
        $object = $this->object();
        echo 'Install ' . $object->request('package') . '...' . PHP_EOL;
    }
}

