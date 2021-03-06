<?php

 namespace Storm\Drivers\Base\Relational\Expressions;

use \Storm\Core\Relational\Expressions\Expression as CoreExpression;

class FunctionCallExpression extends Expression {
    private $Name;
    private $ArgumentValueListExpression;
    public function __construct($Name, ValueListExpression $ArgumentValueListExpression) {
        $this->Name = $Name;
        $this->ArgumentValueListExpression = $ArgumentValueListExpression;
    }
    
    public function GetName() {
        return $this->Name;
    }
    
    /**
     * @return ValueListExpression
     */
    public function GetArgumentValueListExpression() {
        return $this->ArgumentValueListExpression;
    }
}

?>