<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

class FieldGetter extends FieldBase implements IPropertyGetter {

    public function GetValueFrom($Entity) {
        return $this->Reflection->getValue($Entity);
    }
}

?>
