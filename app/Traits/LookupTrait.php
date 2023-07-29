<?php

namespace App\Traits;

use App\Models\Lookup;

trait LookupTrait
{
    function toElementsArray($data, $id): false|array
    {
        $elements = null;
        foreach ($data as $element) {
            if ($element['id'] == $id) {
                $elements = $element;
                break;
            }
        }
        if ($elements === null) return false;
        return $elements;
    }

    function toArray($data): false|array
    {
        $elements = [];
        foreach ($data as $element) {
            $elements[] = $element;
        }
        if ($elements === null) return false;
        return $elements;
    }

    /**
     * return priority name
     *
     * @params int $id
     * @params string $type
     * @return false|string
     */

    public function getPriority(int $id): false | string
    {
        $priority = Lookup::where('for', 'ticket_priority')
            ->whereJsonContains('data', ['id' => $id])
            ->first();
        if (!$priority) return false;
        $data = json_decode($priority->data, true);

        $element = $this->toElementsArray($data, $id);
        if (!$element) return false;
        return $element['name'];
    }

    /**
     * return status name
     *
     * @params int $id
     * @params string $type
     * @return false|string
     */

    public function getStatus(int $id): false | string
    {
        $priority = Lookup::where('for', 'ticket_status')
            ->whereJsonContains('data', ['id' => $id])
            ->first();
        if (!$priority) return false;
        $data = json_decode($priority->data, true);

        $element = $this->toElementsArray($data, $id);
        if (!$element) return false;
        return $element['name'];
    }

    /**
     * return elements for
     *
     * @params int $id
     * @params string $type
     * @return false|array
     */

    public function lookupElement(string $type): false | array
    {
        $priority = Lookup::where('for', $type)
            ->first();
        if (!$priority) return false;
        $data = json_decode($priority->data, true);

        $element = $this->toArray($data);
        if (!$element) return false;
        return $element;
    }
}
