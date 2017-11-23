<?php

namespace Main\Form\Converter;

class ToId extends BaseConverter
{
    public function doConvert()
    {
        $id = $this->getValue();
        if (is_numeric($id)) {
            $id = (int) $id;
            return $id > 0 ? $id : null;
        }
        return null;
    }
}
