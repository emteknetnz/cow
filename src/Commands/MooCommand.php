<?php

namespace SilverStripe\Cow\Commands;

class MooCommand extends Command
{
    /**
     * @var string
     */
    protected $name = "cow:moo";

    /**
     * @var string
     */
    protected $description = "Discuss with cow.";

    /**
     * {@inheritdoc}
     */
    protected function fire()
    {
        $this->output->writeln("moo");
    }
}