<?php

namespace Storm\Drivers\Fluent\Object\Properties\Accessors;

use Storm\Drivers\Base\Object\Properties\Accessors;

class Builder implements \ArrayAccess {
    /**
     * @var Accessors\Accessor 
     */
    private $Accessor = null;
    
    public function __construct() { }
    
    final public function GetAccessor() {
        return $this->Accessor;
    }
    
    /**
     * @return Accessors\Accessor
     */
    public static function DefineAs(callable $Definition) {
        $Builder = new static();
        $Definition($Builder);
        
        return $Builder->Accessor;
    }
    
    private function AddAccessor(Accessors\Accessor $NewAccessor) {
        if($this->Accessor === null) {
            $this->Accessor = $NewAccessor;
        }
        else if (!($this->Accessor instanceof Accessors\Traversing)) {
            $this->Accessor = new Accessors\Traversing([$this->Accessor, $NewAccessor]);
        }
        else {
            $this->Accessor = new Accessors\Traversing(
                    array_merge($this->Accessor->GetNestedAccessors(), [$NewAccessor]));
        }
    }
    
    public function __get($FieldName) {
        $this->AddAccessor(new Accessors\Field($FieldName));
        return $this;
    }

    public function __call($name, $arguments) {
        $this->AddAccessor(new Accessors\MethodPair($name, $name));
        return $this;
    }
    

    public function __invoke() {
        $this->AddAccessor(new Accessors\Invocation(func_get_args()));
        return $this;
    }
    
    public function offsetGet($Index) { 
        $this->AddAccessor(new Accessors\Indexer($Index));
        return $this;
    }

    public function offsetExists($offset) { }
    public function offsetSet($offset, $value) { }
    public function offsetUnset($offset) {}
}

?>