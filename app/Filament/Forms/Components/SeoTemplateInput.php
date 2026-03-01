<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Concerns\HasPlaceholder;

class SeoTemplateInput extends Field
{
    use HasPlaceholder;

    protected string $view = 'filament.forms.components.seo-template-input';

    protected array | \Closure $variables = [];
    protected bool | \Closure $isMultiline = false;

    public function variables(array | \Closure $variables): static
    {
        $this->variables = $variables;

        return $this;
    }

    public function getVariables(): array
    {
        return (array) $this->evaluate($this->variables);
    }

    public function multiline(bool | \Closure $condition = true): static
    {
        $this->isMultiline = $condition;

        return $this;
    }

    public function isMultiline(): bool
    {
        return (bool) $this->evaluate($this->isMultiline);
    }
}
