<?php

namespace Storm\Drivers\Platforms\Development\Logging;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries;

class Connection implements Queries\IConnection {
    private $TimeSpentQuerying = 0.0;
    private $Logger;
    private $Connection;
    
    public function __construct(ILogger $Logger, Queries\IConnection $Connection) {
        $this->Logger = $Logger;
        $this->Connection = $Connection;
    }

    public function BeginTransaction() {
        $this->Logger->Log('Beginning transaction');
        $Start = microtime(true);
        $this->Connection->BeginTransaction();
        $this->TimeSpentQuerying += microtime(true) - $Start;
    }

    public function CommitTransaction() {
        $this->Logger->Log('Commiting transaction');
        $Start = microtime(true);
        $this->Connection->CommitTransaction();
        $this->TimeSpentQuerying += microtime(true) - $Start;
    }

    public function RollbackTransaction() {
        $this->Logger->Log('Rolling back transaction');
        $Start = microtime(true);
        $this->Connection->RollbackTransaction();
        $this->TimeSpentQuerying += microtime(true) - $Start;
    }

    public function Disconnect() {
        $Start = microtime(true);
        $this->Connection->Disconnect();
        $this->TimeSpentQuerying += microtime(true) - $Start;
    }

    public function Escape($Value, $ParameterType) {
        return $this->Connection->Escape($Value, $ParameterType);
    }

    public function Execute($QueryString, Queries\Bindings $Bindings = null) {
        $this->Logger->Log('Executing plain query: ' . $QueryString);
        $Start = microtime(true);
        $Query = $this->Connection->Execute($QueryString, $Bindings);
        $this->TimeSpentQuerying += microtime(true) - $Start;
        return $Query;
    }

    public function FetchValue($QueryString, Queries\Bindings $Bindings = null) {
        $this->Logger->Log('Fetching value with query: ' . $QueryString);
        $Start = microtime(true);
        $Query = $this->Connection->FetchValue($QueryString);
        $this->TimeSpentQuerying += microtime(true) - $Start;
        return $Query;
    }

    public function GetLastInsertIncrement() {
        $Start = microtime(true);
        $Increment = $this->Connection->GetLastInsertIncrement();
        $this->TimeSpentQuerying += microtime(true) - $Start;
        return $Increment;
    }

    public function IsInTransaction() {
        return $this->Connection->IsInTransaction();
    }
    
    public function Prepare($QueryString, Queries\Bindings $Bindings = null) {
        $Start = microtime(true);
        $Query = new Query($this->Logger, $this->Connection->Prepare($QueryString, $Bindings), $this->TimeSpentQuerying);
        $this->TimeSpentQuerying += microtime(true) - $Start;
        return $Query;
    }

    public function QueryBuilder(Queries\Bindings $Bindings = null) {
        $QueryBuilder = $this->Connection->QueryBuilder($Bindings);
        return new Queries\QueryBuilder(
                $this, 
                $QueryBuilder->GetParameterPlaceholder(), 
                $QueryBuilder->GetBindings(),
                $QueryBuilder->GetExpressionCompiler(),
                $QueryBuilder->GetCriterionCompiler(),
                $QueryBuilder->GetIdentifierEscaper());
    }
    
    public function GetTimeSpentQuerying() {
        return $this->TimeSpentQuerying;
    }
   
    public function SetIdentifierEscaper(Queries\IIdentifierEscaper $IdentifierEscaper) {
        return $this->Connection->SetIdentifierEscaper($IdentifierEscaper);
    }

    public function SetCriterionCompiler(Queries\ICriterionCompiler $RequestCompiler) {
        return $this->Connection->SetCriterionCompiler($RequestCompiler);
    }

    public function SetExpressionCompiler(Queries\IExpressionCompiler $ExpressionCompiler) {
        return $this->Connection->SetExpressionCompiler($ExpressionCompiler);
    }
}

?>