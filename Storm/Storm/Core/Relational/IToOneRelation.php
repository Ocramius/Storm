<?php

namespace Storm\Core\Relational;

/**
 * This interface represents a relation in which a parent row can relate to one or no rows.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IToOneRelation extends IRelation {
    const IToOneRelationType = __CLASS__;
    
    /**
     * Map the parent rows to their respective related rows
     * 
     * @param ResultRow[] $ParentRows The parent rows
     * @param ResultRow[] $RelatedRows The related rows
     * @return ResultRow[] The map containing the parent rows indexed with their parents key
     */
    public function MapParentKeysToRelatedRow(array $ParentRows, array $RelatedRows);
    
    /**
     * Sync the supplied relationship change by persisting to/discarding from the transaction
     * 
     * @param Transaction $Transaction The transaction to persist to
     * @param ResultRow $ParentData The parent result row
     * @param RelationshipChange $RelationshipChanges The mapped relationship change
     * @return void
     */
    public function Persist(Transaction $Transaction, ResultRow $ParentData, RelationshipChange $RelationshipChange);
}

?>