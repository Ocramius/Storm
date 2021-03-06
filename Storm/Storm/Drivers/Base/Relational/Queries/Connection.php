<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational;

abstract class Connection implements IConnection {
    /**
     * @var IIdentifierEscaper 
     */
    protected $IdentifierEscaper;
    /**
     * @var IExpressionCompiler 
     */
    protected $ExpressionCompiler;
    /**
     * @var ICriterionCompiler 
     */
    protected $CriterionCompiler;
    
    final public function SetExpressionCompiler(IExpressionCompiler $ExpressionCompiler) {
        $this->ExpressionCompiler = $ExpressionCompiler;
    }
        
    final public function SetIdentifierEscaper(IIdentifierEscaper $IdentifierEscaper) {
        $this->IdentifierEscaper = $IdentifierEscaper;
    }
    
    final public function SetCriterionCompiler(ICriterionCompiler $CriterionCompiler) {
        $this->CriterionCompiler = $CriterionCompiler;
    }
}

?>
