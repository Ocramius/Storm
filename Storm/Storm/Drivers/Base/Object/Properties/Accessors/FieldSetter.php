<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class FieldSetter extends FieldBase implements IPropertySetter {

    public function SetValueTo($Entity, $Value) {
        $this->Reflection->setValue($Entity, $Value);
    }
}

?>