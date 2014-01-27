<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

use \Storm\Core\Relational;

abstract class KeyGenerator implements IKeyGenerator {
    /**
     * @var Relational\Table 
     */
    private $Table;
    
    /**
     * @var Relational\IColumn[] 
     */
    private $PrimaryKeyColumns;
    public function SetTable(Relational\Table $Table) {
        if($this->Table === $Table) {
            return;
        }
        $PrimaryKeyColumns = $Table->GetPrimaryKeyColumns();
        if(count($PrimaryKeyColumns) === 0) {
            throw new \Exception;//TODO:error message
        }
        $this->OnSetPrimaryKeyColumns($PrimaryKeyColumns);
        $this->PrimaryKeyColumns = $PrimaryKeyColumns;
    }
    protected function OnSetPrimaryKeyColumns(array $PrimaryKeyColumns) { }
    
    /**
     * @return Relational\Table
     */
    final public function GetTable() {
        return $this->Table;
    }
    
    /**
     * @return Relational\IColumn[]
     */
    final public function GetPrimaryKeyColumns() {
        return $this->PrimaryKeyColumns;
    }
}

?>
