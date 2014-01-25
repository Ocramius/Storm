<?php

namespace Storm\Drivers\Base\Relational;

use \Storm\Core\Relational;

abstract class Database extends Relational\Database {
    /**
     * @var IPlatform
     */
    private $Platform;
    
    public function __construct(IPlatform $Platform) {
        //$this->Platform = $Platform;
        
        parent::__construct();
        
        //$this->Platform->Sync($this);
    }
    
    /**
     * @return IPlatform
     */
    final public function GetPlatform() {
        return $this->Platform;
    }
    
    private function VerifyPlatform() {
        if($this->Platform === null) {
            throw new \BadMethodCallException('Platform has not been supplied');
        }
    }
    
    /**
     * @return void
     */
    final public function SetPlatform(IPlatform $Platform) {
        $this->Platform = $Platform;
        $this->Initialize();
        $this->Platform->Sync($this);
    }
    
    final protected function LoadRows(Relational\Request $Request) {
        $this->VerifyPlatform();
        return $this->Platform->Select($Request);
    }
    
    final public function CommitTransaction(Relational\Transaction $Transaction) {
        $this->VerifyPlatform();
        return $this->Platform->Commit(
                $this->GetTablesOrderedByPersistingDependency(), 
                $this->GetTablesOrderedByDiscardingDependency(), 
                $Transaction);
    }
}

?>
