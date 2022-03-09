<?php

namespace Code16\Sharp\Utils\Layout;

abstract class LayoutField
{
    protected string $fieldKey;
    protected int $size = 12;
    protected int $sizeXS = 12;
    protected ?LayoutColumn $itemLayout = null;

    public function __construct(string $fieldKey, \Closure $subLayoutCallback = null)
    {
        if (strpos($fieldKey, '|')) {
            [$this->fieldKey, $sizes] = explode('|', $fieldKey);

            if (strpos($fieldKey, ',')) {
                [$this->size, $this->sizeXS] = collect(explode(',', $sizes))->map(function ($size) {
                    return (int) $size;
                });
            } else {
                $this->size = (int) $sizes;
            }
        } else {
            $this->fieldKey = $fieldKey;
        }

        if ($subLayoutCallback) {
            $this->itemLayout = $this->getLayoutColumn();
            $subLayoutCallback($this->itemLayout);
        }
    }

    abstract protected function getLayoutColumn(): LayoutColumn;

    public function toArray(): array
    {
        return array_merge(
            [
                'key' => $this->fieldKey,
                'size' => $this->size,
                'sizeXS' => $this->sizeXS,
            ],
            $this->itemLayout
                ? ['item' => $this->itemLayout->toArray()['fields']]
                : [],
        );
    }
}
