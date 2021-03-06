<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Constraints;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

class JoinTableRelation extends ToManyRelationBase {
    /**
     * @var JoinTable 
     */
    private $JoinTable;
    /**
     * @var ForeignKey 
     */
    private $ParentForeignKey;
    
    private $RelatesToTableTwo;
    
    public function __construct(JoinTable $JoinTable, $RelatesToTableTwo = true) {
        parent::__construct(
                $RelatesToTableTwo ? $JoinTable->GetForeignKey2() : $JoinTable->GetForeignKey1(),
                $RelatesToTableTwo ? $JoinTable->GetTable2() : $JoinTable->GetTable1(), 
                Relational\DependencyOrder::After, 
                Relational\DependencyOrder::Before);
        
        $this->JoinTable = $JoinTable;
        $this->RelatesToTableTwo = $RelatesToTableTwo;
        
        $this->ParentForeignKey = !$this->RelatesToTableTwo ? 
                $this->JoinTable->GetForeignKey2() : $this->JoinTable->GetForeignKey1();
        
    }

    /**
     * @return JoinTable
     */
    final public function GetJoinTable() {
        return $this->JoinTable;
    }
        
    /**
     * @return ForeignKey
     */
    final public function GetOtherForeignKey() {
        return $this->ParentForeignKey;
    }
    
    public function GetRelationalParentColumns() {
        return $this->ParentForeignKey->GetReferencedColumns();
    }

    public function AddConstraintToRequest(Relational\Request $Request) {
        $Request->AddTable($this->JoinTable);
        parent::AddConstraintToRequest($Request);
    }
    
    
    protected function ParentTable(ForeignKey $ForeignKey) { }
    protected function RelatedColumns(ForeignKey $ForeignKey) {
        return array();
    }
    public function AddParentPredicateToRequest(Relational\Request $Request, array $ParentRows) {
        if(count($ParentRows) > 1) {
            $Request->AddColumns($this->ParentForeignKey->GetParentColumns());
        }
        parent::AddParentPredicateToRequest($Request, $ParentRows);
    }
    
    protected function GroupRelatedRowsByParentKeys(array &$MappedRelatedRows, ForeignKey $ForeignKey, array $ParentRows, array $RelatedRows) {
        $ParentJoinTableKeys = $this->ParentForeignKey->GetParentColumns();
        $ParentReferencedKeys = $this->ParentForeignKey->GetReferencedColumns();
        
        $GroupedRelatedRows = $this->GroupRowsByColumnValues($RelatedRows, $ParentJoinTableKeys);
        $this->MapParentRowKeysToGroupedRelatedRows($MappedRelatedRows, $ParentRows, $ParentReferencedKeys, $GroupedRelatedRows);
    }

    protected function MapParentRowToRelatedKey(ForeignKey $ForeignKey, Relational\ResultRow $ParentRow) {
        $JoinTableKey = $this->ParentForeignKey->ParentKey();
        $this->ParentForeignKey->MapReferencedToParentKey($ParentRow, $JoinTableKey);
        
        return $JoinTableKey;
    }
    
    public function MapRelationalParentDataToRelatedData
            (Relational\ColumnData $ParentRow, Relational\ColumnData $RelatedRow) {
        
    }
    
    public function Persist(Relational\Transaction $Transaction, Relational\ResultRow $ParentData, array $RelationshipChanges) {
         /* @var $RelationshipChanges Relational\RelationshipChange[] */
        $JoinRowsWithoutPrimaryKeys = array();
        foreach($RelationshipChanges as $RelationshipChange) {
            if($RelationshipChange->HasPersistedRelationship()) {
                $JoinRow = $this->MapPersistedRelationshipToJoinRow($RelationshipChange->GetPersistedRelationship());
                $Transaction->Persist($JoinRow);
                if(!$JoinRow->HasPrimaryKey()) {
                    $JoinRowsWithoutPrimaryKeys[] = $JoinRow;
                }
            }
            if($RelationshipChange->HasDiscardedRelationship()) {
                $JoinRow = $this->MapDiscardedRelationshipToJoinRow($RelationshipChange->GetDiscardedRelationship());
                $Transaction->Discard($JoinRow->GetPrimaryKey());
            }
        }
        if(count($JoinRowsWithoutPrimaryKeys) > 0) {
            $ParentRow = $ParentData->GetRow($this->ParentForeignKey->GetReferencedTable());
            $Transaction->SubscribeToPrePersistEvent(
                    $this->JoinTable, 
                    function() use (&$ParentRow, &$JoinRowsWithoutPrimaryKeys) {
                        foreach($JoinRowsWithoutPrimaryKeys as $JoinRowWithoutPrimaryKey) {
                            $this->ParentForeignKey->MapReferencedToParentKey(
                                $ParentRow, 
                                $JoinRowWithoutPrimaryKey);
                        }
                    });
        }
    }
    protected function PersistIdentifyingRelationship(Relational\Transaction $Transaction, Relational\ResultRow $ParentRow, array $ChildRows) { }
    
    private function MapDiscardedRelationshipToJoinRow (Relational\DiscardedRelationship $Relationship) {
        return $this->MapRelationshipToJoinRow($Relationship);
    }
    private function MapPersistedRelationshipToJoinRow (Relational\PersistedRelationship $Relationship) {
        if($Relationship->IsIdentifying()) {
            throw new RelationException(
                    'JoinTableRelation cannot persist identifying relationships');
        }
        return $this->MapRelationshipToJoinRow($Relationship);        
    }
    
    private function MapRelationshipToJoinRow($Relationship) {
        if($this->RelatesToTableTwo) {
            return $this->JoinTable->JoinRow(
                    $Relationship->GetParentPrimaryKey(), 
                    $Relationship->GetRelatedPrimaryKey());
        }
        else {
            return $this->JoinTable->JoinRow(
                    $Relationship->GetRelatedPrimaryKey(), 
                    $Relationship->GetParentPrimaryKey());               
        }
    }
}

?>