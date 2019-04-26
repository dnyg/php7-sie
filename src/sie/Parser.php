<?php
namespace sie;

use sie\document\CP437Encoding;
use sie\parser\SieFile;
use sie\parser\LineParser;
use sie\parser\InvalidEntryError;
use Exception;

class Parser
{
    public const BEGINNING_OF_ARRAY = "{";
    public const END_OF_ARRAY = "}";
    public const ENTRY = '/^\\s*#/';

    private $options;

    public function __construct($options = [])
    {
        $this->options = $options;
    }

    public function parseSieFileContents($fileContents)
    {
        $data = CP437Encoding::convertFromCP437ToUTF8($fileContents);
        return $this->parse($data);
    }

    public function parse($data)
    {
        $stack = [];
        $sie_file = new SieFile();
        $current = $sie_file;

        $lines = explode("\n", $data);
        foreach ($lines as $line) {
            $line = trim($line);

            switch (true) {
                case $line === static::BEGINNING_OF_ARRAY:
                    $stack[] = $current;
                    $current = end($current->entries);
                    break;
                case $line === static::END_OF_ARRAY:
                    $current = array_pop($stack);
                    break;
                case preg_match(static::ENTRY, $line):
                    $current->entries[] = $this->parse_line($line);
                    break;
            }

        }
        return $sie_file;
    }

    /**
     * @param $line
     * @throws Exception
     * @return \sie\parser\Entry
     */
    private function parse_line($line)
    {
        try {
            $line_parser = new LineParser($line, $this->lenient());
            $entry = $line_parser->parse();
            return $entry;
        } catch (InvalidEntryError $ex) {
            throw new Exception(
                $ex->getMessage() . ". Pass 'lenient: true' to Parser.new to avoid this exception.",
                null,
                $ex
            );
        }
    }

    private function lenient()
    {
        return array_key_exists('lenient', $this->options) ? $this->options["lenient"] : null;
    }
}
