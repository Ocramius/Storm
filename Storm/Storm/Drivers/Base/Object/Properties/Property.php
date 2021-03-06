<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;
use \Storm\Core\Object\IProperty;
use \Storm\Core\Object\IEntityMap;

abstract class Property implements IProperty {
    private $Identifier;
    /**
     * @var Accessors\Accessor 
     */
    protected $Accessor;
    private $EntityMap;
    
    public function __construct(Accessors\Accessor $Accessor) {
        $this->Identifier = $Accessor->GetIdentifier();
        $this->Accessor = $Accessor;
    }
    
    final public function GetIdentifier() {
        return $this->Identifier;
    }
    
    /**
     * @return Accessors\Accessor
     */
    final public function GetAccessor() {
        return $this->Accessor;
    }
    
    final public function GetEntityMap() {
        return $this->EntityMap;
    }
    
    final public function HasEntityMap() {
        return $this->EntityMap !== null;
    }
    
    final public function SetEntityMap(IEntityMap $EntityMap = null) {
        $this->EntityMap = $EntityMap;
        if($EntityMap !== null) {
            $this->Accessor->SetEntityType($EntityMap->GetEntityType());
            $this->Identifier = $this->Accessor->GetIdentifier();
        }
    }
}

?>
