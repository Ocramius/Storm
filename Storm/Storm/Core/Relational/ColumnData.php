<?php

namespace Storm\Core\Relational;

/**
 * The base class for representing data stored in columns.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class ColumnData implements \IteratorAggregate, \ArrayAccess {
    /**
     * @var IColumn[] 
     */
    private $Columns = array();
    
    /**
     * @var array
     */
    private $ColumnData;
    
    protected function __construct(array $Columns, array $ColumnData) {
        foreach ($Columns as $Column) {
            $Identifier = $Column->GetIdentifier();
            
            $this->Columns[$Identifier] = $Column;
            
            if(!isset($ColumnData[$Identifier])) {
                $ColumnData[$Identifier] = null;
            }
        }
        $this->ColumnData = $ColumnData;
    }
    
    /**
     * @return IColumn[] 
     */
    final public function GetColumns() {
        return $this->Columns;
    }
    
    /**
     * @return array
     */
    final public function GetColumnData() {
        return $this->ColumnData;
    }
    
    /**
     * Get another column data instance with new data.
     * 
     * @param array $ColumnData
     * @return ColumnData
     */
    final public function Another(array $ColumnData) {
        $ClonedColumnData = clone $this;
        $ClonedColumnData->ColumnData = $ColumnData + array_fill_keys(array_keys($this->Columns), null);
        return $ClonedColumnData;
    }
    
    /**
     * Get the column with the supplied identifier
     * 
     * @param string $Identifier The column identifier
     * @return IColumn|null The matched column or null if it does not exist
     */
    final public function GetColumn($Identifier) {
        return isset($this->Columns[$Identifier]) ? $this->Columns[$Identifier] : null;
    }
    
    /**
     * Sets the column to a supplied value
     * 
     * @param IColumn $Column The column to set the value to
     * @param mixed $Data The value to set
     * @return void
     */
    final public function SetColumn(IColumn $Column, $Data) {
        $this->AddColumn($Column, $Data);
    }
    
    protected function AddColumn(IColumn $Column, $Data) {
        $ColumnIdentifier = $Column->GetIdentifier();
        if(!isset($this->Columns[$ColumnIdentifier])) {
            throw new \InvalidArgumentException('$Column must be one of: ' . 
                    implode(', ', array_keys($this->Columns)));
        }
        
        $this->ColumnData[$ColumnIdentifier] = $Data;
    }
    
    protected function RemoveColumn(IColumn $Column) {
        $this->ColumnData[$Column->GetIdentifier()] = null;
    }
    
    final public function Hash() {
        asort($this->ColumnData);
        return md5(json_encode($this->ColumnData));
    }
    
    final public function HashData() {
        asort($this->ColumnData);
        return md5(json_encode(array_values($this->ColumnData)));
    }
    
    final public function getIterator() {
        return new \ArrayIterator($this->ColumnData);
    }

    final public function offsetExists($Column) {
        return isset($this->Columns[$Column->GetIdentifier()]);
    }
    
    final public function offsetGet($Column) {
        $Identifier = $Column->GetIdentifier();
        if(!isset($this->ColumnData[$Identifier]))
            return null;
        else
            return $this->ColumnData[$Identifier];
    }

    final public function offsetSet($Column, $Data) {
        $this->AddColumn($Column, $Data);
    }

    final public function offsetUnset($Column) {
        $this->RemoveColumn($Column);
    }
    
    /**
     * Whether or not the column data matches
     * 
     * @param ColumnData $Data The other column data
     * @return boolean
     */
    public function Matches(ColumnData $Data) {
        ksort($this->ColumnData);
        ksort($Data->ColumnData);
        return $this->ColumnData === $Data->ColumnData;
    }
}

?>