<?php

namespace customiesdevs\customies\item\component;

final class Component implements ItemComponent
{
    private string $string;

    public function __construct(string $string) {
        $this->string = $string;
    }

    public function getName(): string {
        return $this->string;
    }

    public function isProperty(): bool {
        return true;
    }

    public function getValue(): mixed {
        return null;
    }
}
