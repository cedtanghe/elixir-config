<?php

namespace Elixir\Config\Writer;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
trait WriterrFactoryAwareTrait
{
    /**
     * @var WriterFactory
     */
    protected $writerFactory;

    /**
     * @param WriterFactory $value
     */
    public function setWriterFactory(WriterFactory $value)
    {
        $this->writerFactory = $value;
    }

    /**
     * @return WriterFactory
     */
    public function getWriterFactory()
    {
        return $this->writerFactory;
    }
}
