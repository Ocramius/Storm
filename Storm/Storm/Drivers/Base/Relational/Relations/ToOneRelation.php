<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Constraints\Predicate;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

class ToOneRelation extends Relation implements Relational\IToOneRelation {
    private $ForeignKey;
    
    public function __construct(ForeignKey $ForeignKey) {
        parent::__construct($ForeignKey->GetReferencedTable(), 
                Relational\DependencyOrder::After, Relational\DependencyOrder::Before);
        
        $this->ForeignKey = $ForeignKey;
    }
    
    /**
     * @return ForeignKey
     */
    public function GetForeignKey() {
        return $this->ForeignKey;
    }

    public function Persist(Relational\Transaction $Transaction, Relational\Row $Row, Relational\Row $RelatedRow) {
        $Transaction->Persist($RelatedRow);
    }

    public function Discard(Relational\Transaction $Transaction, 
            Relational\PrimaryKey $PrimaryKey) {
        $RelatedPrimaryKey = $this->GetTable()->Row();
        $this->ForeignKey->MapForeignKey($PrimaryKey, $RelatedPrimaryKey);
        
        $Table = $RelatedPrimaryKey->GetTable();
        
        $Request = new Relational\Request($Table, true);
        $Request->AddPredicate(Predicate::On($Table)
                        ->Matches($RelatedPrimaryKey));
        
        $Transaction->DiscardAll($Request);
    }
}

?>